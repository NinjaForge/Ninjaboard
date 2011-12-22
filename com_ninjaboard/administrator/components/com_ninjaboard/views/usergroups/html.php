<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewUsergroupsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		if(!$this->getModel()->getTotal()) return parent::display();

		$this->_createToolbar()
			->prepend('spacer')
			->prepend(KFactory::get('admin::com.ninja.toolbar.button.modal', array(
				'text' => 'Map',
				'icon' => 'icon-32-map',
				'link' => $this->createRoute('view=joomlausergroupmaps&tmpl=component'),
				'y'	   => 470,
				'handler' => 'iframe',
				'ajaxOptions' => '{evalScripts:true}'
			)));

		$permissions 	= array();
		$objects 		= KFactory::get('admin::com.ninjaboard.permissions')->getObjects();
		foreach($objects as $permission)
		{
			$permissions[$permission] = KInflector::humanize($permission);
		}
		$this->assign('columns', $permissions);
		$this->assign('colspan', 4 + count($permissions));

		return parent::display();
	}
}