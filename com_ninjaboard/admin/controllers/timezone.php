<?php
/**
 * @version $Id: timezone.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'timezone.php');

/**
 * Ninjaboard Time Zone Controller
 *
 * @package Ninjaboard
 */
class ControllerTimeZone extends JController
{

	/**
	 * compiles a list of time zones
	 */
	function showTimeZones() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		
		$context			= 'com_ninjaboard.ninjaboard_timezone_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 'z.ordering');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_timezones AS z"
				. $orderby
				;	
		$db->setQuery($query);
		$total = $db->loadResult();
		
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
												
		$query = "SELECT z.*, (c.id_timezone = z.id) AS default_timezone"
				. "\n FROM #__ninjaboard_timezones AS z"
				. "\n LEFT JOIN #__ninjaboard_configs AS c ON c.id_timezone = z.id AND c.default_config = 1"
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// parameter list
		$lists = array();
		
		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
	
		ViewTimeZone::showTimeZones($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit time zone operation
	 */
	function cancelEditTimeZone() {

		$app	=& JFactory::getApplication();
		
		// check in category so other can edit it
		$row =& JTable::getInstance('NinjaboardCategory');
		$row->bind(JRequest::get('post'));
		$row->checkin();
		
		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timezone_view';
		$app->redirect($link);
	}
	
	/**
	 * edit the time zone
	 */
	function editTimeZone() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid 	=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}
	
		$row =& JTable::getInstance('NinjaboardTimeZone');
		$row->load($cid[0]);

		// is someone else editing this time zone?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timezone_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_TIMEZONE'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out category so nobody else can edit it
		$row->checkout($user->get('id'));
		
		// parameter list
		$lists = array();
		
		// build the html radio buttons for state
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $row->published);		
				
		ViewTimeZone::editTimeZone($row, $lists);			
	}
	
	/**
	 * save the time zone
	 */	
	function saveTimeZone($task) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db     =& JFactory::getDBO();
		$post	=  JRequest::get('post');
		$row    =& JTable::getInstance('NinjaboardTimeZone');

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
			case 'ninjaboard_timezone_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timezone_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;

			case 'ninjaboard_timezone_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timezone_view';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_TIMEZONE'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the time zone
	 */	
	function deleteTimeZone() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	= '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
			
			// how many time zones are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardTimeZone'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_TIMEZONE'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_TIMEZONES'), '');
			}
	
			$query = "DELETE FROM #__ninjaboard_timezones"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
					
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_TIMEZONE'), JText::_('NB_DELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_timezone_view', $msg, $msgType);
	}
	
	/**
	 * moves the order of time zone up or down
	 * @param integer increment/decrement
	 */
	function orderTimeZone($direction) {
		global $app;

		// initialize variables
		$app		=& JFactory::getApplication();
		$db		= & JFactory::getDBO();
		$cid	= JRequest::getVar('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$row =& JTable::getInstance('NinjaboardTimeZone');
			$row->load((int) $cid[0]);
			$row->move($direction);
		}
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYREORDERED', JText::_('NB_TIMEZONE'), $row->name);
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_timezone_view', $msg);
	}
	
	/**
	 * save the forum order 
	 */
	function saveTimeZoneOrder() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		= JRequest::getVar('cid', array(0), 'post', 'array');
		$order		= JRequest::getVar('order', array (0), 'post', 'array');
		$total		= count($cid);
		$conditions	= array();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));
		
		// instantiate an article table object
		$row = & JTable::getInstance('NinjaboardTimeZone');

		// update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++) {
			$row->load((int) $cid[$i]);
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg());
					return false;
				}
				
				// remember to update order this group
				$condition = '';
				$found = false;
				foreach ($conditions as $cond)
					if ($cond[1] == $condition) {
						$found = true;
						break;
					}
				if (!$found)
					$conditions[] = array ($row->id, $condition);
			}
		}

		// execute updateOrder for each group
		foreach ($conditions as $cond) {
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVEDORDER', JText::_('NB_TIMEZONE'));
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_timezone_view', $msg);
	}
		
	/**
	 * changes the publish state of a time zone
	 * @param integer 0 = unpublishing, 1 = publishing
	 */
	function changeTimeZonePublishState($state = 0) {
		
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
				$row =& JTable::getInstance('NinjaboardTimeZone'); $row->load($cid[0]);
				$msg = JText::sprintf($msgText, JText::_('NB_TIMEZONE'), $row->name);
			} else {
				$msg = JText::sprintf($msgText, JText::_('NB_TIMEZONES'), '');
			}
			
			$query = "UPDATE #__ninjaboard_timezones"
					. "\n SET published = $state"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_timezone_view', $msg, $msgType);
	}
	
	/**
	 * set time zone as default
	 */
	function defaultTimeZone() {
		
		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
	
			// set the default time zone to the default config 	
			$query = "UPDATE #__ninjaboard_configs"
					. "\n SET id_timezone = $cid[0]"
					. "\n WHERE default_config = 1"
					;
			$db->setQuery($query);
					
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_timezone_view', $msg, $msgType);
	}
				
}
?>
