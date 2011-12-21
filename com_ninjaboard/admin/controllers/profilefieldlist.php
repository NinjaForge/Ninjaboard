<?php
/**
 * @version $Id: profilefieldlist.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'profilefieldlist.php');

/**
 * Ninjaboard Profile Field List Controller
 *
 * @package Ninjaboard
 */
class ControllerProfileFieldList extends JController
{

	/**
	 * compiles a list of profile field lists
	 */
	function showProfileFieldLists() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		
		$context			= 'com_ninjaboard.ninjaboard_profilefieldlist_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 'l.name');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_profiles_fields_lists AS l"
				. $orderby
				;		
		$db->setQuery($query);
		$total = $db->loadResult();
		
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
												
		$query = "SELECT l.*"
				. "\n FROM #__ninjaboard_profiles_fields_lists AS l"
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// parameter list
		$lists = array();
		
		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
		
		ViewProfileFieldList::showProfileFieldLists($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit profile field list operation
	 */
	function cancelEditProfileFieldList() {
		$app	=& JFactory::getApplication();
		
		// check in category so other can edit it
		$row =& JTable::getInstance('NinjaboardProfileFieldList');
		$row->bind(JRequest::get('post'));
		$row->checkin();
	
		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlist_view';
		$app->redirect($link);
	}
	
	/**
	 * edit the profile field list
	 */
	function editProfileFieldList() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid 	=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}

		$row =& JTable::getInstance('NinjaboardProfileFieldList');
		$row->load($cid[0]);

		// is someone else editing this profile field list?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlist_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_PROFILEFIELDLIST'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out profile field list so nobody else can edit it
		$row->checkout($user->get('id'));
		
		// parameter list
		$lists = array();
		
		// build the html radio buttons for published
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $row->published);
				
		ViewProfileFieldList::editProfileFieldList($row, $lists);			
	}
	
	/**
	 * save the profile field list
	 */	
	function saveProfileFieldList($task) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db 	=& JFactory::getDBO();
		$post	=  JRequest::get('post');
		$row 	=& JTable::getInstance('NinjaboardProfileFieldList');

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
			case 'ninjaboard_profilefieldlist_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlist_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;
			case 'ninjaboard_profilefieldlist_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlist_view';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_PROFILEFIELDLIST'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the profile field list
	 */	
	function deleteProfileFieldList() {

		// initialize variables
		$app	    =& JFactory::getApplication();
		$db		    =& JFactory::getDBO();
		$cid	    =  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	= '';
		
		JArrayHelper::toInteger($cid);

		if (count($cid)) {
			
			// how many profile field list are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardProfileFieldList'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_PROFILEFIELDLIST'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_PROFILEFIELDLISTS'), '');
			}
	
			$query = "DELETE FROM #__ninjaboard_profiles_fields_lists"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_PROFILEFIELDLIST'), JText::_('NB_DELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlist_view', $msg, $msgType);
	}
	
	/**
	 * Changes the publish state of a profile field list
	 * @param integer 0 = unpublishing, 1 = publishing
	 */
	function changeProfileFieldListPublishState($state = 0) {
		
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
				$row =& JTable::getInstance('NinjaboardProfileFieldList'); $row->load($cid[0]);
				$msg = JText::sprintf($msgText, JText::_('NB_PROFILEFIELDLIST'), $row->name);
			} else {
				$msg = JText::sprintf($msgText, JText::_('NB_PROFILEFIELDLISTS'), '');
			}
			
			$query = "UPDATE #__ninjaboard_profiles_fields_lists"
					. "\n SET published = $state"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);	
	
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}
		
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlist_view', $msg, $msgType);
	}
			
}
?>
