<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 2439 2011-09-01 11:53:24Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewPermissionsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{		
		jimport('joomla.filesystem.folder');
		$controllers = JFolder::files(JPATH_COMPONENT_SITE.'/controllers', '.php$');
		foreach($controllers as $controller)
		{
			$name = str_replace('.php', '', $controller);
			$this->controllers[$name] = 'com://site/ninjaboard.controller.'.$name;
		}
		
		return parent::display();
	}
}