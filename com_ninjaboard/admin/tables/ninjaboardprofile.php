<?php
/**
 * @version $Id: ninjaboardprofile.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Profile Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardProfile extends JTable {
	/** @var int Unique id*/
	var $id	= null;
														
	/**
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		parent::__construct( '#__ninjaboard_profiles', 'id', $db );
		
		// set dynamic fields
		$fieldrows = JTableNinjaboardProfile::_getProfileFields($db);
		foreach($fieldrows as $fieldrow) {
			$fieldname = $fieldrow->name;
			$this->$fieldname = $fieldrow->default;
		}
	}
	
	/**
	 * @param database A database connector object
	 */
	function _getProfileFields( &$db ) {
		$query = "SELECT f.*"
				. "\n FROM #__ninjaboard_profiles_fields AS f"
				. "\n WHERE f.published = 1"
				. "\n ORDER BY f.ordering"
				;
		$db->setQuery($query);
		return $db->loadObjectList();
	}	
}
?>