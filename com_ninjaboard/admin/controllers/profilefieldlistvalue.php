<?php
/**
 * @version $Id: profilefieldlistvalue.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'profilefieldlistvalue.php');

/**
 * Ninjaboard Profile Field List Value Controller
 *
 * @package Ninjaboard
 */
class ControllerProfileFieldListValue extends JController
{

	/**
	 * compiles a list of profile field list values
	 */
	function showProfileFieldListValues() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		
		$context			= 'com_ninjaboard.ninjaboard_profilefieldlistvalue_view';
		$filter_fieldlist	= $app->getUserStateFromRequest("$context.filter_fieldlist", 'filter_fieldlist', 0);
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 'v.id_profile_field_list, v.ordering');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		$where = array();
		if ($filter_fieldlist > 0) {
			$where[] = "v.id_profile_field_list = $filter_fieldlist";
		}
		$where = (count($where) ? "\n WHERE " . implode(' AND ', $where) : '');
		
		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_profiles_fields_lists_values AS v"
				. $where
				. $orderby
				;		
		$db->setQuery($query);
		$total = $db->loadResult();
		
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
												
		$query = "SELECT v.*, l.name AS profile_field_list_name"
				. "\n FROM #__ninjaboard_profiles_fields_lists_values AS v"
				. "\n LEFT JOIN #__ninjaboard_profiles_fields_lists AS l ON l.id = v.id_profile_field_list"
				. $where
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// parameter list
		$lists = array();
		
		// get profile field lists
		$query = "SELECT l.*"
				. "\n FROM #__ninjaboard_profiles_fields_lists AS l"
				. "\n ORDER BY l.name"
				;
		$db->setQuery($query);
		$profileFieldLists[] = JHTML::_('select.option', '0', '- '.JText::_('NB_SELECTPROFILEFIELDLIST').' -', 'id', 'name');
		$profileFieldLists = array_merge($profileFieldLists, $db->loadObjectList());
		$lists['profilefieldlists'] = JHTML::_('select.genericlist',  $profileFieldLists, 'filter_fieldlist', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'id', 'name', $filter_fieldlist);

		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
									
		ViewProfileFieldListValue::showProfileFieldListValues($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit profile field list value operation
	 */
	function cancelEditProfileFieldListValue() {
		$app	=& JFactory::getApplication();
		
		// check in category so other can edit it
		$row =& JTable::getInstance('NinjaboardProfileFieldListValue');
		$row->bind(JRequest::get('post'));
		$row->checkin();
		
		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_view';
		$app->redirect($link);
	}
	
	/**
	 * edit the profile field list value
	 */
	function editProfileFieldListValue() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid 	=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}

		$row =& JTable::getInstance('NinjaboardProfileFieldListValue');
		$row->load($cid[0]);
		
		if (!$row->id) {
			$row->published = 1;
		}

		// is someone else editing this profile field list value?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_PROFILEFIELDLISTVALUE'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out profile field list value so nobody else can edit it
		$row->checkout($user->get('id'));
		
		// parameter list
		$lists = array();

		// list profile field lists		
		$query = "SELECT l.*"
				. "\n FROM #__ninjaboard_profiles_fields_lists AS l"
				. "\n ORDER BY l.name"
				;
		$db->setQuery($query);	
		$lists['profilefieldlists'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'id_profile_field_list', 'class="inputbox" size="1"', 'id', 'name', intval($row->id_profile_field_list));

		// build the html radio buttons for state
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $row->published);
						
		ViewProfileFieldListValue::editProfileFieldListValue($row, $lists);			
	}
	
	/**
	 * save the profile field list value
	 */	
	function saveProfileFieldListValue($task) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db     =& JFactory::getDBO();
		$post	=  JRequest::get('post');
		$row    =& JTable::getInstance('NinjaboardProfileFieldListValue');

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
			case 'ninjaboard_profilefieldlistvalue_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;
			case 'ninjaboard_profilefieldlistvalue_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_view';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_PROFILEFIELDLISTVALUE'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the profile field list value
	 */	
	function deleteProfileFieldListValue() {

		// initialize variables
		$app	    =& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
			
			// how many categories are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardProfileFieldListValue'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_PROFILEFIELDLISTVALUE'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_PROFILEFIELDLISTVALUES'), '');
			}
		
			$query = "DELETE FROM #__ninjaboard_profiles_fields_lists_values"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_PROFILEFIELDLISTVALUE'), JText::_('NB_DELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_view', $msg, $msgType);
	}

	/**
	 * moves the order of profile field list value up or down
	 * @param integer increment/decrement
	 */
	function orderProfileFieldListValue($direction) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$cid	=  JRequest::getVar('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$row =& JTable::getInstance('NinjaboardProfileFieldListValue');
			$row->load((int) $cid[0]);
			$row->move($direction);
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYREORDERED', JText::_('NB_PROFILEFIELDLISTVALUE'), $row->name);
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_view', $msg);
	}

	/**
	 * save the profile field list value order 
	 */
	function saveProfileFieldListValueOrder() {

		// initialize variables
		$app	    =& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$order		=  JRequest::getVar('order', array (0), 'post', 'array');
		$total		=  count($cid);
		$conditions	=  array();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// instantiate a profile field list value table object
		$row = & JTable::getInstance('NinjaboardProfileFieldListValue');

		// update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++) {
			$row->load((int) $cid[$i]);
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg());
					return false;
				}
				
				// remember to updateOrder this group
				$condition = "id_profile_field_list = $row->id_profile_field_list";
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
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVEDORDER', JText::_('NB_PROFILEFIELDLISTVALUE'));
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_view', $msg);
	}

	/**
	 * Changes the publish state of a profile field list value
	 * @param integer 0 = unpublishing, 1 = publishing
	 */
	function publishedProfileFieldListValue($state = 0) {
		
		// initialize variables
		$app	    =& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
			$msgText = ($state == 1) ? 'NB_MSGSUCCESSFULLYPUBLISHED' : 'NB_MSGSUCCESSFULLYUNPUBLISHED';
			
			// are there one or more rows to publish/unpublish?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardProfileFieldListValue'); $row->load($cid[0]);
				$msg = JText::sprintf($msgText, JText::_('NB_CATEGORY'), $row->name);
			} else {
				$msg = JText::sprintf($msgText, JText::_('NB_CATEGORIES'), '');
			}
			
			$query = "UPDATE #__ninjaboard_profiles_fields_lists_values"
					. "\n SET published = $state"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
		
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_view', $msg, $msgType);
	}
			
}
?>
