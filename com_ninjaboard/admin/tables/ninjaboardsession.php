<?php
/**
 * @version $Id: ninjaboardsession.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

defined('_JEXEC') or die();

/**
 * Ninjaboard Session Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardSession extends JTable
{
	/** @var int Primary key */
	var $session_id		= null;
	/** @var string */
	var $action_time	= null;
	/** @var string */
	var $id_user		= null;
	/** @var string */
	var $current_action	= null;
	/** @var string */
	var $action_url		= null;

	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_session', 'session_id', $db);
	}

	function insert($sessionId) {
		$this->session_id	= $sessionId;

		$this->action_time = time();
		$this->current_action = NinjaboardHelper::getAction();

		$uri = JURI::getInstance();
		$this->action_url = $uri->toString();
		
		$ninjaboardUser =& NinjaboardHelper::getNinjaboardUser();
		
		// if user do not want to show his online state, we just tarn him as a guest
		if ($ninjaboardUser->get('id') && $ninjaboardUser->get('show_online_state')) {
			$this->id_user = $ninjaboardUser->get('id');		
		} else {
			$this->id_user = 0;
		}

		$ret = $this->_db->insertObject($this->_tbl, $this, 'session_id');

		if(!$ret) {
			$this->_error = strtolower(get_class($this))."::". JText::_('store failed') ."<br />" . $this->_db->stderr();
			return false;
		} else {
			return true;
		}
	}

	function update($updateNulls = false) {
		$this->action_time = time();
		$this->current_action = NinjaboardHelper::getAction();
		
		$uri = JURI::getInstance();
		$this->action_url = $uri->toString();

		$ninjaboardUser =& NinjaboardHelper::getNinjaboardUser();
		
		if ($ninjaboardUser->get('id') && $ninjaboardUser->get('show_online_state')) {
			$this->id_user = $ninjaboardUser->get('id');		
		} else {
			$this->id_user = 0;
		}
		
		$ret = $this->_db->updateObject($this->_tbl, $this, 'session_id', $updateNulls);

		if(!$ret) {
			$this->_error = strtolower(get_class($this))."::". JText::_('store failed') ." <br />" . $this->_db->stderr();
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Destroys the pesisting session
	 */
	function destroy($userId) {
		
		$query = "DELETE FROM #__ninjaboard_session"
				. "\n WHERE id_user = ". $this->_db->Quote($userId)
				;
		$this->_db->setQuery($query);

		if (!$this->_db->query()) {
			$this->_error = $this->_db->stderr();
			return false;
		}

		return true;
	}

	/**
	 * Purge old sessions
	 *
	 * @param int 	Session age in seconds
	 * @return mixed Resource on success, null on fail
	 */
	function purge($sessionTime = 1440) {
		$past = time() - $sessionTime;
		$query = 'DELETE FROM '. $this->_tbl .' WHERE (action_time < '. (int) $past .')';
		$this->_db->setQuery($query);

		return $this->_db->query();
	}

	/**
	 * Find out if a user has a one or more active sessions
	 *
	 * @param int $userid The identifier of the user
	 * @return boolean True if a session for this user exists
	 */
	function exists($userId) {
		$query = 'SELECT COUNT(id_user) FROM #__ninjaboard_session'
			. ' WHERE id_user = '. $this->_db->Quote($userId);
		$this->_db->setQuery($query);

		if (!$result = $this->_db->loadResult()) {
			$this->_error = $this->_db->stderr();
			return false;
		}

		return (boolean) $result;
	}
}