<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: watch.php 2470 2011-11-01 14:22:28Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
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
	 * Checks if we can delete or not
	 *
	 * @return boolean  false if no user id, to stop the chain
	 */
	public function beforeDelete(KCommandContext $context)
	{
		$me = $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$id = isset($this->getRequest()->id) ? $this->getRequest()->id : null;
		
		if(!$id && isset($this->getRequest()->type_id)) $id = $this->getRequest()->type_id; 

		if(!$me->id || $id === null) return false;

		//Makes sure the user do not delete other peoples subscriptions
		$this->getModel()->by($me->id);
		
		//Set the redirect
		$this->_redirect = 'index.php?option=com_ninjaboard&view=watches';
	}

	/**
	 * Email notifications
	 */
	public function _actionNotify(KCommandContext $context)
	{
		$post		= $context->result;
		$recipients	= $this->getService($this->getModel())->topic($post->ninjaboard_topic_id)->getRecipients();
		$topic		= $this->getService('com://admin/ninjaboard.model.topics')->id($post->ninjaboard_topic_id)->getItem();
		$forum		= $this->getService('com://site/ninjaboard.model.forums')->id($topic->forum_id)->getItem();
		$me			= $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$app		= JFactory::getApplication();
		$params		= $this->getService('com://admin/ninjaboard.model.settings')->getParams();

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