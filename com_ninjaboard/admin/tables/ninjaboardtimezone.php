<?php
/**
 * @version $Id: ninjaboardtimezone.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Time Zone Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardTimeZone extends JTable {
	/** @var int Unique id*/
	var $id					= null;
	/** @var string */
	var $name				= null;
	/** @var string */
	var $description		= null;
	/** @var int */
	var $offset				= null;
	/** @var int */
	var $published			= null;
	/** @var int */
	var $checked_out		= null;
	/** @var datetime */
	var $checked_out_time	= null;
														
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_timezones', 'id', $db);
	}
	
}
?>