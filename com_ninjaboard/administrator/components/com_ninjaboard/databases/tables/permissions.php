<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: permissions.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard permissions table
 *
 * This table don't follow koowas naming conventions so we do overrides here
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTablePermissions extends KDatabaseTableAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		$options->name		= 'ninjaboard_assets';
		$options->identity_column	= 'ninjaboard_asset_id';
		
		parent::__construct($options);
	}
}