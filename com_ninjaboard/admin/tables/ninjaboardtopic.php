<?php
/**
 * @version $Id: ninjaboardtopic.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Topic Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardTopic extends JTable {
	/** @var int Unique id*/
	var $id					= null;
	/** @var string */
	var $views				= null;
	/** @var string */
	var $replies			= null;		
	/** @var string */
	var $status				= null;
	/** @var string */
	var $vote				= null;		
	/** @var string */
	var $type				= null;	
	/** @var int */
	var $id_moved			= null;
	/** @var int */
	var $id_forum			= null;
	/** @var int */
	var $id_first_post		= null;
	/** @var int */
	var $id_last_post		= null;
	/** @var int */
	var $id_user			= null;
																		
	/**
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		parent::__construct( '#__ninjaboard_topics', 'id', $db );
	}
	
}
?>