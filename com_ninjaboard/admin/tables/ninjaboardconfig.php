<?php
/**
 * @version $Id: ninjaboardconfig.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Config Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardConfig extends JTable {
	/** @var int Unique id*/
	var $id						= null;
	/** @var string */
	var $name					= null;
	/** @var int */
	var $default_config			= null;	
	/** @var int */
	var $id_design				= null;
	/** @var int */
	var $id_timezone			= null;			
	/** @var int */
	var $id_timeformat			= null;
	/** @var string */
	var $editor					= null;	
	/** @var int */
	var $topic_icon_function	= null;
	/** @var int */
	var $post_icon_function		= null;
	/** @var string */	
	var $board_settings			= null;
	/** @var string */	
	var $latestpost_settings	= null;
	/** @var string */
	var $feed_settings			= null;
	/** @var string */
	var $view_settings			= null;
	/** @var string */
	var $view_footer_settings	= null;
	/** @var string */
	var $user_settings_defaults	= null;
	/** @var string */
	var $attachment_settings	= null;
	/** @var string */
	var $avatar_settings		= null;
	/** @var string */
	var $captcha_settings		= null;
	/** @var int */
	var $checked_out			= null;	
	/** @var datetime */
	var $checked_out_time		= null;
								
	/**
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		parent::__construct( '#__ninjaboard_configs', 'id', $db );
	}
	
}
?>