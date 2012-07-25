<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Watch Controller
 *
 * Takes care of managing Email Updates, and sending them.
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerWatch extends ComNinjaboardControllerAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		//To prevent these states to be set while browsing after a delete/unwatch
		$config->append(array(
			'request' => array(
				'sort' => 'created_on',
				'direction' => 'desc'
			)
		));
		$config->persistent = false;

		parent::__construct($config);

		$this->registerCallback('before.add', array($this, 'setUser'));
		$this->registerCallback('before.edit', array($this, 'setUser'));
		$this->registerCallback('before.delete', array($this, 'beforeDelete'));

		//Set default redirect
		$this->_redirect = KRequest::referrer();
	}

	/**
	 * Makes sure the user id is set and correct
	 *
	 * @return boolean false when no user id, to stop the chain
	 */
	public function setUser(KCommandContext $context)
	{
		$me = $this->getService('com://admin/ninjaboard.model.people')->getMe();
		if(!$me->id) return false;

		foreach(array('subscription_type', 'subscription_type_id') as $required)
		{
			if(!isset($context->data->$required)) return false;
		}
	}

	/**
	 * Subscribe to a topic when editing, posting or replying
	 *
	 * @param	KCommandContext	The context of the event
	 * @return	void
	 */
	public function subscribe(KCommandContext $context)
	{
		$data = $context->result;
		$me   = $this->getService('com://admin/ninjaboard.model.people')->getMe();

		if ($data->notify_on_reply_topic && $data->ninjaboard_topic_id) {
		
			$table 	= $this->getModel()->getTable();
			$type  	= $table->getTypeIdFromName('topic');
			$row	= $table->getRow()->setData(array('type' => $type, 'type_id' => $data->ninjaboard_topic_id, 'created_by' => $me->id));

			// only save a subcription if 
			if (!$row->load())
				$this->execute('add', array('subscription_type'	=> $type, 'subscription_type_id' => $data->ninjaboard_topic_id));
		}
	}
	
	/**
	 * Checks if we can delete or not
	 *
	 * @return boolean  false if no user id, to stop the chain
	 */
	public function beforeDelete(KCommandContext $context)
	{
		$me = $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$id = isset($this->getRequest()->id) ? $this->getRequest()->id : null;
		
		if(!$id && isset($this->getRequest()->type_id)) $id = $this->getRequest()->type_id; 

		if($id === null) return false;

		//Makes sure the user do not delete other peoples subscriptions
		$this->getModel()->by($me->id);
		
		//Set the redirect
		$this->_redirect = 'index.php?option=com_ninjaboard&view=watches';
	}

	/**
	 * Send out notification emails to subscribers
	 */
	public function notify(KCommandContext $context)
	{
		$post	= $context->result;
		$params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		//Only notify users if we have an id and we notifications are enabled
		if ($params['email_notification_settings']['enable_email_notification'] && $post->id) {

			$recipients	= $this->getModel()->topic($post->ninjaboard_topic_id)->getRecipients();
			$topic		= $this->getService('com://admin/ninjaboard.model.topics')->id($post->ninjaboard_topic_id)->getItem();
			$forum		= $this->getService('com://site/ninjaboard.model.forums')->id($topic->forum_id)->getItem();
			$me			= $this->getService('com://admin/ninjaboard.model.people')->getMe();
			$app		= JFactory::getApplication();

			$root		= KRequest::url()->get(KHttpUrl::BASE ^ KHttpUrl::PATH);
			$link		= $root.JRoute::_('index.php?option=com_ninjaboard&view=topic&id='.$post->ninjaboard_topic_id.'&post='.$post->id).'#p'.$post->id;
			$watches	= $root.JRoute::_('index.php?option=com_ninjaboard&view=watches');

			$sitename 	= $app->getCfg( 'sitename' );
			$mailfrom 	= $app->getCfg( 'mailfrom' );
			$fromname 	= $app->getCfg( 'fromname' );

			if ( ! $mailfrom  || ! $fromname ) {
				$fromname = $me->display_name;
				$mailfrom = $me->email;
			}

			// Send notification to all administrators
			//$subject = sprintf ( JText::_( 'New post by %s in "%s"' ), $me->display_name, $topic->subject);
			$subject = $topic->subject.' ['.$forum->title.']';
			$subject = html_entity_decode($subject, ENT_QUOTES);
			
			$text = $this->getService('ninja:helper.bbcode')->parse(array('text' => $post->text));

			foreach ($recipients as $row )
			{
				if($params->email_notification_settings->include_post == 'yes') {
					$message = str_replace('/n', "\n", JText::_( 'NOTIFY_USER_INCLUDING_POST' ));
					if($message == 'NOTIFY_USER_INCLUDING_POST') $message = "Hello %s,\n\nA new message was posted in the thread \"%s\" by %s:\n\n<%s>\n\nMessage:\n%s\n\n\nTo edit your Email Updates, go to %s\n\n- %s";
					$message = sprintf ($message , $row->name, $topic->subject, $me->display_name, $link, $this->getService('koowa:filter.string')->sanitize($text), $watches, $fromname);
					$message = html_entity_decode($message, ENT_QUOTES);
					JUtility::sendMail($mailfrom, $fromname, $row->email, $subject, $message);
				} else {
					$message = str_replace('/n', "\n", JText::_( 'NOTIFY_USER' ));
					if($message == 'NOTIFY_USER') $message = "Hello %s,\n\nA new message was posted in the thread \"%s\" by %s:\n\n<%s>\n\n\n\nTo edit your Email Updates, go to %s\n\n- %s";
					$message = sprintf ($message , $row->name, $topic->subject, $me->display_name, $link, $watches, $fromname);
					$message = html_entity_decode($message, ENT_QUOTES);
					JUtility::sendMail($mailfrom, $fromname, $row->email, $subject, $message);
				}
				
			}
		}
	}
}