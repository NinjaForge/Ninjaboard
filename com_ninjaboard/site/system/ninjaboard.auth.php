<?php
/**
 * @version $Id: ninjaboard.auth.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Authentification
 *
 * @package Ninjaboard
 */
class NinjaboardAuth
{
	/**
	 * authentification option list
	 * @var array
	 */
	var $_authOptionList = null;
	
	/**
	 * user roles list
	 * @var array
	 */
	var $_userRoleList = null;
		
	function NinjaboardAuth() {	 
	}
	
	/**
	 * get a ninjaboard authentification object
	 *
	 * @access public
	 * @return object of NinjaboardAuth
	 */
	function &getInstance() {
	
		static $ninjaboardAuth;

		if (!is_object($ninjaboardAuth)) {
			$ninjaboardAuth = new NinjaboardAuth();
		}

		return $ninjaboardAuth;
	}

	function getAuth($auth, $id_forum) {
		$ninjaboardUser =& NinjaboardHelper::getNinjaboardUser();

		$row =& JTable::getInstance('NinjaboardForum');
		
		if ($row->load($id_forum)) {
			if ($row->$auth <= $ninjaboardUser->get('role')) {
				return true;
			} else if ($row->$auth <= $ninjaboardUser->getExtendedRole($id_forum)) {
				return true;
			} else if ($row->$auth <= $ninjaboardUser->getGroupRole($id_forum)) {
				return true;
			}
		}
		return false;
	}
	
	function getUserRole($id_forum) {
		$ninjaboardUser =& NinjaboardHelper::getNinjaboardUser();

		$role = $ninjaboardUser->get('role');

		if ($role < ($extendedRole = $ninjaboardUser->getExtendedRole($id_forum))) {
			$role = $extendedRole;
		}
		if ($role < ($groupRole = $ninjaboardUser->getGroupRole($id_forum))) {
			$role = $groupRole;
		}
		
		return $role;
	}
	
	function getAuthOptionList() {
		
		if (empty($this->_authOptionList)) {
			$_authOptionList = array();
			$_authOptionList[] = JHTML::_('select.option', '0', JText::_('NB_ALL'));
			$_authOptionList[] = JHTML::_('select.option', '1', JText::_('NB_REGISTERED'));
			$_authOptionList[] = JHTML::_('select.option', '2', JText::_('NB_PRIVATE'));
			$_authOptionList[] = JHTML::_('select.option', '3', JText::_('NB_MODERATOR'));
			$_authOptionList[] = JHTML::_('select.option', '4', JText::_('NB_ADMINISTRATOR'));
		}
		
		return $_authOptionList;
	}
	
	function getUserRoleList() {
		
		if (empty($this->_userRoleList)) {
			$_userRoleList = array();
			$_userRoleList[0] = JText::_('NB_NONE');
			$_userRoleList[1] = JText::_('NB_REGISTERED');
			$_userRoleList[2] = JText::_('NB_PRIVATE');
			$_userRoleList[3] = JText::_('NB_MODERATOR');		
			$_userRoleList[4] = JText::_('NB_ADMINISTRATOR');
		}
		
		return $_userRoleList;
	}
	
	/**
	 * get authentification text
	 */	
	function getAuthText($auth) {
		global $mainframe;
		$result = '';

		if (isset($auth)) {
			switch ($auth) {
				case 0:
					$result = JText::_('NB_ALL');
					break;
				case 1:
					$result = JText::_('NB_REG');
					break;
				case 2:
					$result = JText::_('NB_PRIVATE');
					break;
				case 3:
					$result = JText::_('NB_MODS');
					break;
				case 4:
					$result = JText::_('NB_ADMIN');
					break;																	
				default:
					$result = JText::_('NB_NONE');
					break;
			}
		} else {
			$result = '-';
		}
		return $result;
	}

}
?>