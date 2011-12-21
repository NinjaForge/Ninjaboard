<?php
/**
 * @version $Id: iconset.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Ninjaboard Icon Set Controller
 *
 * @package Ninjaboard
 */
class NinjaBoardControllerIconSet extends JController
{

	/**
	 * Show a list of icon sets
	 */
	function display()
	{
		$view = JRequest::getVar('view');
		
		if (!$view) {
			JRequest::setVar('view', 'iconsetall');
		}
		
		parent::display();
	}

	
	/**
	 * cancels edit icon set operation
	 *
	function cancel() {
		$app =& JFactory::getApplication();
		$link = 'index.php?option=com_ninjaboard&task=display&controller=iconset';
		$app->redirect($link);
	}*/
	
	/**
	 * edit the icon set
	 */
	function edit()
	{
		JRequest::setVar('view', 'iconsetsingle');
		$this->display();
	}
	
	/**
	 * save the icon set
	 *
	function save() {

		// initialize variables
		$app =& JFactory::getApplication();
		$db  =& JFactory::getDBO();

		$post	= JRequest::get('post');

		// ToDo: implement code saving icon set

		switch($task) {
			case 'apply':
				$link = 'index.php?option=com_ninjaboard&task=edit&controller=iconset&cid[]='. $row->id .'&hidemainmenu=1';
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_iconset_view';
				break;
		}

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_ICONSET'), $row->name);
		$app->redirect($link, $msg);
	}*/
	
	/**
	 * delete the icon set
	 *
	function remove() {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$cid	=  JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		// ToDo: implement code deleting icon set

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_iconset_view', $db->getErrorMsg(), 'error');
	}*/
	
	/**
	 * set icon set as default
	 */	
	function setDefault() {

		// initialize variables
		$app =& JFactory::getApplication();
		$cid =  JRequest::getVar('cid', array(), 'post', 'array');

		if(count($cid)) {

			// set the default icon set to true
			NinjaboardHelper::setDefaultIconSet($cid[0]);
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_ICONSET'), JText::_('NB_DEFAULT'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=display&controller=iconset', $msg);
	}
	
}
?>
