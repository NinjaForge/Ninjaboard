<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('JDependent', dirname(__FILE__).'/jdependent/jdependent.php');

/**
 * Installation Script Files
 */
class com_ninjaboardInstallerScript
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
		error_log('does it exist? '.JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_koowa'));
		// if com_koowa exists remove it
		if (JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_koowa')) {
			error_log('it exists');
			JFolder::delete(JPATH_ADMINISTRATOR.'/components/com_koowa');
			JFolder::delete(JPATH_ROOT.'/media/com_koowa');

			// remove it from the database
			$db = JFactory::getDBO();
			$db->setQuery("DELETE FROM `#__extensions` WHERE `element` = 'com_koowa' OR `element` = 'com_ninja';");
			error_log("DELETE FROM `#__extensions` WHERE `element` = 'com_koowa' OR `element` = 'com_ninja';");
			$db->execute();
		}
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
}