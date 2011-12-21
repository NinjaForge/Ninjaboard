<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: maintenance.php 2460 2011-10-11 21:21:19Z stian $
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
		return $this->getService('com://admin/ninjaboard.database.table.forums')->maintenance();
	}

	public function _actionLogtopicreads()
	{
		return $this->getService('com://admin/ninjaboard.database.table.logtopicreads')->maintenance();
	}

	public function _actionTopics()
	{
		return $this->getService('com://admin/ninjaboard.database.table.topics')->maintenance();
	}
}