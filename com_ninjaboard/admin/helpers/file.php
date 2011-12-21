<?php defined ('_JEXEC') or die('Direct Access to this location is not allowed.');
/**
 * @version $Id: file.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

class NinjaboardFileHelper {

	function cleanFileName($filename = '') {

		/**
		* This function is not needed, because JFile::upload() does this job!
		* I do not delete it, because I don't know if we might need this for
		* other purposes (somewhen).
		*/
		return preg_replace(
			array('/[\x21-\x2d]/u','/[\x5b-\x60]/u','/[\x7b-\xff]/u','/_+/','/(^_|_$)/'), array('_','_','_','_',''),
			urldecode(strtolower(trim($filename)))
		);    
	}

	function getUploadMaxFilesize() {
	
		/**
		* Here we get the maximum size an uploaded file should not exceed.
		*/
	
		$filesize = ini_get('upload_max_filesize');
	
		if     (strpos($filesize, 'K') !== FALSE)	return substr($filesize, 0, -1) * 1024;
		elseif (strpos($filesize, 'M') !== FALSE)	return substr($filesize, 0, -1) * 1024 * 1024;
		elseif (strpos($filesize, 'G') !== FALSE)	return substr($filesize, 0, -1) * 1024 * 1024 * 1024;
		else
			return $filesize;
	}

} // End class
