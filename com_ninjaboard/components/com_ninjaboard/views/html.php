<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1384 2011-01-11 13:56:29Z stian $
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
		$this->js('/site.js');
		$this->assign('dateformat', str_replace(array('%A', '%B'), array('%a', '%b'), JText::_('DATE_FORMAT_LC2')));
		$timezone = KFactory::tmp('lib.joomla.user')->getParam('timezone');
		if(is_null($timezone)) $timezone = KFactory::get('lib.joomla.config')->getValue('offset');
		$this->assign('timezone', $timezone);
		
		//Autoload breadcrumbs module when needed
		//@TODO optimize so it's not made available all the time, only when needed
		if(KRequest::type() != 'AJAX' && KRequest::get('get.format', 'cmd', 'html') == 'html')
		{
			$this->_loadModBreadcrumbs();
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
		$modules = KFactory::tmp('admin::com.ninja.model.joomla.modules');

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

			$modules->append(KFactory::get('admin::com.ninja.helper.module')->create(array(
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
		$pathway = KFactory::get('lib.koowa.application')->getPathWay();
		$menu	 = JSite::getMenu()->getActive()->query;
		
		//@TODO Don't add a pathway item that's a duplicate of something else
		//if()
		
		$pathway->addItem($this->getDocumentSubtitle(), $this->createRoute());
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