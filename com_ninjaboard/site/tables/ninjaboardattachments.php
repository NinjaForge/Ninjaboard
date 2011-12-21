<?php defined('_JEXEC') or die('Restricted access');
/**
 * @version $Id: ninjaboardattachments.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 */
 
/**
 * Ninjaboard Attachments Table Class
 *
 * @package Ninjaboard
 */
class TableNinjaboardAttachments extends JTable {
	/** @var int Unique id*/
	var $id					= null;
	/** @var int */
	var $id_user			= null;
	/** @var int */
	var $id_post			= null;
	/** @var string */
	var $file_name			= null;	
														
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_attachments', 'id', $db);
	}
	
}
?>