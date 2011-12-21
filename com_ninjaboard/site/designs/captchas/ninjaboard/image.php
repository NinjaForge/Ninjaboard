<?php
/**
 * @version $Id: image.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

require_once('captcha.php');

$ninjaboardCaptcha =& NinjaboardCaptcha::getInstance();
$ninjaboardCaptcha->createImage();
		
?>