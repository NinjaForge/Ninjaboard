<?php
/**
 * @version $Id: forum.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'forum.php');

/**
 * Ninjaboard Forum Controller
 *
 * @package Ninjaboard
 */
class ControllerForum extends JController
{

	/**
	 * compiles a list of forums
	 */
	function showForums() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();

		$context			= 'com_ninjaboard.ninjaboard_forum_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 'c.ordering, f.ordering');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_forums AS f"
				. "\n INNER JOIN #__ninjaboard_categories AS c ON f.id_cat = c.id"
				. $orderby
				;
		$db->setQuery($query);
		$total = $db->loadResult();
	
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);		
		
		$query = "SELECT f.*, c.name AS category"
				. "\n FROM #__ninjaboard_forums AS f"
				. "\n INNER JOIN #__ninjaboard_categories AS c ON f.id_cat = c.id"
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// parameter list
		$lists = array();
		
		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
		
		ViewForum::showForums($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit forum operation
	 */
	function cancelEditForum() {
		
		// check in forum so other can edit it
		$app	=& JFactory::getApplication();
		$row	=& JTable::getInstance('NinjaboardForum');
		$row->bind(JRequest::get('post'));
		$row->checkin();

		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_forum_view';
		$app->redirect($link);
	}
	
	/**
	 * edit the forum
	 */
	function editForum() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db 	=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid 	=  JRequest::getVar('cid', array(0));

		if (!is_array($cid)) {
			$cid = array(0);
		}

		$row =& JTable::getInstance('NinjaboardForum');
		$row->load($cid[0]);

		// is someone else editing this forum?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_forum_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_FORUM'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out forum so nobody else can edit it
		$row->checkout($user->get('id'));
		
		// preinitialize fields of a new forum record
		if ($cid[0] < 1) {
			$row->state = 1;	// enabled
			$row->locked = 0;	// not locked
			
			$row->auth_view = 0;		// all
			$row->auth_read = 0;		// all
			$row->auth_post = 1;		// registered
			$row->auth_reply = 1;		// registered
			$row->auth_edit = 1;		// registered
			$row->auth_delete = 1;		// registered
			$row->auth_reportpost = 1;	// registered
			$row->auth_sticky = 3;		// moderators
			$row->auth_lock = 3;		// moderators
			$row->auth_announce = 3;	// moderators
			$row->auth_vote = 1;		// registered
			$row->auth_pollcreate = 1;	// registered
			$row->auth_attachments = 1;	// registered
		}

		// parameter list
		$lists = array();
		
		// list categories		
		$query = "SELECT c.*"
				. "\n FROM #__ninjaboard_categories AS c"
				. "\n ORDER BY c.ordering"
				;
		$db->setQuery( $query );
		$lists['categories'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'id_cat', 'class="inputbox" size="1"', 'id', 'name', intval($row->id_cat));
				
		// build the html radio buttons for state
		$lists['state'] = JHTML::_('select.booleanlist', 'state', '', $row->state);
		// build the html radio buttons for locked
		$lists['locked'] = JHTML::_('select.booleanlist', 'locked', '', $row->locked);
		
		// get authentification option list
		$ninjaboardAuth =& NinjaboardAuth::getInstance();
		$authOptionList = $ninjaboardAuth->getAuthOptionList();

		// get last post
		$lastPost =& JTable::getInstance('NinjaboardPost');
		$lastPost->load($row->id_last_post);
		$query = "SELECT MAX(id)"
				. "\n FROM #__menu AS m"
				. "\n WHERE m.link LIKE 'index.php?option=com_ninjaboard&view=board%'"
				;
		$db->setQuery($query);
		$Itemid = $db->loadResult();
		$itemLink = JRoute::_(JURI::root().'index.php?option=com_ninjaboard&view=topic&topic='.$lastPost->id_topic.'&Itemid='.$Itemid.'#p'.$lastPost->id);
		$row->last_post_href ='<a href="'.$itemLink.'" target="_blank">'.$lastPost->subject.'</a>';
		
		$lists['auth_view'] = JHTML::_('select.genericlist', $authOptionList, 'auth_view', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_view));
		$lists['auth_read'] = JHTML::_('select.genericlist', $authOptionList, 'auth_read', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_read));
		$lists['auth_post'] = JHTML::_('select.genericlist', $authOptionList, 'auth_post', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_post));
		$lists['auth_reply'] = JHTML::_('select.genericlist', $authOptionList, 'auth_reply', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_reply));
		$lists['auth_edit'] = JHTML::_('select.genericlist', $authOptionList, 'auth_edit', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_edit));
		$lists['auth_delete'] = JHTML::_('select.genericlist', $authOptionList, 'auth_delete', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_delete));
		$lists['auth_reportpost'] = JHTML::_('select.genericlist', $authOptionList, 'auth_reportpost', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_reportpost));
		$lists['auth_sticky'] = JHTML::_('select.genericlist', $authOptionList, 'auth_sticky', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_sticky));
		$lists['auth_lock'] = JHTML::_('select.genericlist', $authOptionList, 'auth_lock', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_lock));
		$lists['auth_announce'] = JHTML::_('select.genericlist', $authOptionList, 'auth_announce', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_announce));
		$lists['auth_vote'] = JHTML::_('select.genericlist', $authOptionList, 'auth_vote', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_vote));
		$lists['auth_pollcreate'] = JHTML::_('select.genericlist', $authOptionList, 'auth_pollcreate', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_pollcreate));
		$lists['auth_attachments'] = JHTML::_('select.genericlist', $authOptionList, 'auth_attachments', 'class="inputbox" size="1"', 'value', 'text', intval($row->auth_attachments));

		ViewForum::editForum($row, $lists);	
	}
	
	/**
	 * save the forum
	 */	
	function saveForum($task) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db 	=& JFactory::getDBO();
		$row 	=& JTable::getInstance('NinjaboardForum');
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
			case 'ninjaboard_forum_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_forum_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;
			case 'ninjaboard_forum_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_forum_view';
				break;
		}

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_FORUM'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the forum
	 */	
	function deleteForum() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger( $cid );

		if (count($cid)) {
			
			// how many categories are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardCategory'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_FORUM'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_FORUMS'), '');
			}
		
			$query = "DELETE FROM #__ninjaboard_forums"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_FORUM'), JText::_('NB_DELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_forum_view', $msg, $msgType);
	}
	
	/**
	 * moves the order of forum up or down
	 * @param integer increment/decrement
	 */
	function orderForum($direction) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$cid	=  JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (isset($cid[0])) {
			$row =& JTable::getInstance('NinjaboardForum');
			$row->load((int) $cid[0]);
			$row->move($direction, 'id_cat = ' . (int) $row->id_cat);
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYREORDERED', JText::_('NB_FORUM'), $row->name);
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_forum_view');
	}
	
	/**
	 * save the forum order 
	 */
	function saveForumOrder() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$order		=  JRequest::getVar('order', array (0), 'post', 'array');
		$total		=  count($cid);
		$conditions	=  array ();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// instantiate a forum table object
		$row = & JTable::getInstance('NinjaboardForum');

		// update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++) {
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError( 500, $db->getErrorMsg() );
					return false;
				}
				
				// remember to updateOrder this group
				$condition = "id_cat = $row->id_cat";
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

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVEDORDER', JText::_('NB_FORUM'));
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_forum_view', $msg);
	}
	
	/**
	 * changes the publish state of a forum
	 * @param integer 0 = unpublishing, 1 = publishing
	 */
	function changeForumPublishState($state = 0) {
		
		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=&  JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$msgType	=  '';

		JArrayHelper::toInteger($cid);

		if (count($cid)) {
			$msgText = ($state == 1) ? 'NB_MSGSUCCESSFULLYPUBLISHED' : 'NB_MSGSUCCESSFULLYUNPUBLISHED';
			
			// are there one or more rows to publish/unpublish?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardCategory'); $row->load($cid[0]);
				$msg = JText::sprintf($msgText, JText::_('NB_FORUM'), $row->name);
			} else {
				$msg = JText::sprintf($msgText, JText::_('NB_FORUMS'), '');
			}

			$query = "UPDATE #__ninjaboard_forums"
					. "\n SET state = $state"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
	
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}
		
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_forum_view', $msg, $msgType);
	}
			
}
?>
