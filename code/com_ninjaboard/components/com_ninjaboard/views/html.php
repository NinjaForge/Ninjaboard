<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewHtml extends ComNinjaboardViewDefault
{
	/**
	 * An boolean to disable the toolbar when needed
	 *
	 * @var boolean
	 */
	protected $_toolbar = false;
	
	/**
	 * Set the page title in the dispatcher instead of the view, for better HMVC support
	 *
	 * @var boolean
	 */
	public $_auto_title = false;

	public function display()
	{	
		$this->assign('dateformat', str_replace(array('%A', '%B'), array('%a', '%b'), JText::_('DATE_FORMAT_LC2=')));
		$timezone = JFactory::getUser()->getParam('timezone');
		if(is_null($timezone)) $timezone = JFactory::getConfig()->getValue('offset');
		$this->assign('timezone', $timezone);
		
		//Autoload breadcrumbs module when needed
		//@TODO optimize so it's not made available all the time, only when needed
		if(KRequest::type() != 'AJAX' && KRequest::get('get.format', 'cmd', 'html') == 'html')
		{
			if(!version_compare(JVERSION,'1.6.0','ge')) $this->_loadModBreadcrumbs();
		}
		
		return parent::display();
	}

	/**
	 * Checks wether the mod_breadcrumbs module is enabled on page
	 *
	 * If it's not, then it'll instantiate it, and render it in either the 'breadcrumbs'
	 * or 'breadcrumb' module position, depending on what's available
	 *
	 * @return $this
	 */
	protected function _loadModBreadcrumbs()
	{
		$modules = $this->getService('ninja:model.modules');

		if(!$modules->module('mod_breadcrumbs')->count())
		{
			//We need to check wether to use 'breadcrumbs' or 'breadcrumb'
			$positions = $modules->getPositions();

			if(in_array('breadcrumbs', $positions))
			{
				$position = 'breadcrumbs';
			}
			elseif(in_array('breadcrumb', $positions))
			{
				$position = 'breadcrumb';
			}
			else
			{
				//If neither, then we don't know where to load the breadcrumbs
				return $this;
			}

			$modules->append($this->getService('ninja:helper.module')->create(array(
				'title'		=> 'Breadcrumbs',
				'position'	=> $position,
				'module'	=> 'mod_breadcrumbs',
				'showtitle'	=> false,
				'params'	=> 'showHome=0'
			)));
		}
		
		return $this;
	}

	/**
	 * Method suitable for callbacks that sets the breadcrumbs according to this view context
	 *
	 * @return void
	 */
	public function setBreadcrumbs()
	{
		$app		= JFactory::getApplication();
		$pathway 	= $app->getPathWay();
		$menu	 	= $app->getMenu()->getActive()->query;
		
		if (!KInflector::isPlural($this->getName()) && $menu['view'] != 'forum') $pathway->addItem($this->getDocumentSubtitle(), $this->createRoute());
	}

	/**
	 * Function for getting a plural and human readable version of a name
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  bool $pluralize	Wether to pluralize or not
	 */
	public function getReadableName($pluralize = true)
	{
		$name = $this->getName();
		if($pluralize && KInflector::isSingular($name)) $name = KInflector::pluralize($name);
		return KInflector::humanize($name);
	}
}