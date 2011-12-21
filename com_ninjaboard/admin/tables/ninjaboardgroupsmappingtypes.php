<?php
/**
 * @version $Id: ninjaboardgroupsmappingtypes.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Groups Mapping Types Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardGroupsMappingTypes extends JTable {
	/** @var int */
	var $id					= null;
	/** @var string */
	var $name				= null;
	/** @var string */
	var $description		= null;
	/** @var string */
	var $get_value_sql		= null;
	/** @var string */
	var $make_param_list_sql= null;
														
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_groups_mapping_types', 'id', $db);
	}
	
}
?>