<?php
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
defined('_JEXEC') or die;

JLoader::register('JDependent', dirname(__FILE__).'/jdependent/jdependent.php');

$installer = new JDependent();

$installer->uninstall('nooku')->uninstall('ninja');