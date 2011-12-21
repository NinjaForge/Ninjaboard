<?php
/**
 * @version $Id: ninjaboard.attachment.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Attachment
 *
 * @package Ninjaboard
 */
class NinjaboardAttachment
{
	/**
	 * attachment file
	 *
	 * @var string
	 */
	var $attachmentFile;
	
	/**
	 * constructor
	 */
	function NinjaboardAttachment($attachmentFile) {
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();	
	}

	/**
	 * upload attachment
	 *
	 * @access 	public
	 */		
	function uploadAttachment($attachmentFile) {
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		
		jimport('joomla.filesystem.file');
	}

	/**
	 * delete attachment
	 *
	 * @access 	public
	 */	
	function deleteAttachment($attachmentFile) {
	}
	
}
?>