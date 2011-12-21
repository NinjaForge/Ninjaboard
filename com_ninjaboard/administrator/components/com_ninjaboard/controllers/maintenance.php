<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: maintenance.php 2183 2011-07-11 14:59:16Z stian $
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
		return KFactory::get('admin::com.ninjaboard.database.table.forums')->maintenance();
	}

	public function _actionLogtopicreads()
	{
		return KFactory::get('admin::com.ninjaboard.database.table.logtopicreads')->maintenance();
	}

	public function _actionTopics()
	{
		return KFactory::get('admin::com.ninjaboard.database.table.topics')->maintenance();
	}
}