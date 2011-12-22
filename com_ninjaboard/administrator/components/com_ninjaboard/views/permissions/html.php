<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewPermissionsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{		
		// Display the toolbar
		$toolbar = $this->_createToolbar()->reset();
		
		$this->toolbar = KFactory::get($this->getToolbar(), array('isGrid' => false))
							->append(KFactory::get('admin::com.ninja.toolbar.button.apply', array('isGrid' => false)));
		
		jimport('joomla.filesystem.folder');
		$controllers = JFolder::files(JPATH_COMPONENT_SITE.'/controllers', '.php$');
		foreach($controllers as $controller)
		{
			$name = str_replace('.php', '', $controller);
			$this->controllers[$name] = 'site::com.ninjaboard.controller.'.$name;
		}
		
		return parent::display();
	}
	
	public function _createToolbar()
	{
		$identifier	= $this->getToolbar();
		$name		= $identifier->name;
		$package	= $identifier->package;
		
		$this->css('/toolbar.css');
		$img = KInflector::isPLural($name) 
						? $this->img('/48/'.$name.'.png')
						: $this->img('/48/'.KInflector::pluralize($name).'.png');
		if(!$img)
		{
			$img = KInflector::isSingular($name) 
						? $this->img('/48/'.$name.'.png')
						: $this->img('/48/'.KInflector::singularize($name).'.png');
		}
		if($img) 
		{
			$this->css('.header.icon-48-'.$name.' { background-image: url(' . $img . '); }');
		}
		return KFactory::get($identifier, array('icon' => $name));
	}
}