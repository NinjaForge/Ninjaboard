<?php
/**
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_latest_posts
 * @copyright	Copyright (C) 2010 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 defined( '_JEXEC' ) or die( 'Restricted access' );

 jimport('joomla.filesystem.file');

 // load com_ninja's language file

 $lang = JFactory::getLanguage();

// load the com_ninja english language file
$lang->load('com_ninja', JPATH_ADMINISTRATOR, 'en-GB', true);

// load the foriegn language file for com_ninja
$lang->load('com_ninja', JPATH_ADMINISTRATOR, $lang->getDefault(), true);

if (JFile::exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')) {

	$module->params = $params;
	echo KService::get('mod://site/ninjaboard_latest_posts.html')
		->module($module)
	    ->attribs($attribs)
	    ->layout($params->get('layout', 'default'))
	    ->display();
} else {
	echo JText::_('MOD_NINJABOARD_LATEST_POSTS_NINJABOARD_NOT_INSTALLED');
}
