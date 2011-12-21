<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: discussions.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersDiscussions
 *
 * Imports data from Discussions.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersDiscussions extends ComNinjaboardDatabaseConvertersAbstract
{
	/**
	 * Checks if Discussions can be converted
	 *
	 * @TODO Discussions converter is disabled until it's completed
	 *
	 * @return boolean
	 */
	public function canConvert()
	{
		return false;
	
		return JComponentHelper::getComponent( 'com_discussions', true )->enabled;
	}
}