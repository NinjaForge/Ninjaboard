<?php
/**
 * @version $Id: rank.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'rank.php');

/**
 * Ninjaboard Rank Controller
 *
 * @package Ninjaboard
 */
class ControllerRank extends JController
{

	/**
	 * compiles a list of rank
	 */
	function showRanks() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		
		$context			= 'com_ninjaboard.ninjaboard_rank_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 'r.min_posts');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_ranks AS r"
				. $orderby
				;		
		$db->setQuery($query);
		$total = $db->loadResult();
		
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
												
		$query = "SELECT r.*"
				. "\n FROM #__ninjaboard_ranks AS r"
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// parameter list
		$lists = array();
		
		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
			
		ViewRank::showRanks($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit rank operation
	 */
	function cancelEditRank() {
		$app	=& JFactory::getApplication();
		
		// check in category so other can edit it
		$row =& JTable::getInstance('NinjaboardRank');
		$row->bind(JRequest::get('post'));
		$row->checkin();
	
		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_rank_view';
		$app->redirect($link);
	}
	
	/**
	 * edit the rank
	 */
	function editRank() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid 	=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}
		
		$row =& JTable::getInstance('NinjaboardRank');
		$row->load($cid[0]);

		// is someone else editing this rank?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_rank_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_RANK'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// parameter list
		$lists = array();

		// build the html radio buttons for state
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $row->published);		
				
		ViewRank::editRank($row, $lists);			
	}
	
	/**
	 * save the rank
	 */	
	function saveRank($task) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db     =& JFactory::getDBO();
		$post	=  JRequest::get('post');
		$row    =& JTable::getInstance('NinjaboardRank');

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
			case 'ninjaboard_rank_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_rank_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;
			case 'ninjaboard_rank_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_rank_view';
				break;
		}

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_RANK'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the rank
	 */	
	function deleteRank() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
			
			// how many categories are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardRank'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_RANK'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_RANKS'), '');
			}
		
			$query = "DELETE FROM #__ninjaboard_ranks"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_RANK'), JText::_('NB_DELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_rank_view', $msg, $msgType);
	}
	
	/**
	 * Changes the publish state of a rank
	 * @param integer 0 = unpublishing, 1 = publishing
	 */
	function changeRankPublishState($state = 0) {
		
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
				$row =& JTable::getInstance('NinjaboardCategory'); $row->load($cid[0]);
				$msg = JText::sprintf($msgText, JText::_('NB_CATEGORY'), $row->name);
			} else {
				$msg = JText::sprintf($msgText, JText::_('NB_CATEGORIES'), '');
			}
			
			$query = "UPDATE #__ninjaboard_ranks"
					. "\n SET published = $state"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
	
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_rank_view', $msg, $msgType);
	}
			
}
?>
