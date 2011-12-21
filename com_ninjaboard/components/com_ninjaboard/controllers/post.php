<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: post.php 2306 2011-07-28 13:49:38Z captainhook $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Post Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerPost extends ComNinjaboardControllerAbstract
{
	/**
	 * Boolean used to decide wether or not to append #p[post id] to the redirect
	 * They case the page to scroll, and when a failed upload happened
	 * the user needs to see that message
	 *
	 * @var boolean
	 */
	protected $_redirect_hash = true;

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$config->append(array(
			'request' => array(
				'layout'	=> 'form'
			)
		));
	
		parent::__construct($config);

		//Register validation event
		//@TODO we shouldn't have to attach to the save and apply events. But KControllerView expects 'edit' to succeed.
		$this->registerCallback(array('before.add', 'before.edit', 'before.save', 'before.apply'), array($this, 'validate'));

		$this->registerCallback(array('after.add', 'after.edit'), array($this, 'setNotify'));
		$this->registerCallback(array('after.add', 'after.edit'), array($this, 'setAttachments'));
		$this->registerCallback('after.add', array($this, 'notify'));
		
		//Delete related event handlers
		$this->registerCallback('before.delete', array($this, 'canDelete'));
		$this->registerCallback('after.delete', array($this, 'cleanupDelete'));
		
		// Workaround for avoiding 404 status on editor preview ajax
		// @TODO replace MarkItUp with a wysiwyg editor so that ajax previews are no longer necessary.
		$this->registerCallback('after.read', array($this, 'prevent404'));
	}

	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'persistent'	=> false,
        ));

        parent::_initialize($config);
    }

	/**
	 * Validates the entered data
	 *
	 * @param	KCommandContext	The context of the event
	 * @return	boolean			True when valid, false when invalid to stop the action from executing
	 */
	public function validate(KCommandContext $context)
	{
		$data 	 = $context->data;
		$request = $this->getRequest();
		
		//If there's a topic id, then we're replying or editing
		if(!empty($data->ninjaboard_topic_id) && empty($data->subject)) {
			$topic = KFactory::tmp('site::com.ninjaboard.model.topics')
																		->id($data->ninjaboard_topic_id)
																		->getItem();
			//If the id is set, then we're editing
			if(isset($request->id)) {
				//If this post is the topic starter, it can't be without a subject or body
				if($topic->first_post_id === $request->id) {
					JError::raiseWarning(21, JText::_('Topics cannot be without a subject.'));
					$this->execute('cancel');
					return false;
				} 
			}
		} elseif(empty($data->subject)) {
			JError::raiseWarning(21, JText::_('Topics cannot be without a subject.'));
			$this->execute('cancel');
			return false;
		}
		
		if(empty($data->text)) {
			JError::raiseWarning(21, JText::_('Posts cannot be without text.'));
			$this->execute('cancel');
			return false;
		}
	}
	
	/**
	 * Set email notifications status when editing, posting or replying
	 */
	public function setNotify(KCommandContext $context)
	{
		$data = $context->result;
		if(is_a($data, 'KDatabaseRowsetInterface')) $data = (object) end($data->getData());

		if(!isset($data->notify_on_reply_topic) || !$data->ninjaboard_topic_id) return;
		
		$table = KFactory::get('admin::com.ninjaboard.database.table.watches');
		$type  = $table->getTypeIdFromName('topic');
		
		//Always run delete to clean out duplicates
		//@TODO make this lazier
		KFactory::get('site::com.ninjaboard.controller.watch')
															->type($type)
															->type_id($data->ninjaboard_topic_id)
															->execute('delete');
		
		if($data->notify_on_reply_topic)
		{
			KFactory::get('site::com.ninjaboard.controller.watch')->execute('add', array(
				'subscription_type'		=> $type,
				'subscription_type_id'	=> $data->ninjaboard_topic_id
			));
		}
	}
	
	/**
	 * Email notifications
	 */
	public function notify(KCommandContext $context)
	{
		//If no id, do not notify
		if(!$context->result->id) return;
		
		$params = KFactory::get('admin::com.ninjaboard.model.settings')->getParams();
		if($params['email_notification_settings']['enable_email_notification'])
		{
			KFactory::get('site::com.ninjaboard.controller.watch')->execute('notify', $context);
		}
	}

	public function setAttachments(KCommandContext $context)
	{
		// Check Forum Attachment Settings
		$params			= KFactory::get('admin::com.ninjaboard.model.settings')->getParams();
		if(!$params['attachment_settings']['enable_attachments']){
			JError::raiseWarning(21, JText::_('Attachments have been disabled on this forum.'));
			$this->execute('cancel');
			return false;	
		}
		
		// Check User Attachment Permissions
		$row = $this->getModel()->getItem();
		$topic = KFactory::tmp('site::com.ninjaboard.model.topics')
																	->id($row->ninjaboard_topic_id)
																	->getItem();
		$forum = KFactory::tmp('site::com.ninjaboard.model.forums')
																	->id($topic->forum_id)
																	->getItem();
																	
		if($forum->attachment_permissions < 2){
			JError::raiseWarning(21, JText::_("You don't have the permissions to use Attachments in this forum."));
			$this->execute('cancel');
			return false;
		}
		
		$data			= $context['result'];
		$me				= KFactory::get('admin::com.ninjaboard.model.people')->getMe();

		if(is_a($data, 'KDatabaseRowsetInterface')) $data = (object) end($data->getData());
		$err			= null;
		$errors			= array();
		$identifier		= $this->getIdentifier();
		$destination	= JPATH_ROOT.'/media/'.$identifier->type.'_'.$identifier->package.'/attachments/';
		$attachments	= array();
		
		require_once JPATH_ROOT.'/components/com_media/helpers/media.php';

		$files = KRequest::get('files.attachments.name', 'raw', array());
		foreach ($files as $i => $file)
		{
		    //If no name is set, then we can't upload
		    if(!trim($file)) continue;
		
			foreach (KRequest::get('files.attachments', 'raw') as $key => $values)
			{
				$attachment[$key] = KRequest::get('files.attachments.'.$key.'.'.$i, 'raw');
			}
			if(MediaHelper::canUpload($attachment, $err)) $attachments[] = $attachment;
			else $errors[] = array_merge($attachment, array('error' => $err));
		}
		
		foreach ($attachments as $attachment)
		{
			$upload = JFile::makeSafe(uniqid(time())).'.'.JFile::getExt($attachment['name']);

			JFile::upload($attachment['tmp_name'], $destination.$upload);
			KFactory::tmp('site::com.ninjaboard.model.attachments')
				->post($data->id)
				->getItem()
				->setData(array(
					'post' => $data->id, 
					'file' => $upload, 
					'name' => $attachment['name'],
					'joomla_user_id' => $me->id
				))
				->save();
		}
		
		//Makes sure the page don't scroll after redirect when there are errors
		if($errors) $this->_redirect_hash = false;
		
		foreach ($errors as $error)
		{
			JError::raiseWarning(21, sprintf(JText::_("%s couldn't upload because %s"), $error['name'], lcfirst($error['error'])));
		}
		
		foreach (KRequest::get('post.attachments', 'int', array()) as $attachment)
		{
			$item = KFactory::tmp('site::com.ninjaboard.model.attachments')
					->id($attachment)
					->getItem();

			if(JFile::exists($destination.$item->file)) JFile::delete($destination.$item->file);
			$item->delete();
		}		
	}

	protected function _actionSave(KCommandContext $context)
	{
		$result = parent::_actionSave($context);

		$row = $this->getModel()->getItem();

		if($row->ninjaboard_topic_id && $row->id)
		{
			$append = $this->_redirect_hash ? '#p'.$row->id : '';
			$this->setRedirect('index.php?option=com_ninjaboard&view=topic&id='.$row->ninjaboard_topic_id.'&post='.$row->id.$append);
		}
		elseif($row->ninjaboard_topic_id)
		{
			$this->setRedirect('index.php?option=com_ninjaboard&view=topic&id='.$row->ninjaboard_topic_id);
		}

		return $result;
	}
	
	/*
	 * Generic cancel action
	 *
	 * @return 	void
	 */
	protected function _actionCancel(KCommandContext $context)
	{
		$post	= $this->getModel()->getItem();

		$isset	= array(
			'post'	=> $post->id,
			'topic' => isset($this->_request->topic) && $this->_request->topic,
			'forum'	=> isset($this->_request->forum) && $this->_request->forum
		);

		if($isset['post']) {
			$append = $this->_redirect_hash ? '#p'.$post->id : '';
			$this->_redirect = 'index.php?option=com_ninjaboard&view=topic&id='.$post->ninjaboard_topic_id.'&post='.$post->id.$append;
			
		} elseif($isset['topic']) {
			$this->_redirect = 'index.php?option=com_ninjaboard&view=topic&id='.$this->_request->topic;
		} elseif($isset['forum']) {
			$this->_redirect = 'index.php?option=com_ninjaboard&view=forum&id='.$this->_request->forum;
		} else {
			parent::_actionCancel($context);
		}
		
		return $post;
	}

	/**
	 * Checks wether the we can delete this post or not
	 *
	 * @TODO	currently if one post fails the permission check, 
	 *			the command chain is stopped by returning false.
	 *			Change it so posts that can be deleted, are deleted.
	 *
	 * @return boolean	returns false if permission check fails
	 */
	public function canDelete()
	{
		$user = KFactory::get('admin::com.ninjaboard.model.people')->getMe();
		$rows = $this->getModel()->getList();
		foreach($rows as $row)
		{
			$topic = KFactory::tmp('site::com.ninjaboard.model.topics')
																		->id($row->ninjaboard_topic_id)
																		->getItem();
			$forum = KFactory::tmp('site::com.ninjaboard.model.forums')
																		->id($topic->forum_id)
																		->getItem();

			// @TODO we migth want to add an option later, wether or not to allow users to delete their own post.
			if($forum->post_permissions < 3 && $row->created_by != $user->id) {
				JError::raiseError(403, JText::_("You don't have the permissions to delete others topics."));
				return false;
			}
		}
	}

	/**
	 * Updating forums, topics and users when a post successfully deleted.
	 *
	 * @param $param
	 */
	public function cleanupDelete(KCommandContext $context)
	{
		$rows = $context->result;
		$table = KFactory::get('site::com.ninjaboard.database.table.posts');
		
		foreach($rows as $row)
		{
			$topic	= KFactory::tmp('site::com.ninjaboard.model.topics')->id($row->ninjaboard_topic_id)->getItem();
			$query = KFactory::tmp('lib.koowa.database.query')->where('ninjaboard_topic_id', '=', $topic->id);
			$posts = $table->count($query);
		
			if($posts)
			{
				//Replies does not count the first post, thus we subtract by 1
				$topic->replies = $posts - 1;
				
				// @TODO merge into one query
				$query = KFactory::tmp('lib.koowa.database.query')
																->select('ninjaboard_post_id')
																->where('ninjaboard_topic_id', '=', $topic->id)
																->order('created_time', 'desc');
				$topic->last_post_id = $table->select($query, KDatabase::FETCH_FIELD);
				
				$query = KFactory::tmp('lib.koowa.database.query')
																->select('created_time')
																->where('ninjaboard_topic_id', '=', $topic->id)
																->order('created_time', 'desc');
				$topic->last_post_on = $table->select($query, KDatabase::FETCH_FIELD);
				
				$query = KFactory::tmp('lib.koowa.database.query')
																->select('created_user_id')
																->where('ninjaboard_topic_id', '=', $topic->id)
																->order('created_time', 'desc');
				$topic->last_post_by = $table->select($query, KDatabase::FETCH_FIELD);

				$topic->save();
			}
			
			if($topic->first_post_id == $row->id)
			{
				//If this is the last post, and only post in the topic, delete the topic.
				//If not, then don't delete it
				if(!$posts) $topic->delete();
				else return false;
			}

			//Update the forums' topics and posts count, and correct the last_post_id column
			$forums	= KFactory::tmp('site::com.ninjaboard.model.forums')->limit(0)->id($topic->forum_id)->getListWithParents();
			$forums->recount();

			if($row->created_by)
			{
				$user	= KFactory::tmp('site::com.ninjaboard.model.people')->id($row->created_by)->getItem();
				if(!$user->guest)
				{
					$query = KFactory::tmp('lib.koowa.database.query')->where('created_user_id', '=', $user->id);
					$user->posts = $table->count($query);

					$user->save();
				}
			}
		}
	}
	
	/**
	 * Workaround for preview ajax requests that will fail if the status code is 404
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	public function prevent404(KCommandContext $context)
	{
	    if($this->_request->layout == 'preview' || $this->_request->topic) $context->status = KHttpResponse::OK;
	}
}