<?php
/**
 * @version $Id: ninjaboard.messagequeue.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Message Queue
 *
 * @package Ninjaboard
 */
class NinjaboardMessageQueue
{
	/**
	 * messege data
	 *
	 * @var array
	 */
	var $_messages = array();

	function NinjaboardMessageQueue() {	 
	}
	
	/**
	 * get instance
	 *
	 * @access	public
	 * @return	object
	 */
	function &getInstance() {
	
		static $ninjaboardMessageQueue;

		if (!is_object($ninjaboardMessageQueue)) {
			$ninjaboardMessageQueue = new NinjaboardMessageQueue();
		}

		return $ninjaboardMessageQueue;
	}
		
	function addMessage($msg, $type='info') {
		$message->message = $msg;
		$message->type = $type;
		$this->_messages[] = $message;
		
		$session =& JFactory::getSession();
		$session->set('ninjaboardMessage', $this->_messages);
	}
			
	function getMessages() {
		$session =& JFactory::getSession();
		$this->_messages = $session->get('ninjaboardMessage');
		$session->set('ninjaboardMessage', null);	
		return $this->_messages;
	}
						
}
?>