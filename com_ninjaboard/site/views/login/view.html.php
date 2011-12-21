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
 * Ninjaboard Login View
 *
 * @package Ninjaboard
 */
class NinjaboardViewLogin extends JView
{

	function display( $tpl = null ) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();

		// load form validation behavior
		JHTML::_('behavior.formvalidation');
		
		// handle page title
		$document->setTitle(JText::_('NB_LOGIN'));
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_('NB_LOGIN'), '');
		
		$action = JRoute::_('index.php?option=com_ninjaboard&task=ninjaboardlogin&Itemid='.$this->Itemid);
		$this->assignRef('action', $action);

		// get buttons
		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		$this->assignRef('buttonLogin', $ninjaboardButtonSet->buttonByFunction['buttonLogin']);		
		
		parent::display($tpl);
	}

}
?>