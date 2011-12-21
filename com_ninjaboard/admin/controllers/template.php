<?php defined('_JEXEC') or die('Restricted access');
/**
 * @version $Id: template.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// Load teplate view.
require_once(JPATH_COMPONENT.DS.'views'.DS.'template.php');

/**
 * Ninjaboard Template Controller
 *
 * @package Ninjaboard
 */
class NinjaBoardControllerTemplate extends JController
{

	/**
	 * compiles a list of templates
	 */
	function display()
	{
		$view = JRequest::getVar('view');
		
		if (!$view)
			JRequest::setVar('view', 'templateall');

		parent::display();
	}
		
	/**
	 * cancels edit template operation
	 *
	function cancelEditTemplate() {
		global $mainframe;
		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_template_view';
		$mainframe->redirect($link);
	}*/
	
	/**
	 * edit the template
	 */
	function edit() {
		JRequest::setVar('view', 'templatesingle');
		$this->display();
	}
	
	/**
	 * save the template
	 */	
	function save() {
		
		// initialize variables
		$app		=& JFactory::getApplication();
		$file_name	=  JRequest::getVar('file_name');
		
		if(!$template = NinjaboardHelper::parseXMLFile(NB_TEMPLATES.DS.basename($file_name, ".xml"), $file_name, 'template')) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_template_view';
			$msg  = sprintf(JText::_('NB_MSGFILENOTFOUND'), $file_name);
			$app->redirect($link, $msg.'test'.$file_name);
		}
		
		// set the default template		
		if (JRequest::getVar('defaulttemplate')) {
			NinjaboardHelper::setDefaultTemplate($template->file_name);			
		}
								
		switch ($task) {
			case 'ninjaboard_template_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_template_edit&cid[]='. $template->file_name .'&hidemainmenu=1';
				break;

			case 'ninjaboard_template_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_template_view';
				break;
		}

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_TEMPLATE'), $template->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the template
	 *
	function deleteTemplete() {
		global $mainframe;
		
		// ToDo: Source for delete the template
		
		$mainframe->redirect('index.php?option=com_ninjaboard&task=ninjaboard_template_view');
	}*/
	
	/**
	 * set template as default
	 */	
	function setDefault() {

		// initialize variables
		$app =& JFactory::getApplication();
		$cid =  JRequest::getVar('cid', array(), 'post', 'array');

		if (count($cid)) {

			// set the selected template to default
			NinjaboardHelper::setDefaultTemplate($cid[0]);	
		}

		$app->redirect('index.php?option=com_ninjaboard&controller=template&amp;task=view');
	}
		
}
?>
