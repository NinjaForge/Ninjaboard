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
 * Ninjaboard Information View
 *
 * @package Ninjaboard
 */
class NinjaboardViewInformation extends JView
{

	function display($tpl = null) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();

		$ninjaboardUser = NinjaboardUser::getInstance(JRequest::getVar('user', 0));
		$this->assignRef('ninjaboardUser', $ninjaboardUser);
		
		$info = JRequest::getVar('info');
		$this->assignRef('info', $info);

		// handle page title
		$document->setTitle(JText::_('NB_INFORMATION'));
		
		// handle metadata
		$document->setDescription($ninjaboardConfig->getBoardSettings('description'));
		$document->setMetadata('keywords', $ninjaboardConfig->getBoardSettings('keywords'));
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_('NB_INFORMATION'), '');
		
		$this->assignRef('boardIndexLink', JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$this->Itemid));
		$this->assignRef('loginLink', JRoute::_('index.php?option=com_ninjaboard&view=login&Itemid='.$this->Itemid));
		$this->assignRef('requestLoginLink', JRoute::_('index.php?option=com_ninjaboard&view=requestlogin&Itemid='.$this->Itemid));		

		parent::display($tpl);
	}
	
	function loadInformation() {
		global $mainframe;
		
		// initialize variables
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$infoFile 		= $this->templatePath.DS.'information'.DS;
		
		switch($this->info) {
			case 'account_activation':
				switch($ninjaboardConfig->getBoardSettings('account_activation')) {
					case 0:		// no activation needed
						$infoFile .= $this->info.'_no.php';
						break;
					case 1:		// activation by user
						$infoFile .= $this->info.'_user.php';
						break;
					case 2:		// activation by admin
						$infoFile .= $this->info.'_admin.php';
						break;				
				}			
				break;
			default:
				$infoFile .= $this->info.'.php';
				break;				 
		}

		jimport('joomla.filesystem.file');
		if (JFile::exists($infoFile)) {
			include $infoFile;
		} else {
			JError::raiseError(500, JText::sprintf('NB_MSGFILENOTFOUND', $infoFile)); 
		}
	}
	
}
?>