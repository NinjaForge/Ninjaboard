<?php
/**
 * @version $Id: captcha.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

define('DS', DIRECTORY_SEPARATOR);

define('NB_CAPTCHABASE', dirname(__FILE__));
define('NB_CAPTCHAFONTS', NB_CAPTCHABASE.DS.'fonts');
define('NB_CAPTCHAIMAGES', NB_CAPTCHABASE.DS.'images');

/**
 * Ninjaboard Captcha
 *
 * @package Ninjaboard
 */
class NinjaboardCaptcha
{
	/**
	 * image file list
	 *
	 * @var array
	 */
	var $_imageFileList;

	/**
	 * font file list
	 *
	 * @var array
	 */
	var $_fontFileList;
	
	/**
	 * character set
	 *
	 * @var string
	 */	
	var $_characterSet = "ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";
	
	function NinjaboardCaptcha() {
		$this->setImageList();
		$this->setFontList();
	}
	
	/**
	 * get a ninjaboard captcha object
	 *
	 * @access public
	 * @return object of NinjaboardCaptcha
	 */
	function &getInstance() {
	
		static $ninjaboardCaptcha;

		if (!is_object($ninjaboardCaptcha)) {
			$ninjaboardCaptcha = new NinjaboardCaptcha();
		}

		return $ninjaboardCaptcha;
	}

	function createImage() {
	
		$codeText = $this->getCodeString(4);
		$_SESSION['captcha_code'] = md5($codeText); 

		header('Content-type: image/png'); 
	
		// create a background image
		$captchaImage = ImageCreateFromPNG($this->getRandomImage());

		$color = ImageColorAllocate($captchaImage, 255, 255, 255);
			
//echo count($this->_fontFileList);
//print_r($this->_fontFileList);

		$codeTextLength = strlen($codeText);
		$this->segmentSize = (int)(ImageSX($captchaImage) / $codeTextLength);
		for ($i=0; $i < $codeTextLength; $i++) {
			$this->drawCharacter($captchaImage, $codeText[$i], $i);
		}
//echo $_SESSION['captcha_code'];		
		ImagePNG($captchaImage);
		ImageDestroy($captchaImage);
	}

	function drawCharacter($captchaImage, $character, $characterPos) {
	
		// get random font
		$characterFont = NB_CAPTCHAFONTS.DS.$this->_fontFileList[array_rand($this->_fontFileList)];
		
		// get random colour
		$textColour = ImageColorAllocate($captchaImage, rand(120, 125), rand(120, 125), rand(120, 125));

		// get random font size
		$fontSize = rand(25, 30);
		
		// get random angle
		$angle = rand(-30, 30);
		
		// get the points of the bounding box of the character
		$characterDetails = ImageTTFBBox($fontSize, $angle, $characterFont, $character);
		
		/**
			0 lower left corner, X position 
			1 lower left corner, Y position 
			2 lower right corner, X position 
			3 lower right corner, Y position 
			4 upper right corner, X position 
			5 upper right corner, Y position 
			6 upper left corner, X position 
			7 upper left corner, Y position
		*/		

//print_r($characterDetails);

		// calculate character starting coordinates
		$posX = $characterPos * $this->segmentSize;
		$posY = rand(ImageSY($captchaImage) - ($characterDetails[1] - $characterDetails[7]), ImageSY($captchaImage)-10);
		
		// draw the character
		ImageTTFText($captchaImage, $fontSize, $angle, $posX, $posY, $textColour, $characterFont, $character);
	}
	
	function getRandomImage() {
		return NB_CAPTCHAIMAGES.DS.$this->_imageFileList[array_rand($this->_imageFileList)];
	}
			
	function setImageList() {
		if (empty($this->_imageFileList)) {
			$this->_imageFileList = array();
			$this->_imageFileList = $this->getFileList(NB_CAPTCHAIMAGES, '.png');
		}
	}
	
	function setFontList() {
		if (empty($this->_fontFileList)) {
			$this->_fontFileList = array();
			$this->_fontFileList = $this->getFileList(NB_CAPTCHAFONTS, '.ttf');
		}
	}

	function getCodeString($lengh) { 

		srand($this->makeSeed());   

		while(strlen($codeString) < $lengh) {
			$codeString .= substr($this->_characterSet, (rand() % (strlen($this->_characterSet))), 1); 
		}

		return($codeString); 
	}
	
	function makeSeed(){ 
		list($usec, $sec) = explode (' ', microtime()); 
		return (float) $sec + ((float) $usec * 100000); 
	}
		
	function getFileList($path, $filter = '.') { 
	
		// initialize variables
		$files = array();

		if (is_dir($path)) {
			$handle = opendir($path);
			while (($file = readdir($handle)) !== false) {
				if (($file != '.') && ($file != '..')) {
					if (preg_match("/$filter/", strtolower($file))) {
						$files[] = $file;
					}
				}
			}
			closedir($handle);
			asort($files);
		} else {
			$files = NULL;
		}
		return $files;
	}

}
?>