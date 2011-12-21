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
 * Ninjaboard Who`s Online View
 *
 * @package Ninjaboard
 */
class NinjaboardViewWhosOnline extends JView
{

	function display($tpl = null) {
		global $mainframe;
		
		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
		
		// request variables
		$limit		= JRequest::getVar('limit', 20, '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		
		// handle page title
		$document->setTitle(JText::_('NB_WHOSONLINE'));

		// set data model
		$onlineUsers =& $this->get('onlineusers');
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_('NB_WHOSONLINE'), '');

		$total = count($onlineUsers);
		
		$showPagination = false;
		if ($total > $limit) {
			$showPagination = true;
		}
		$this->assign('showPagination', $showPagination);
				
		jimport('joomla.html.pagination');
		$this->pagination = new JPagination($total, $limitstart, $limit);
				
		$this->assignRef('onlineUsers', $onlineUsers);
		$this->assignRef('total', $total);		

		parent::display($tpl);
	}

	function &getOnlineUser($index = 0) {
		global $mainframe;
		
		$onlineUser =& $this->onlineUsers[$index];
		
		$onlineUser->actionTime = NinjaboardHelper::Date($onlineUser->action_time);

		$onlineUser->userLink = '';
		if ($onlineUser->name) {
			$onlineUser->userLink = JRoute::_('index.php?option=com_ninjaboard&view=profile&id='.$onlineUser->id_user.'&Itemid='.$this->Itemid);
		} else {
			$onlineUser->name = JText::_('NB_GUEST');
		}

		return $onlineUser;
	}
	
}
?>