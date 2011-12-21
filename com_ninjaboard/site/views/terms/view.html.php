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
 * Ninjaboard Terms View
 *
 * @package Ninjaboard
 */
class NinjaboardViewTerms extends JView
{

	function display($tpl = null) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		
		$terms = $this->get('terms');
		if (!$terms->id) {
			$messageQueue->addMessage(JText::_('NB_MSGNOTERMS'));
			$redirect = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$this->Itemid);
			$mainframe->redirect($redirect);
			return;	
		}
		
		$terms->agreementtext = str_replace('{agreed}', JHTML::_('select.booleanlist', 'agreed', 'class="inputbox validate-agreed"', 0), $terms->agreementtext);
		$this->assignRef('terms', $terms);
		
		// load form validation behavior
		JHTML::_('behavior.formvalidation');
		
		// handle page title
		$document->setTitle($terms->terms);
		
		// handle metadata
		$document->setDescription($ninjaboardConfig->getBoardSettings('description'));
		$document->setMetadata('keywords', $ninjaboardConfig->getBoardSettings('keywords'));
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_($terms->terms), '');
		
		$showAgreement = 1;
		if ($this->ninjaboardUser->get('id') || !$this->allowUserRegistration) {
			$showAgreement = 0;
		}
		$this->assign('showAgreement', $showAgreement);
		
		if ($showAgreement) {
			$action = JRoute::_('index.php?option=com_ninjaboard&view=register&Itemid='. $this->Itemid);
			$this->assignRef('action', $action);
			
			// get buttons
			$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
			$this->assignRef('buttonRegister', $ninjaboardButtonSet->buttonByFunction['buttonRegister']);
		}
				
		parent::display($tpl);
	}

}
?>