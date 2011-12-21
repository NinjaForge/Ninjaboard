<?php
/**
 * @version $Id: ninjaboardgroupsmapping.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Groups Mapping Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardGroupsMapping extends JTable {
	/** @var int */
	var $id_mapping_type	= null;
	/** @var int */
	var $id_group			= null;
	/** @var string */
	var $value				= null;
														
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_groups_mapping', 'id', $db);
	}
	
}
?>