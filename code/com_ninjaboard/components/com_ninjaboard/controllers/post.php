<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
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
			'behaviors'     =>  array('verifiable'),
			'request' => array(
				'layout'	=> 'form'
			)
		));
	
		parent::__construct($config);

		$watch = $this->getService('com://site/ninjaboard.controller.watch');

		//Add/Edit related event handlers
		$this->registerCallback(array('after.add', 'after.edit'), array($this, 'setAttachments'));
		$this->registerCallback(array('after.add', 'after.edit'), array($watch, 'subscribe'));
		$this->registerCallback('after.add', array($watch, 'notify'));
		$this->registerCallback(array('after.add', 'after.edit'), array($this, 'redirect'));
		
		//Delete related event handlers
		$this->registerCallback('before.delete', array($this, 'interceptDelete'));
		$this->registerCallback('after.delete', array($this, 'cleanupDelete'));
		$this->registerCallback('after.delete', array($this, 'prevent404'));
		
		//Perhaps we need to prefill the text if quote state exists
		$this->registerCallback('before.read', array($this, 'canQuote'));
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

	public function setAttachments(KCommandContext $context)
	{
		$data			= $context['result'];
		$me				= $this->getService('com://admin/ninjaboard.model.people')->getMe();

		if(is_a($data, 'KDatabaseRowsetInterface')) $data = (object) end($data->getData());
		$err			= null;
		$errors			= array();
		$identifier		= $this->getIdentifier();
		$destination	= JPATH_ROOT.'/media/'.$identifier->type.'_'.$identifier->package.'/attachments/';
		$attachments	= array();
		
		require_once JPATH_ROOT.'/components/com_media/helpers/media.php';

		$files = KRequest::get('files.attachments.name', 'raw', array());
		
		if($files)
		{
			// Check Forum Attachment Settings
			$params			= $this->getService('com://admin/ninjaboard.model.settings')->getParams();
			if(!$params['attachment_settings']['enable_attachments']){
				JError::raiseWarning(21, JText::_('COM_NINJABOARD_ATTACHMENTS_HAVE_BEEN_DISABLED_ON_THIS_FORUM'));
				$this->execute('cancel');
				return false;	
			}
			
			// Check User Attachment Permissions
			$row = $this->getModel()->getItem();
			$topic = $this->getService('com://site/ninjaboard.model.topics')
																		->id($row->ninjaboard_topic_id)
																		->getItem();
			$forum = $this->getService('com://site/ninjaboard.model.forums')
																		->id($topic->forum_id)
																		->getItem();
			
			if($forum->attachment_permissions < 2){
				JError::raiseWarning(21, JText::_('COM_NINJABOARD_YOU_DONT_HAVE_THE_PERMISSIONS_TO_USE_ATTACHMENTS_IN_THIS_FORUM'));
				$this->execute('cancel');
				return false;
			}
		}
		
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
			$this->getService('com://site/ninjaboard.model.attachments')
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
			JError::raiseWarning(21, sprintf(JText::_('COM_NINJABOARD_COULDNT_UPLOAD_BECAUSE'), $error['name'], lcfirst($error['error'])));
		}
		
		foreach (KRequest::get('post.attachments', 'int', array()) as $attachment)
		{
			$item = $this->getService('com://site/ninjaboard.model.attachments')
					->id($attachment)
					->getItem();

			if(JFile::exists($destination.$item->file)) JFile::delete($destination.$item->file);
			$item->delete();
		}		
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
	public function interceptDelete()
	{
		$user = $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$rows = $this->getModel()->getList();
		foreach($rows as $row)
		{
			$topic = $this->getService('com://site/ninjaboard.model.topics')
																		->id($row->ninjaboard_topic_id)
																		->getItem();
			$forum = $this->getService('com://site/ninjaboard.model.forums')
																		->id($topic->forum_id)
																		->getItem();

			// @TODO we migth want to add an option later, wether or not to allow users to delete their own post.
			if($forum->post_permissions < 3 && $row->created_by != $user->id) {
				JError::raiseError(403, JText::_('COM_NINJABOARD_YOU_DONT_HAVE_THE_PERMISSIONS_TO_DELETE_OTHERS_TOPICS'));
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
		$rows  = $context->result;
		$table = $this->getService('com://site/ninjaboard.database.table.posts');

		// make sure we are not a row
		if ($rows instanceof KDatabaseRowDefault) $rows = array($rows);
	
		foreach ($rows as $row)
		{
			$topic	= $this->getService('com://site/ninjaboard.model.topics')->id($row->ninjaboard_topic_id)->getItem();
			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()->where('ninjaboard_topic_id', '=', $topic->id);
			$posts = $table->count($query);

			if($posts)
			{
				//Replies does not count the first post, thus we subtract by 1
				$topic->replies = $posts - 1;
				
				// @TODO merge into one query
				$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
																->select('ninjaboard_post_id')
																->where('ninjaboard_topic_id', '=', $topic->id)
																->order('created_time', 'desc');
				$topic->last_post_id = $table->select($query, KDatabase::FETCH_FIELD);
				
				$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
																->select('created_time')
																->where('ninjaboard_topic_id', '=', $topic->id)
																->order('created_time', 'desc');
				$topic->last_post_on = $table->select($query, KDatabase::FETCH_FIELD);
				
				$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
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
			$forums	= $this->getService('com://site/ninjaboard.model.forums')->limit(0)->id($topic->forum_id)->getListWithParents();
			$forums->recount();

			if($row->created_by)
			{
				$user	= $this->getService('com://site/ninjaboard.model.people')->id($row->created_by)->getItem();
				if(!$user->guest)
				{
					$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()->where('created_user_id', '=', $user->id);
					$user->posts = $table->count($query);

					$user->save();
				}
			}
		}
	}
	
	/**
	 * Workaround for ajax delete action 404ing due to actionForward not working correctly
	 * @see http://nooku.assembla.com/spaces/nooku-framework/tickets/206-ajax-delete-gives-a-404-error
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	public function prevent404(KCommandContext $context)
	{
	    if ($this->getView()->getFormat() == 'json' && $context->result->getStatus() == KDatabase::STATUS_DELETED) { 
			$context->result->setStatus(KDatabase::STATUS_DELETED); 
		} 
	}
	
	/*
	 * CaptainHook's quoting "hack"
	 * @TODO make into reusable controller behavior, we need this for private messages
	 */
	public function canQuote(KCommandContext $context)
	{
	    // Cloned so that the item we get doesn't get passed elsewhere
	    $quoting = $this->getModel()->getState()->quote;
	    if(!$quoting) return;

        $model = clone $this->getModel();
	    $quote = $model->id($quoting)->getItem();

	    // Set the text on our item
	    $this->getModel()->getItem()->set('text', '[quote="'.htmlspecialchars($quote->display_name).'"]'.$quote->text.'[/quote]');
	}

	/**
	 * Redirect the user after add/edit
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	public function redirect(KCommandContext $context)
	{
		$row = $this->getModel()->getItem();
		$app = JFactory::getApplication();

		// for some reason $this->setRedirect is ignored on the quote view so do it with joomla
		if($row->ninjaboard_topic_id && $row->id) {
			$append = $this->_redirect_hash ? '#p'.$row->id : '';
			$app->redirect('index.php?option=com_ninjaboard&view=topic&id='.$row->ninjaboard_topic_id.'&post='.$row->id.$append);
		} elseif($row->ninjaboard_topic_id){
			$app->redirect('index.php?option=com_ninjaboard&view=topic&id='.$row->ninjaboard_topic_id);
		}
	}
}