<?php
/**
 * @version $Id: profilefieldset.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'profilefieldset.php');

/**
 * Ninjaboard Profile Field Set Controller
 *
 * @package Ninjaboard
 */
class ControllerProfileFieldSet extends JController
{

	/**
	 * compiles a list of profile field sets
	 */
	function showProfileFieldSets() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		
		$context			= 'com_ninjaboard.ninjaboard_profilefieldset_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 'p.ordering');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_profiles_fields_sets AS p"
				. $orderby
				;		
		$db->setQuery($query);
		$total = $db->loadResult();
		
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
												
		$query = "SELECT p.*"
				. "\n FROM #__ninjaboard_profiles_fields_sets AS p"
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// parameter list
		$lists = array();
		
		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
		
		ViewProfileFieldSet::showProfileFieldSets($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit profile field set operation
	 */
	function cancelEditProfileFieldSet() {
		$app	=& JFactory::getApplication();
		
		// check in category so other can edit it
		$row =& JTable::getInstance('NinjaboardProfileFieldSet');
		$row->bind(JRequest::get('post'));
		$row->checkin();
		
		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_view';
		$app->redirect($link);
	}
	
	/**
	 * edit the profile field set
	 */
	function editProfileFieldSet() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid 	=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}

		$row =& JTable::getInstance('NinjaboardProfileFieldSet');
		$row->load($cid[0]);

		// is someone else editing this profile field set?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_PROFILEFIELDSET'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out profile field set so nobody else can edit it
		$row->checkout($user->get('id'));
		
		// parameter list
		$lists = array();

		// build the html radio buttons for state
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $row->published);		
				
		ViewProfileFieldSet::editProfileFieldSet($row, $lists);			
	}
	
	/**
	 * save the profile field set
	 */	
	function saveProfileFieldSet($task) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$post	=  JRequest::get('post');
		$row 	=& JTable::getInstance('NinjaboardProfileFieldSet');

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
		$row->reorder();

		switch ($task) {
			case 'ninjaboard_profilefieldset_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;
			case 'ninjaboard_profilefieldset_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_view';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_PROFILEFIELDSET'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the profile field set
	 */	
	function deleteProfileFieldSet() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
			
			// how many profile field sets are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardProfileFieldSet'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_PROFILEFIELDSET'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_PROFILEFIELDSETS'), '');
			}
		
			$query = "DELETE FROM #__ninjaboard_profiles_fields_sets"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_PROFILEFIELDSET'), JText::_('NB_DELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_view', $msg, $msgType);
	}
	
	/**
	 * moves the order of profile field set up or down
	 * @param integer increment/decrement
	 */
	function orderProfileFieldSet($direction) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$cid	=  JRequest::getVar('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$row =& JTable::getInstance('NinjaboardProfileFieldSet');
			$row->load((int) $cid[0]);
			$row->move($direction);
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYREORDERED', JText::_('NB_PROFILEFIELDSET'), $row->name);
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_view', $msg);
	}
	
	/**
	 * save the profile field set order 
	 */
	function saveProfileFieldSetOrder() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$order		=  JRequest::getVar('order', array (0), 'post', 'array');
		$total		=  count($cid);
		$conditions	=  array();
		
		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// instantiate an profile field set table object
		$row = & JTable::getInstance('NinjaboardProfileFieldSet');

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
				$condition = "";
				$found = false;
				foreach ($conditions as $cond)
					if ($cond[1] == $condition) {
						$found = true;
						break;
					}
				if (!$found)
					$conditions[] = array($row->id, $condition);
			}
		}

		// execute updateOrder for each group
		foreach ($conditions as $cond) {
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVEDORDER', JText::_('NB_PROFILEFIELDSET'));
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_view', $msg);
	}
	
	/**
	 * Changes the publish state of a profile field set
	 * @param integer 0 = unpublishing, 1 = publishing
	 */
	function changeProfileFieldSetPublishState($state = 0) {
		
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
				$row =& JTable::getInstance('NinjaboardProfileFieldSet'); $row->load($cid[0]);
				$msg = JText::sprintf($msgText, JText::_('NB_PROFILEFIELDSET'), $row->name);
			} else {
				$msg = JText::sprintf($msgText, JText::_('NB_PROFILEFIELDSETS'), '');
			}

			$query = "UPDATE #__ninjaboard_profiles_fields_sets"
					. "\n SET published = $state"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
	
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_view', $msg, $msgType);
	}
			
}
?>
