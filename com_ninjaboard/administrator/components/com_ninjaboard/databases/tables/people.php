<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: people.php 1723 2011-03-28 16:35:59Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard users table
 *
 * Stores user data, like signature, avatar, stats
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTablePeople extends KDatabaseTableDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$config->identity_column	= 'ninjaboard_person_id';
		
		parent::__construct($config);
	}
}