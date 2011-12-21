<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 2499 2011-11-22 02:38:51Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewJoomlausergroupmapsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{		
		$this->getService('ninja:template.helper.document')->load('/' . $this->getIdentifier()->application . '.' . $this->getName() . '.css');

		$this->acltree    = $this->getService('ninja:model.core_usergroups')->getList();
		$this->usergroups = $this->getService('com://admin/ninjaboard.model.usergroups')->limit(0)->getList();
		
		$maps = array();
		foreach($this->getService($this->getModel()->getIdentifier())->limit(0)->getList() as $map)
		{
			$maps[$map->id] = $map->ninjaboard_gid;
		}
		$this->maps = $maps;
		

		return parent::display();
	}
}