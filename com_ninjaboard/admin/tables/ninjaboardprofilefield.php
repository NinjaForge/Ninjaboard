<?php
/**
 * @version $Id: ninjaboardprofilefield.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Profile Field Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardProfileField extends JTable {
	/** @var int Unique id*/
	var $id						= null;
	/** @var string */
	var $name					= null;
	/** @var string */
	var $title					= null;
	/** @var string */
	var $description			= null;
	/** @var int */
	var $element				= null;	
	/** @var string */
	var $type					= null;
	/** @var int */
	var $size					= null;
	/** @var int */
	var $length					= null;
	/** @var int */
	var $rows					= null;
	/** @var int */
	var $columns				= null;						
	/** @var int */
	var $published				= null;
	/** @var int */
	var $required				= null;
	/** @var int */
	var $disabled				= null;
	/** @var int */
	var $show_on_registration	= null;
	/** @var int */
	var $ordering				= null;
	/** @var int */
	var $checked_out			= null;	
	/** @var datetime */
	var $checked_out_time		= null;	
	/** @var int */
	var $id_profile_field_set	= null;		
	/** @var int */
	var $id_profile_field_list	= null;
																	
	/**
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		parent::__construct( '#__ninjaboard_profiles_fields', 'id', $db );
	}
	
}
?>