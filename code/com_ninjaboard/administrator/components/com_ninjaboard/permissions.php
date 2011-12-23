<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

//Classes outside folders are not autoloaded
KLoader::loadIdentifier('ninja:permissions');

/**
 * ComNinjaboardPermissions
 *
 * Singleton for getting permissions, as well as making it possible for 
 * plugins to add new permission objects.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
final class ComNinjaboardPermissions extends NinjaPermissions
{
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'data'	=> array(
				'forum'			=> 1,
				'topic'			=> 1,
				'post'			=> 1,
				'attachment'	=> 0
			)
		));

		parent::_initialize($config);
	}
}