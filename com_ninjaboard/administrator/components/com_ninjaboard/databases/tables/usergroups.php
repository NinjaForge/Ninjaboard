<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Usergroups table
 *
 * We had some issues with Mod Security blocking user_group requests.
 * So we had to rename it to usergroup.
 * To keep table altering down to a low, we just override koowas default behavior from here.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTableUsergroups extends KDatabaseTableAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		$options->name		= 'ninjaboard_user_groups';
		$options->identity_column	= 'ninjaboard_user_group_id';
		
		$options->behaviors = array('orderable');
		
		parent::__construct($options);
	}
}