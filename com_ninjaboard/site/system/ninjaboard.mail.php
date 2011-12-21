<?php
/**
 * @version $Id: ninjaboard.mail.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Mail
 *
 * @package Ninjaboard
 */
class NinjaboardMail
{
	/**
	 * mail from
	 * @var string
	 */
	var $mailFrom = null;
	
	/**
	 * from name
	 * @var string
	 */
	var $fromName = null;
	
	/**
	 * mail to
	 * @var string
	 */
	var $mailTo = null;
	
	/**
	 * subject
	 * @var string
	 */
	var $subject = null;
	
	/**
	 * message
	 * @var string
	 */
	var $message = null;
			
	/**
	 * site name
	 * @var string
	 */
	var $siteName = null;
			
	/**
	 * site url
	 * @var string
	 */
	var $siteURL = null;
			
	/**
	 * ninjaboard mail
	 */	
	function NinjaboardMail() {
		global $mainframe;
		
		$this->mailFrom = $mainframe->getCfg('mailfrom');
		$this->fromName = $mainframe->getCfg('fromname');
		$this->siteName = $mainframe->getCfg('sitename');
		$this->siteURL = JURI::base();
	}
	
	/**
	 * get instance
	 *
	 * @access public
	 * @return object
	 */
	function &getInstance() {
	
		static $ninjaboardMail;

		if (!is_object($ninjaboardMail)) {
			$ninjaboardMail = new NinjaboardMail();
		}

		return $ninjaboardMail;
	}

	function sendRegistrationMail(&$ninjaboardUser) {

		// initialize variables
		$db				=& JFactory::getDBO();		
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		
		$name = $ninjaboardUser->get('name');
		$userName = $ninjaboardUser->get('username');
		$password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); // disallow control chars in the email

		$this->mailTo = $ninjaboardUser->get('email');
		$this->subject = sprintf(JText::_('NB_ACCOUNTACTIVATIONSUBJECTUSER'), $this->siteName);
		
		switch($ninjaboardConfig->getBoardSettings('account_activation')) {
			case 0:		// no activation needed
				$this->message = sprintf(JText::_('NB_ACCOUNTACTIVATIONNO'), $name, $this->siteName, $this->siteURL, $userName, $password);
				$this->_sendMail();
				break;
			case 1:		// activation by user
				$ninjaboardUser->setActivation();
				
				$activationLink = $this->siteURL."index.php?option=com_ninjaboard&task=ninjaboardactivateprofile&activation=".$ninjaboardUser->get('activation');
				$this->message = sprintf(JText::_('NB_ACCOUNTACTIVATIONUSER'), $name, $this->siteName, $activationLink, $this->siteURL, $userName, $password);
				$this->_sendMail();
				break;
			case 2:		// activation by admin
				$ninjaboardUser->setActivation();
							
				// handle user email
				$this->message = sprintf(JText::_('NB_ACCOUNTACTIVATIONADMINUSER'), $name, $this->siteName, $this->siteURL, $userName, $password);
				$this->_sendMail();
				
				// handle system emails
				$query = "SELECT u.name, u.email"
						. "\n FROM #__users AS u"
						. "\n INNER JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
						. "\n WHERE ju.system_emails = 1";
				$db->setQuery($query);
				$rows = $db->loadObjectList();				
				
				$activationLink = $this->siteURL."index.php?option=com_ninjaboard&task=ninjaboardactivateprofile&activation=".$ninjaboardUser->get('activation');
				$this->subject = sprintf(JText::_('NB_ACCOUNTACTIVATIONSUBJECTADMIN'), $this->siteName, $this->userName);

				foreach ($rows as $row) {
					$this->message = sprintf(JText::_('NB_ACCOUNTACTIVATIONADMINADMIN'), $row->name, $userName, $this->siteURL, $activationLink);
					$this->mailTo = $row->email;
					$this->_sendMail();
				}
				break;
		}
						
	}
	
	function sendConfirmationMail(&$ninjaboardUser) {

		$this->mailTo = $ninjaboardUser->get('email');
		$this->subject = sprintf(JText::_('NB_CONFIRMATIONSUBJECT'), $this->siteName);
		$this->message = sprintf(JText::_('NB_CONFIRMATIONMESSAGE'), $ninjaboardUser->get('name'), $this->siteURL, $this->siteName);

		$this->_sendMail();			
	}
	
	function sendRequestLoginMail(&$ninjaboardUser) {

		// initialize variables
		$Itemid	= JRequest::getVar('Itemid');

		$this->mailTo = $ninjaboardUser->get('email');
		$this->subject = sprintf(JText::_('NB_LOGINREQUESTSUBJECT'), $this->siteName);
		$resetLoginLink = JRoute::_($this->siteURL.'index.php?option=com_ninjaboard&view=resetlogin&activation='.$ninjaboardUser->get('activation').'&Itemid='.$Itemid);
		$this->message = sprintf(JText::_('NB_LOGINREQUESTMESSAGE'), $ninjaboardUser->get('name'), $resetLoginLink, $this->siteName);

		$this->_sendMail();
		
		return true;
	}
	
	function sendNotifyOnReplyMail($topicId) {
		
		// initialize variables
		$db			=& JFactory::getDBO();
		$ninjaboardUser	=& NinjaboardHelper::getNinjaboardUser();
		$Itemid		= JRequest::getVar('Itemid');
		
		$this->subject = sprintf(JText::_('NB_NOTIFYONREPLYSUBJECT'), $this->siteName);
		
		$topicLink = JRoute::_($this->siteURL.'index.php?option=com_ninjaboard&view=topic&topic='.$topicId.'&Itemid='.$Itemid);

		// get all notification requests
		$query = "SELECT *"
				. "\n FROM #__ninjaboard_posts AS p"
				. "\n INNER JOIN #__users AS u ON u.id = p.id_user"
				. "\n WHERE p.id_topic = $topicId"
				. "\n AND p.notify_on_reply = 1"
				. "\n AND u.id <> ".$ninjaboardUser->get('id')
				. "\n GROUP BY u.id"
				;
		$db->setQuery($query);
		$notifies = $db->loadObjectList();
	
		foreach ($notifies as $notify) {
			$this->mailTo = $notify->email;
			$this->message = sprintf(JText::_('NB_NOTIFYONREPLYMESSAGE'), $notify->name, $notify->subject, $topicLink, $this->siteName);
			$this->_sendMail();
		}
	}
	
	function sendReportPostMail($postId, $reportComment) {
		
		// initialize variables
		$db			=& JFactory::getDBO();
		$ninjaboardUser	=& NinjaboardHelper::getNinjaboardUser();
		$Itemid		= JRequest::getVar('Itemid');
		
		$this->subject = sprintf(JText::_('NB_REPORTPOSTSUBJECT'), $this->siteName);

		$row =& JTable::getInstance('NinjaboardPost');
		if (!$row->load($postId)) {
			return;
		}
		
		$topicLink = JRoute::_($this->siteURL.'index.php?option=com_ninjaboard&view=topic&topic='.$row->topicId.'&Itemid='.$Itemid);
		
		// get all system message users
		$query = "SELECT *"
				. "\n FROM #__users AS u"
				. "\n INNER JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
				. "\n WHERE ju.system_emails = 1"
				. "\n GROUP BY u.id"
				;
		$db->setQuery($query);
		$systemUsers = $db->loadObjectList();
		
		foreach ($systemUsers as $systemUser) {
			$this->mailTo = $systemUser->email;
			$this->message = sprintf(JText::_('NB_REPORTPOSTMESSAGE'), $systemUser->name, $reportComment, $topicLink, $this->siteName);
			$this->_sendMail();
		}
		
	}
	
	function _sendMail() {
		JUtility::sendMail($this->mailFrom, $this->fromName, $this->mailTo, $this->subject, $this->message);
	}
}
?>