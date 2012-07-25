<?php
/**
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_quickpanel
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 defined( '_JEXEC' ) or die( 'Restricted access' );

 jimport('joomla.filesystem.file');

if (JFile::exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')) {
	KLoader::loadIdentifier('com://site/ninjaboard.router');

	echo KService::get('mod://site/ninjaboard_quickpanel.html')
		->module($module)
	    ->attribs($attribs)
	    ->display();
} else {
	echo JText::_('MOD_NINJABOARD_QUICKPANEL_NINJABOARD_NOT_INSTALLED');
}