<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: users.php 1690 2011-03-25 00:56:53Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Joomla users table
 *
 * We use the core joomla users table so we don't have to sync users back and forth.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTableUsers extends KDatabaseTableAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		$options->name		= 'users';
		
		parent::__construct($options);
	}
}