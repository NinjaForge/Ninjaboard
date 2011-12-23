<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard avatars table
 *
 * Gets the avatars, allowing gravatar, cb integration and more through a single interface
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTableAvatars extends KDatabaseTableAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration config
	 */
	public function __construct(KConfig $config)
	{
		$config->name		= 'ninjaboard_people';
		$config->identity_column	= 'ninjaboard_person_id';
		
		parent::__construct($config);
	}
}