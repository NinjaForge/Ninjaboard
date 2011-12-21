<?php
/**
 * @version $Id: ninjaboarduser.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard User Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardUser extends JTable {
	/** @var int Unique id*/
	var $id					= null;
	/** @var int */
	var $posts				= null;
	/** @var int */
	var $role				= null;
	/** @var int */
	var $system_emails		= null;
	/** @var int */
	var $show_email			= null;
	/** @var int */
	var $show_online_state	= null;
	/** @var int */
	var $notify_on_reply	= null;
	/** @var int */
	var $enable_bbcode		= null;
	/** @var int */
	var $enable_emoticons	= null;
	/** @var string */
	var $avatar_file		= null;
	/** @var string */
	var $signature			= null;	
	/** @var decimal */
	var $time_zone			= null;
	/** @var string */
	var $time_format		= null;		
	
	var $gender				= null;		
	
	var $show_gender		= null;		
	
	var $birthdate			= null;		
	
	var $show_birthdate		= null;		
	
	var $location			= null;		
	
								
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_users', 'id', $db);
	}
	
}

?>
