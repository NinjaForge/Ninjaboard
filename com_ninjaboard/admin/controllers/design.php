<?php
/**
 * @version $Id: design.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'design.php');

/**
 * Ninjaboard Design Controller
 *
 * @package Ninjaboard
 */
class ControllerDesign extends JController
{

	/**
	 * compiles a list of designs
	 */
	function showDesigns() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		
		$context			= 'com_ninjaboard.ninjaboard_design_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 'd.name');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_designs AS d"
				. $orderby
				;		
		$db->setQuery($query);
		$total = $db->loadResult();
		
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
													
		$query = "SELECT d.*"
				. "\n FROM #__ninjaboard_designs AS d"
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// get the design components
		jimport( 'joomla.filesystem.folder' );		

		for ($i=0; $i < count($rows); $i++) {
			if(!$data = NinjaboardHelper::parseXMLFile(NB_TEMPLATES.DS.basename($rows[$i]->template, ".xml"), $rows[$i]->template, 'template')) {
				continue;
			} else {
				$rows[$i]->template_name = $data->name;
			}
			
			if(!$data = NinjaboardHelper::parseXMLFile(NB_STYLES.DS.basename($rows[$i]->style, ".xml"), $rows[$i]->style, 'style')) {
				continue;
			} else {
				$rows[$i]->style_name = $data->name;
			}
			
			if(!$data = NinjaboardHelper::parseXMLFile(NB_EMOTICONS.DS.basename($rows[$i]->emoticon_set, ".xml"), $rows[$i]->emoticon_set, 'emoticonset')) {
				continue;
			} else {
				$rows[$i]->emoticon_set_name = $data->name;
			}
			
			if(!$data = NinjaboardHelper::parseXMLFile(NB_BUTTONS.DS.basename($rows[$i]->button_set, ".xml"), $rows[$i]->button_set, 'buttonset')) {
				continue;
			} else {
				$rows[$i]->button_set_name = $data->name;
			}

			if(!$data = NinjaboardHelper::parseXMLFile(NB_ICONS.DS.basename($rows[$i]->icon_set, ".xml"), $rows[$i]->icon_set, 'iconset')) {
				continue;
			} else {
				$rows[$i]->icon_set_name = $data->name;
			}
		}
				
		// parameter list
		$lists = array();
		
		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
		
		ViewDesign::showDesigns($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit design operation
	 */
	function cancelEditDesign() {
		$app	=& JFactory::getApplication();
		
		// check in design so other can edit it
		$row =& JTable::getInstance('NinjaboardDesign');
		$row->bind(JRequest::get('post'));
		$row->checkin();

		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_design_view';
		$app->redirect( $link );
	}
	
	/**
	 * edit the design
	 */
	function editDesign() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db   	=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid 	=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}

		$row =& JTable::getInstance('NinjaboardDesign');
		$row->load($cid[0]);

		// is someone else editing this design?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_design_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_DESIGN'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out design so nobody else can edit it
		$row->checkout($user->get('id'));
	
		// we need it for template lists etc. 
		jimport( 'joomla.filesystem.folder' );

		// parameter list
		$lists = array();

		// list template files
		$templatesList = JFolder::folders(NB_TEMPLATES);
				
		$templates = array();
		foreach ($templatesList as $templateFolder) {
			$fileList = JFolder::files(NB_TEMPLATES.DS.$templateFolder, '.xml');
			foreach ($fileList as $templateFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_TEMPLATES.DS.$templateFolder, $templateFile, 'template')) {
					continue;
				} else {
					$templates[] = JHTML::_('select.option', $data->file_name, $data->name);
				}
			}
		}
		$lists['templates'] = JHTML::_('select.genericlist',  $templates, 'template', 'class="inputbox" size="1"', 'value', 'text', $row->template);

		// list style files
		$stylesList = JFolder::folders(NB_STYLES);
				
		$styles = array();
		foreach ($stylesList as $styleFolder) {
			$fileList = JFolder::files(NB_STYLES.DS.$styleFolder, '.xml');
			foreach ($fileList as $styleFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_STYLES.DS.$styleFolder, $styleFile, 'style')) {
					continue;
				} else {
					$styles[] = JHTML::_('select.option', $data->file_name, $data->name);
				}
			}
		}
		$lists['styles'] = JHTML::_('select.genericlist',  $styles, 'style', 'class="inputbox" size="1"', 'value', 'text', $row->style);
	
		// list emoticon sets
		$emoticonSetsList = JFolder::folders(NB_EMOTICONS);
				
		$emoticonSets = array();
		foreach ($emoticonSetsList as $emoticonSetFolder) {
			$fileList = JFolder::files(NB_EMOTICONS.DS.$emoticonSetFolder, '.xml');
			foreach ($fileList as $emoticonSetFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_EMOTICONS.DS.$emoticonSetFolder, $emoticonSetFile, 'emoticonset')) {
					continue;
				} else {
					$emoticonSets[] = JHTML::_('select.option', $data->file_name, $data->name);
				}
			}
		}
		$lists['emoticonsets'] = JHTML::_('select.genericlist',  $emoticonSets, 'emoticon_set', 'class="inputbox" size="1"', 'value', 'text', $row->emoticon_set);		

		// list button sets
		$buttonSetsList = JFolder::folders(NB_BUTTONS);
				
		$buttonSets = array();
		foreach ($buttonSetsList as $buttonSetFolder) {
			$fileList = JFolder::files(NB_BUTTONS.DS.$buttonSetFolder, '.xml');
			foreach ($fileList as $buttonSetFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_BUTTONS.DS.$buttonSetFolder, $buttonSetFile, 'buttonset')) {
					continue;
				} else {
					$buttonSets[] = JHTML::_('select.option', $data->file_name, $data->name);
				}
			}
		}
		$lists['buttonsets'] = JHTML::_('select.genericlist',  $buttonSets, 'button_set', 'class="inputbox" size="1"', 'value', 'text', $row->button_set);
		
		// list icon sets
		$iconSetsList = JFolder::folders(NB_ICONS);
				
		$iconSets = array();
		foreach ($iconSetsList as $iconSetFolder) {
			$fileList = JFolder::files(NB_ICONS.DS.$iconSetFolder, '.xml');
			foreach ($fileList as $iconSetFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_ICONS.DS.$iconSetFolder, $iconSetFile, 'iconset')) {
					continue;
				} else {
					$iconSets[] = JHTML::_('select.option', $data->file_name, $data->name);
				}
			}
		}
		$lists['iconsets'] = JHTML::_('select.genericlist',  $iconSets, 'icon_set', 'class="inputbox" size="1"', 'value', 'text', $row->icon_set);

		ViewDesign::editDesign($row, $lists);			
	}
	
	/**
	 * save the design
	 */	
	function saveDesign($task) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db 	=& JFactory::getDBO();
		$row 	=& JTable::getInstance('NinjaboardDesign');
		$post	=  JRequest::get('post');

		if (!$row->bind($post)) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$row->checkin();

		switch ($task) {
			case 'ninjaboard_design_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_design_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;
			case 'ninjaboard_design_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_design_view';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_DESIGN'), $row->name);
		$app->redirect( $link, $msg );
	}
	
	/**
	 * delete the design
	 */		
	function deleteDesign() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);

		if (count($cid)) {
			
			// how many categories are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardDesign'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_DESIGN'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_DESIGNS'), '');
			}
		
			$query = "DELETE FROM #__ninjaboard_designs"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_DESIGN'), JText::_('NB_MENUBARDELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_design_view', $msg, $msgType);
	}
	
	/**
	 * set design as default
	 */	
	function defaultDesign() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);

		if (count($cid)) {

			// set the default design to true
			NinjaboardHelper::setDefaultDesign($cid[0]);
			
			// set the default design to the default config	
			$query = "UPDATE #__ninjaboard_configs"
					. "\n SET id_design = $cid[0]"
					. "\n WHERE default_config = 1"
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_DESIGN'), JText::_('NB_MENUBARDEFAULT'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_design_view', $msg, $msgType);
	}
}
?>
