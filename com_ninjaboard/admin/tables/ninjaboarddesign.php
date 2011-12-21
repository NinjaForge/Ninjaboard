<?php
/**
 * @version $Id: ninjaboarddesign.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Design Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardDesign extends JTable {
	/** @var int Unique id*/
	var $id					= null;
	/** @var string */
	var $name				= null;
	/** @var int */
	var $default_design		= null;	
	/** @var string */
	var $template			= null;
	/** @var string */
	var $style				= null;	
	/** @var string */
	var $emoticon_set		= null;
	/** @var string */
	var $icon_set			= null;
	/** @var int */
	var $button_set			= null;
	/** @var int */
	var $checked_out		= null;	
	/** @var datetime */
	var $checked_out_time	= null;
																	
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_designs', 'id', $db);
	}
	
}
?>
