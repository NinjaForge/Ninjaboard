<?php
/**
 * @version $Id: timeformat.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'timeformat.php');

/**
 * Ninjaboard Time Format Controller
 *
 * @package Ninjaboard
 */
class ControllerTimeFormat extends JController
{

	/**
	 * compiles a list of time formats
	 */
	function showTimeFormats() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		
		$context			= 'com_ninjaboard.ninjaboard_timeformat_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 'f.name');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_timeformats AS f"
				. $orderby
				;
		$db->setQuery($query);
		$total = $db->loadResult();
		
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
												
		$query = "SELECT f.*, (c.id_timeformat = f.id) AS default_timeformat"
				. "\n FROM #__ninjaboard_timeformats AS f"
				. "\n LEFT JOIN #__ninjaboard_configs AS c ON c.id_timeformat = f.id AND c.default_config = 1"
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// parameter list
		$lists = array();
		
		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
		
		ViewTimeFormat::showTimeFormats($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit time format operation
	 */
	function cancelEditTimeFormat() {

		$app	=& JFactory::getApplication();
		
		// check in category so other can edit it
		$row =& JTable::getInstance('NinjaboardTimeFormat');
		$row->bind(JRequest::get('post'));
		$row->checkin();
		
		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timeformat_view';
		$app->redirect($link);
	}
	
	/**
	 * edit the time format
	 */
	function editTimeFormat() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid 	=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}
	
		$row =& JTable::getInstance('NinjaboardTimeFormat');
		$row->load($cid[0]);

		// is someone else editing this time format?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timeformat_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_TIMEFORMAT'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out category so nobody else can edit it
		$row->checkout($user->get('id'));
		
		// parameter list
		$lists = array();
		
		// build the html radio buttons for state
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $row->published);		
				
		ViewTimeFormat::editTimeFormat($row, $lists);			
	}
	
	/**
	 * save the time format
	 */	
	function saveTimeFormat($task) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db     =& JFactory::getDBO();
		$post	=  JRequest::get('post');
		$row    =& JTable::getInstance('NinjaboardTimeFormat');

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
			case 'ninjaboard_timeformat_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timeformat_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;
			case 'ninjaboard_timeformat_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timeformat_view';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_TIMEFORMAT'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the time format
	 */	
	function deleteTimeFormat() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
			
			// how many categories are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardTimeFormat'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_TIMEFORMAT'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_TIMEFORMATS'), '');
			}
		
			$query = "DELETE FROM #__ninjaboard_timeformats"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_TIMEFORMAT'), JText::_('NB_DELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_timeformat_view', $msg, $msgType);
	}
	
	/**
	 * changes the publish state of a time format
	 * @param integer 0 = unpublishing, 1 = publishing
	 */
	function changeTimeFormatPublishState($state = 0) {
		
		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
			$msgText = ($state == 1) ? 'NB_MSGSUCCESSFULLYPUBLISHED' : 'NB_MSGSUCCESSFULLYUNPUBLISHED';
			
			// are there one or more rows to publish/unpublish?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardTimeFormat'); $row->load($cid[0]);
				$msg = JText::sprintf($msgText, JText::_('NB_TIMEFORMAT'), $row->name);
			} else {
				$msg = JText::sprintf($msgText, JText::_('NB_TIMEFORMATS'), '');
			}
	
			$query = "UPDATE #__ninjaboard_timeformats"
					. "\n SET published = $state"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_timeformat_view', $msg, $msgType);
	}
	
	/**
	 * set time format as default
	 */	
	function defaultTimeFormat() {
		
		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {

			// set the default time format to the default config 	
			$query = "UPDATE #__ninjaboard_configs"
					. "\n SET id_timeformat = $cid[0]"
					. "\n WHERE default_config = 1"
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_timeformat_view', $msg, $msgType);
	}
				
}
?>
