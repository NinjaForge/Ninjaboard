<?php
/**
 * @version $Id: view.html.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Ninjaboard Latest Posts View
 *
 * @package Ninjaboard
 */
class NinjaboardViewLatestPosts extends JView
{

	function display($tpl = null) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
		
		$latestPostLinks = array();
		$latestPostLinks = $this->_getLatestPostOptions('hours');
		$latestPostLinks = array_merge($latestPostLinks, $this->_getLatestPostOptions('days'));
		$latestPostLinks = array_merge($latestPostLinks, $this->_getLatestPostOptions('weeks'));
		$latestPostLinks = array_merge($latestPostLinks, $this->_getLatestPostOptions('months'));
		$latestPostLinks = array_merge($latestPostLinks, $this->_getLatestPostOptions('years'));

		$this->assignRef('latestPostLinks', $latestPostLinks);
		
		$hours = JRequest::getVar('hours', 2, '', 'int');
		$this->assignRef('hours', $hours);
		
		$model	=& $this->getModel();
		$posts	= new NinjaboardPost($model->getLatestPosts());
		$this->assignRef('posts', $posts);

		// request variables
		$limit		= JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('posts_per_page'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// enable latest posts filter
		$this->assignRef('enableFilter', $ninjaboardConfig->getLatestPostSettings('enable_filter'));
		
		$total = count($posts->posts);
		$this->assignRef('total', $total);
	
		$showPagination = false;
		if ($total > $limit) {
			$showPagination = true;
		}
		$this->assign('showPagination', $showPagination);

		jimport('joomla.html.pagination');
		$this->pagination = new JPagination($total, $limitstart, $limit);

		// handle page title
		$document->setTitle(JText::_('NB_LATESTPOSTS'));
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_('NB_LATESTPOSTS'), '');

		parent::display($tpl);
	}
	
	function _getLatestPostOptions($var) {
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		
		$latestPostLinks = array();
		
		$array = explode(',', $ninjaboardConfig->getLatestPostSettings('latest_post_'.$var));
		foreach ($array as $value) {
			$latestPostLink = new StdClass();
			$latestPostLink->href = 'index.php?option=com_ninjaboard&view=latestposts&'.$var.'='.$value.'&Itemid='.$this->Itemid;
			$latestPostLink->text = $value .' '. $this->_getLatestPostOptionName($var, $value);
			$latestPostLinks[] = $latestPostLink;
		}
		
		return $latestPostLinks;	
	}
	
	function _getLatestPostOptionName($var, $value) {
		$optionName = '';
		
		switch ($var) {
			case 'hours':
				$optionName = ($value == 1) ? JText::_('NB_HOUR') : JText::_('NB_HOURS');
				break;
			case 'days':
				$optionName = ($value == 1) ? JText::_('NB_DAY') : JText::_('NB_DAYS');
				break;
			case 'weeks':
				$optionName = ($value == 1) ? JText::_('NB_WEEK') : JText::_('NB_WEEKS');
				break;
			case 'months':
				$optionName = ($value == 1) ? JText::_('NB_MONTH') : JText::_('NB_MONTHS');
				break;
			case 'years':
				$optionName = ($value == 1) ? JText::_('NB_YEAR') : JText::_('NB_YEARS');
				break;
			default:
				$optionName = $var;
				break;
		}
		
		return $optionName;	
	}

}
?>