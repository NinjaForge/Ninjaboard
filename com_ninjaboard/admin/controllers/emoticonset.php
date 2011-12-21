<?php
/**
 * @version $Id: emoticonset.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'emoticonset.php');

/**
 * Ninjaboard Emoticon Set Controller
 *
 * @package Ninjaboard
 */
class NinjaBoardControllerEmoticonSet extends JController
{

	/**
	 * compiles a list of emoticon sets
	 */
	function display()
	{
		$view = JRequest::getVar('view');
		
		if (!$view) {
			JRequest::setVar('view', 'emoticonsetall');
		}
		
		parent::display();
	}
	
	/**
	 * edit the emoticon set
	 */
	function edit()
	{
		JRequest::setVar('view', 'emoticonsetsingle');
		$this->display();
	}
	
	/**
	 * cancels edit emoticon set operation
	 *
	function cancelEditEmoticonSet() {
		$app	=& JFactory::getApplication();
		$link	=  'index.php?option=com_ninjaboard&task=display&controller=emoticonset';
		$app->redirect($link);
	}*/
	
	/**
	 * save the emoticon set
	 *
	function save() {

		// initialize variables
		$app	=& JFactory::getApplication();
		$post	=  JRequest::get('post');
		
		// ToDo: implement code saving emoticon set
		
		switch ($task) {
			case 'apply':
				$link = 'index.php?option=com_ninjaboard&task=edit&controller=emoticonset&cid[]='. $row->id .'&hidemainmenu=1';
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=display&controller=emoticonset';
				break;
		}
		
		$msg = sprintf(JText::_('Successfully Saved Emoticon-Set: %s'), $row->name);
		$app->redirect($link, $msg);
	}*/
	
	/**
	 * delete the emoticon set
	 *
	function remove() {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$cid	=  JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		// ToDo: implement code deleting emoticon set

		$app->redirect('index.php?option=com_ninjaboard&task=display&controller=emoticonset', $db->getErrorMsg(), 'error');
	}*/
	
	/**
	 * set emoticon set as default
	 */		
	function setDefault() {

		// Initialize variables
		$app	=& JFactory::getApplication();
		$cid	=  JRequest::getVar('cid', array(), 'post', 'array');

		if (count($cid)) {

			// set the default emoticon set to true
			NinjaboardHelper::setDefaultEmoticonSet($cid[0]);
		}

		$app->redirect('index.php?option=com_ninjaboard&task=display&controller=emoticonset');
	}
	
}
?>
