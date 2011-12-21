<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: maintenance.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Default Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerMaintenance extends KControllerAbstract
{
	public function _actionForums()
	{
		KFactory::get('admin::com.ninjaboard.database.table.forums')->maintenance();
	}
}