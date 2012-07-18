<?php
/**
 * @package		Ninja
 * @subpackage	Installer
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
defined('_JEXEC') or die;

JLoader::register('JDependent', dirname(__FILE__).'/jdependent/jdependent.php');

$installer = new JDependent();

$installer->install('nooku')->install('ninja');

// if com_koowa exists remove it
if (JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_koowa')) {
	JFolder::delete(JPATH_ADMINISTRATOR.'/components/com_koowa');
	
	if (JFolder::exists(JPATH_ROOT.'/media/com_koowa')) {
		JFolder::delete(JPATH_ROOT.'/media/com_koowa');
	}

	// remove it from the database
	$db = JFactory::getDBO();
	$db->setQuery("DELETE FROM `#__components` WHERE `option` = 'com_koowa' OR `option` = 'com_ninja';");
	$db->query();
}