<?php
/**
 * @version $Id: ninjaboardpost.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Post Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardPost extends JTable {
	/** @var int Unique id*/
	var $id					= null;
	/** @var string */
	var $subject			= null;
	/** @var string */
	var $text				= null;		
	/** @var string */
	var $date_post			= null;
	/** @var string */
	var $date_last_edit		= null;
	/** @var int */
	var $id_edit_by			= null;
	/** @var string */
	var $edit_reason		= null;
	/** @var int */
	var $enable_bbcode		= null;	
	/** @var int */
	var $enable_emoticons	= null;
	/** @var int */
	var $notify_on_reply	= null;			
	/** @var string */
	var $ip_poster			= null;
	/** @var int */
	var $icon_function		= null;	
	/** @var string */
	var $id_topic			= null;
	/** @var int */
	var $id_forum			= null;
	/** @var int */
	var $id_parent			= null;
	/** @var int */
	var $id_user			= null;
	/** @var string */
	var $guest_name			= null;
	/** @var string */
	var $guest_email		= null;
																
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_posts', 'id', $db);
	}
	
}
?>
