<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewUsergroupsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$permissions 	= array();
		$objects 		= $this->getService('com://admin/ninjaboard.permissions')->getObjects();
		
		foreach($objects as $permission)
		{
			$permissions[$permission] = KInflector::humanize($permission);
		}
		$this->assign('columns', $permissions);
		$this->assign('colspan', 4 + count($permissions));

		return parent::display();
	}
}