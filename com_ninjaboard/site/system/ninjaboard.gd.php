<?php
/**
 * @version $Id: ninjaboard.gd.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard GD
 *
 * @package Ninjaboard
 */
class NinjaboardGD
{
	var $_quality;
	var $_tmpImage;
	
	function NinjaboardGD($quality = 100) {
		$this->_quality = $quality;
	}
	
	/**
	 * get instance
	 *
	 * @access public
	 * @return object
	 */
	function &getInstance() {
	
		static $ninjaboardGD;

		if (!is_object($ninjaboardGD)) {
			$ninjaboardConfig =& NinjaboardConfig::getInstance();
			$ninjaboardGD = new NinjaboardGD($ninjaboardConfig->getAvatarSettings('image_resize_quality'));
		}

		return $ninjaboardGD;
	}
	
	/**
	 * is enabled
	 *
	 * @access public
	 * @return boolean
	 */
	function isEnabled() { 
		if (extension_loaded('gd') || extension_loaded('gd2')) {
			return true;
		}
		return false;
	}
	
	/**
	 * resize image
	 *
	 * @access public
	 * @return boolean
	 */
	function resizeImage($fileName, $maxWidth=100, $maxHeight=100) {
		$messageQueue 	=& NinjaboardMessageQueue::getInstance();
		
		if (!$this->isEnabled()) {
			return false;
		}
		
		$resizeFactorWidth=0;
		$resizeFactorHeight=0;
		$size = getimagesize($fileName);

		if ($maxWidth < $size[0]) {
			$resizeFactorWidth = 100 / $size[0] * $maxWidth;
		}
		if ($maxHeight < $size[1]) {
			$resizeFactorHeight = 100 / $size[1] * $maxHeight;
		}

		$resizeFactor = max($resizeFactorWidth, $resizeFactorHeight);
		$width = $size[0] / 100 * $resizeFactor;
		$height = $size[1] / 100 * $resizeFactor;
	
		if ($resizeFactor > 0) {
			$this->_tmpImage = @imagecreatetruecolor($width, $height);
	
			$image = $this->createImage($fileName, $size[2]);
			if (!$image) {
				$messageQueue->addMessage(JText::_('NB_MSGFAILEDCRATEIMAGE'));
				return false;
			}

			// set transparency if we are handling gif or png
			if(($size[2] == 1) || ($size[2] == 3)) {
				imagealphablending($this->_tmpImage, false);
				imagesavealpha($this->_tmpImage, true);
				$transparent = imagecolorallocatealpha($this->_tmpImage, 255, 255, 255, 127);
				imagefilledrectangle($this->_tmpImage, 0, 0, $width, $height, $transparent);
			}

    		imagecopyresampled($this->_tmpImage, $image, 0,0, 0,0, $width, $height, $size[0], $size[1]);
    		
    		$this->putImage($fileName, $size[2]);
    		
    		imagedestroy($this->_tmpImage);
		}
		return true;
	}
	
	/**
	 * create image
	 *
	 * @access public
	 * @return resource
	 */
	function createImage($fileName, $imageType) {

		switch ($imageType) {
			case 1:
				$resource = imagecreatefromgif($fileName);
				break;
			case 2:
				$resource = imagecreatefromjpeg($fileName);
				break;
			case 3:
				$resource = imagecreatefrompng($fileName);
				break;
			case 4:
				$resource = imagecreatefromwbmp($fileName);
				break;
			default: 
				$resource = false;
		}
		
		return $resource;
	}
	
	/**
	 * put image
	 *
	 * @access public
	 * @return boolean
	 */	
	function putImage($fileName, $imageType) {
		$result = true;
		
		switch ($imageType) {
			case 1:
				imagegif($this->_tmpImage, $fileName);
				break;
			case 2:
				imagejpeg($this->_tmpImage, $fileName, 50);
				break;
			case 3:
				imagepng($this->_tmpImage, $fileName);
				break;
			case 4:
				imagewbmp($this->_tmpImage, $fileName);
				break;
			default: 
				$result = false;
		}
		
		return $result;
	}
	
}
