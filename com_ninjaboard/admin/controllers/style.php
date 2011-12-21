<?php
/**
 * @version $Id: style.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Style Controller
 *
 * @package Ninjaboard
 */
class NinjaBoardControllerStyle extends JController
{

	/**
	 * Display a list of styles
	 */
	function display()
	{
		$view = JRequest::getVar('view');
		
		if (!$view)
			JRequest::setVar('view', 'styleall');
		
		parent::display();
	}
	
	/**
	 * cancels edit style operation
	 *
	function cancelEditStyle() {
        $app =& JFactory::getApplication();
		$link = 'index.php?option=com_ninjaboard&task=display&controller=style';
		$app->redirect($link);
	}*/
	
	/**
	 * edit the style
	 */
	function edit()
	{
		JRequest::setVar('view', 'stylesingle');
		$this->display();
	}
	 
	/**
	 * save the style
	 */	
	function save() {
		
		// initialize variables
        $app        =& JFactory::getApplication();
		$file_name	=  JRequest::getVar('file_name');
		
		if(!$style = NinjaboardHelper::parseXMLFile(NB_STYLES.DS.basename($file_name, ".xml"), $file_name, 'style')) {
			$link = 'index.php?option=com_ninjaboard&task=display&controller=style';
			$msg  = sprintf(JText::_('NB_MSGFILENOTFOUND'), $file_name);
			$app->redirect($link, $msg);
		}
		
		// set the default style		
		if (JRequest::getVar('defaultstyle')) {
			NinjaboardHelper::setDefaultStyle($style->file_name);			
		}
								
		switch ($task) {
			case 'apply':
				$link = 'index.php?option=com_ninjaboard&task=edit&controller=style&cid[]='. $style->file_name .'&hidemainmenu=1';
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=display&controller=style';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_STYLE'), $style->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the style
	 *
	function remove() {
        $app =& JFactory::getApplication();
		
		// ToDo: Source for delete style

		$app->redirect('index.php?option=com_ninjaboard&task=display&controller=style');
	}*/
	
	/**
	 * set style as default
	 */	
	function setDefault() {

		// initialize variables
        $app =& JFactory::getApplication();
		$cid =  JRequest::getVar('cid', array(), 'post', 'array');

		if (count($cid)) {

			// set the selected style to default
			NinjaboardHelper::setDefaultStyle($cid[0]);	
		}

		$app->redirect('index.php?option=com_ninjaboard&task=display&controller=style');
	}		
}
?>
