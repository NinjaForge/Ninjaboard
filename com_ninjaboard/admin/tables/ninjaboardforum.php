<?php
/**
 * @version $Id: ninjaboardforum.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Forum Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardForum extends JTable {
	/** @var int Unique id*/
	var $id					= null;
	/** @var string */
	var $name				= null;
	/** @var string */
	var $description		= null;	
	/** @var int */
	var $state				= null;
	/** @var int */
	var $locked				= null;	
	/** @var int */
	var $ordering			= null;	
	/** @var int */
	var $new_posts_time		= null;
	/** @var int */
	var $posts				= null;
	/** @var int */
	var $topics				= null;	
	/** @var int */
	var $auth_view			= null;
	/** @var int */
	var $auth_read			= null;	
	/** @var int */
	var $auth_post			= null;	
	/** @var int */
	var $auth_reply			= null;	
	/** @var int */
	var $auth_edit			= null;	
	/** @var int */
	var $auth_delete		= null;
	/** @var int */
	var $auth_reportpost	= null;
	/** @var int */
	var $auth_sticky		= null;
	/** @var int */
	var $auth_lock			= null;
	/** @var int */
	var $auth_announce		= null;
	/** @var int */
	var $auth_vote			= null;	
	/** @var int */
	var $auth_pollcreate	= null;
	/** @var int */
	var $auth_attachments	= null;
	/** @var int */
	var $checked_out		= null;	
	/** @var datetime */
	var $checked_out_time	= null;	
	/** @var int */
	var $id_cat				= null;	
	/** @var int */
	var $id_last_post		= null;	
														
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_forums', 'id', $db);
	}
	
}

?>