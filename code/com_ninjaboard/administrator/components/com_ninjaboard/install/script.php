<?php
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('JDependent', dirname(__FILE__).'/jdependent/jdependent.php');

/**
 * Installation Script Files
 */
class com_NinjaboardInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		$installer = new JDependent();

		$installer->install('nooku')->install('ninja');

		$this->installAdditionals($parent);
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		$installer = new JDependent();

		$installer->uninstall('nooku')->uninstall('ninja');
	}

	function installAdditionals($parent)
	{
		$path 	= $parent->getParent()->getPath('source').'/packages/';
		foreach (JFolder::files($path) as $extension) {
			$folder = JFile::stripExt($extension);
		
			JArchive::extract($path.$extension, $path.$folder);
			$installer	= new JInstaller;
			$installer->install($path.$folder);
		}
	}

	/**
	 * method to upgrade the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		$installer = new JDependent();

		$installer->install('nooku')->install('ninja');

		$this->installAdditionals($parent);

		// if com_koowa exists remove it
		if (JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_koowa')) {
			JFolder::delete(JPATH_ADMINISTRATOR.'/components/com_koowa');
			
			if (JFolder::exists(JPATH_ROOT.'/media/com_koowa')) {
				JFolder::delete(JPATH_ROOT.'/media/com_koowa');
			}

			// remove it from the database
			$db = JFactory::getDBO();
			$db->setQuery("DELETE FROM `#__extensions` WHERE `element` = 'com_koowa' OR `element` = 'com_ninja';");
			$db->execute();
		}
	}
}