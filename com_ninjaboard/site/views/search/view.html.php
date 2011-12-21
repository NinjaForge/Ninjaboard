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
 * Ninjaboard Search View
 *
 * @package Ninjaboard
 */
class NinjaboardViewSearch extends JView
{

	function display($tpl = null) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();

		$searchWords = JRequest::getVar('searchwords', '');
		$this->assignRef('searchWords', $searchWords);

		$model	=& $this->getModel();
		$posts	= new NinjaboardPost($model->getSearchResults());
		$this->assignRef('posts', $posts);

		// request variables
		$limit		= JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('search_results_per_page'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		
		$total = count($posts->posts);
		$this->assignRef('total', $total);
		
		$showPagination = false;
		if ($total > $limit) {
			$showPagination = true;
		}
		$this->assign('showPagination', $showPagination);
			
		jimport('joomla.html.pagination');
		$this->pagination = new JPagination($total, $limitstart, $limit);

		// load form validation behavior
		JHTML::_('behavior.formvalidation');
		
		// handle page title
		$document->setTitle(JText::_('NB_SEARCH'));
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_('NB_SEARCH'), '');
		
		$action = 'index.php?option=com_ninjaboard&view=search&Itemid='. $this->Itemid;
		$this->assignRef('action', $action);
				
		// get buttons
		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		$this->assignRef('buttonSearch', $ninjaboardButtonSet->buttonByFunction['buttonSearch']);
		$this->assignRef('actionSearch', JRoute::_('index.php?option=com_ninjaboard&view=search&Itemid='.$this->Itemid));
		
		parent::display($tpl);
	}

}
?>