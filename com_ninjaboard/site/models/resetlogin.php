<?php
/**
 * @version $Id: resetlogin.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Reset Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelResetLogin extends NinjaboardModel
{
	
	/**
	 * get ninjaboard user object
	 *
	 * @access public
	 * @return object
	 */
	function getNinjaboardUser() {
		
		$db   =& JFactory::getDBO();
		$activation	= JRequest::getVar('activation', '');

		$ninjaboardUser = null;

		$query = "SELECT u.id"
				. "\n FROM #__users AS u"
				. "\n WHERE u.activation = '$activation'"
				;
		$db->setQuery($query);
		$userId = $db->loadResult();
		
		if ($userId) {
			$ninjaboardUser =& NinjaboardUser::getInstance($userId);
		}
			
		return $ninjaboardUser;
	}

}
?>