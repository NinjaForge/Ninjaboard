<?php
/**
 * @version $Id: userlist.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Information Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelUserList extends NinjaboardModel
{
	
	/**
	 * ninjaboard users data array
	 *
	 * @var array
	 */
	var $_ninjaboardUsers = null;
	
	/**
	 * get ninjaboard users
	 * 
	 * @access public
	 * @return array
	 */
	function getNinjaboardUsers() {
	
		// load ninjaboard users
		if (empty($this->_ninjaboardUsers)) {
			$ninjaboardConfig =& NinjaboardConfig::getInstance();
			$limit = JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('items_per_page'), '', 'int');
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
				
			$query = "SELECT u.id, u.name, u.registerDate, ju.role, ju.posts, s.action_time"
					. "\n FROM #__users AS u"
					. "\n INNER JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
					. "\n LEFT JOIN #__ninjaboard_session AS s ON s.id_user = u.id"
					;
			$this->_ninjaboardUsers = $this->_getList($query, $limitstart, $limit);
		}
		
		return $this->_ninjaboardUsers;
	}
	
}
?>