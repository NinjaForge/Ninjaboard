<?php
/**
 * @version $Id: buttonset.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

jimport('joomla.application.component.controller');

/**
 * Ninjaboard Button Set Controller
 *
 * @package Ninjaboard
 */
class NinjaBoardControllerButtonSet extends JController
{

	/**
	 * displays a list of button sets
	 */
	function display()
	{
		$view = JRequest::getVar('view');
		
		if (!$view) {
			JRequest::setVar('view', 'buttonsetall');
		}
		
		parent::display();
	}
	
	/**
	 * cancels edit button set operation
	 *
	function cancelEditButtonSet() {
		$app	=& JFactory::getApplication();
		$link	=  'index.php?option=com_ninjaboard&task=view&controller=buttonset';
		$app->redirect($link);
	}*/
	
	/**
	 * edit the button set
	 */
	function edit()
	{
		JRequest::setVar('view', 'buttonsetsingle');
		$this->display();
	}
	
	/**
	 * save the button set
	 * This function is not in use yet
	 *
	function save() {

		// initialize variables
		$app	=& JFactory::getApplication();
		$post	= JRequest::get('post');

		// ToDo: implement code saving button set

		switch ($task) {
			case 'ninjaboard_buttonset_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_buttonset_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;

			case 'ninjaboard_buttonset_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_buttonset_view';
				break;
		}

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_BUTTONSET'), $row->name);
		$app->redirect($link, $msg );
	}*/
	
	/**
	 * delete the button set
	 * This function is not in use yet
	 *
	function deleteButtonSet() {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$cid	=  JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		// ToDo: implement code deleting button set

		$app->redirect('index.php?option=com_ninjaboard&task=display&controller=buttonset', $db->getErrorMsg(), 'error');
	}*/
	
	/**
	 * set button set as default
	 */	
	function setDefault() {

		// initialize variables
		$app	=& JFactory::getApplication();
		$cid	=  JRequest::getVar('cid', array(), 'post', 'array');

		if (count($cid)) {

			// set the default button set to true
			NinjaboardHelper::setDefaultButtonSet($cid[0]);
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_BUTTONSET'), JText::_('NB_DEFAULT'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=view&controller=buttonset', $msg);
	}
	
}
?>
