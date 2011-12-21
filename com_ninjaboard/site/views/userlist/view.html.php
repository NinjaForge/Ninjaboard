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
 * Ninjaboard User List View
 *
 * @package Ninjaboard
 */
class NinjaboardViewUserList extends JView
{

	function display($tpl = null) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardAuth		=& NinjaboardAuth::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
		
		$this->assignRef('latestPostLinks', $latestPostLinks);

		$ninjaboardUsers =& $this->get('ninjaboardusers');
		$this->assignRef('ninjaboardUsers', $ninjaboardUsers);

		// request variables
		$limit		= JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('items_per_page'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		
		$total = $this->get('totalmembers');
		$this->assignRef('total', $total);
		
		$showPagination = false;
		if ($total > $limit) {
			$showPagination = true;
		}
		$this->assign('showPagination', $showPagination);
				
		jimport('joomla.html.pagination');
		$this->pagination = new JPagination($total, $limitstart, $limit);

		// handle page title
		$document->setTitle(JText::_('NB_USERLIST'));
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_('NB_USERLIST'), '');
		
		// ninjaboard user roles
		$this->assignRef('roles', $ninjaboardAuth->getUserRoleList());
	
		parent::display($tpl);
	}
	
	function &getNinjaboardUser($index) {

		// initialize variables		
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
				
		$ninjaboardUser =& $this->ninjaboardUsers[$index];
		$ninjaboardUser->mainRole = $this->roles[$ninjaboardUser->role];
		$ninjaboardUser->registerDate = NinjaboardHelper::Date($ninjaboardUser->registerDate);
		$ninjaboardUser->userLink = JRoute::_('index.php?option=com_ninjaboard&view=profile&id='.$ninjaboardUser->id.'&Itemid='.$this->Itemid);

		return $ninjaboardUser;
	}

}
?>