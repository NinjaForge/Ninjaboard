<?php
/**
 * @version $Id: profilefield.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'profilefield.php');

/**
 * Ninjaboard Profile Field Controller
 *
 * @package Ninjaboard
 */
class ControllerProfileField extends JController
{

	/**
	 * compiles a list of profile fields
	 */
	function showProfileFields() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		
		$context			= 'com_ninjaboard.ninjaboard_profilefield_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 's.ordering, p.ordering');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_profiles_fields AS p"
				. "\n INNER JOIN #__ninjaboard_profiles_fields_sets AS s ON s.id = p.id_profile_field_set"
				. $orderby
				;		
		$db->setQuery($query);
		$total = $db->loadResult();
		
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
												
		$query = "SELECT p.*, s.name AS profile_field_set_name"
				. "\n FROM #__ninjaboard_profiles_fields AS p"
				. "\n INNER JOIN #__ninjaboard_profiles_fields_sets AS s ON s.id = p.id_profile_field_set"
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// parameter list
		$lists = array();
		
		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
		
		ViewProfileField::showProfileFields($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit profile field operation
	 */
	function cancelEditProfileField() {
		$app	=& JFactory::getApplication();
		
		// check in profile field so other can edit it
		$row =& JTable::getInstance('NinjaboardProfileField');
		$row->bind(JRequest::get('post'));
		$row->checkin();
		
		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefield_view';
		$app->redirect($link);
	}
	
	/**
	 * edit the profile field
	 */
	function editProfileField() {
		
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$cid 	=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}

		$row =& JTable::getInstance('NinjaboardProfileField');
		$row->load($cid[0]);

		// is someone else editing this profile field?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefield_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_PROFILEFIELD'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out profile field so nobody else can edit it
		$row->checkout($user->get('id'));
		
		// parameter list
		$lists = array();

		// build the html radio buttons for published
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $row->published);
		// build the html radio buttons for required
		$lists['required'] = JHTML::_('select.booleanlist', 'required', '', $row->required);
		// build the html radio buttons for disabled
		$lists['disabled'] = JHTML::_('select.booleanlist', 'disabled', '', $row->disabled);
		// build the html radio buttons for show on registration
		$lists['show_on_registration'] = JHTML::_('select.booleanlist', 'show_on_registration', '', $row->show_on_registration);

		// list profile field sets		
		$query = "SELECT p.*"
				. "\n FROM #__ninjaboard_profiles_fields_sets AS p"
				. "\n ORDER BY p.ordering"
				;
		$db->setQuery($query);
		$lists['profilefieldsets'] = JHTML::_('select.genericlist', $db->loadObjectList(), 'id_profile_field_set', 'class="inputbox" size="1"', 'id', 'name', intval($row->id_profile_field_set));
	
		// list profile field lists		
		$query = "SELECT l.*"
				. "\n FROM #__ninjaboard_profiles_fields_lists AS l"
				. "\n ORDER BY l.name"
				;
		$db->setQuery($query);
		$lists['profilefieldlists'] = JHTML::_('select.genericlist', $db->loadObjectList(), 'id_profile_field_list', 'class="inputbox" size="1"', 'id', 'name', intval($row->id_profile_field_list));
		
		// list GUI elements
		$elements = array();
		$elements[] = JHTML::_('select.option', 0, JText::_('TextBox'));
		$elements[] = JHTML::_('select.option', 1, JText::_('TextArea'));
//		$elements[] = JHTML::makeOption(2, JText::_('CheckBox'));
		$elements[] = JHTML::_('select.option', 3, JText::_('RadioButton'));
		$elements[] = JHTML::_('select.option', 4, JText::_('ListBox'));
		$elements[] = JHTML::_('select.option', 5, JText::_('ComboBox'));
		$lists['elements'] = JHTML::_('select.genericlist', $elements, 'element', 'class="inputbox" size="1" onchange="selElement(this.options[this.selectedIndex].value);"', 'value', 'text', $row->element);
		
		$types = array();
		$types[] = JHTML::_('select.option', 'varchar', JText::_('Text'));
		$types[] = JHTML::_('select.option', 'integer', JText::_('Integer'));
		$types[] = JHTML::_('select.option', 'date', JText::_('Date'));
		$types[] = JHTML::_('select.option', 'time', JText::_('Time'));
		$types[] = JHTML::_('select.option', 'datetime', JText::_('Datetime'));
		$lists['types'] = JHTML::_('select.genericlist', $types, 'type', 'class="inputbox" size="1"', 'value', 'text', $row->type);
		
		ViewProfileField::editProfileField($row, $lists);			
	}
	
	/**
	 * save the profile field
	 */	
	function saveProfileField($task) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db     =& JFactory::getDBO();
		$cid 	=  JRequest::getVar('id', 0, 'post', 'int');
		$post	=  JRequest::get('post');

		$row =& JTable::getInstance('NinjaboardProfileField');
		
		if (!$row->bind($post)) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		// must be called before storing actual record!
		NinjaboardHelper::checkInField($row, $cid);
		
		if ($row->id == 0) {	
			$query = "SELECT Max(ordering) AS ordering"
					. "\n FROM #__ninjaboard_profiles_fields"
					. "\n WHERE id_profile_field_set = $row->id_profile_field_set"
					;
			$db->setQuery($query);
			$max = $db->loadObject();
			$row->ordering = $max->ordering + 1;
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
			case 'ninjaboard_profilefield_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefield_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;

			case 'ninjaboard_profilefield_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefield_view';
				break;
		}

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_PROFILEFIELD'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the profile field
	 */	
	function deleteProfileField() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
			
			// how many categories are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardProfileField'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_PROFILEFIELD'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_PROFILEFIELDS'), '');
			}
		
			$query = "DELETE FROM #__ninjaboard_profiles_fields"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_PROFILEFIELD'), JText::_('NB_DELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefield_view', $msg, $msgType);
	}
	
	/**
	 * moves the order of profile field up or down
	 * @param integer increment/decrement
	 */
	function orderProfileField($direction) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$cid	=  JRequest::getVar('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$row =& JTable::getInstance('NinjaboardProfileField');
			$row->load((int) $cid[0]);
			$row->move($direction, 'id_profile_field_set = ' . (int) $row->id_profile_field_set);
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYREORDERED', JText::_('NB_PROFILEFIELD'), $row->name);
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefield_view', $msg);
	}
	
	/**
	 * save the profile field order 
	 */
	function saveProfileFieldOrder() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$order		=  JRequest::getVar('order', array (0), 'post', 'array');
		$total		=  count($cid);
		$conditions	=  array();
		
		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));
		
		// instantiate a profile field table object
		$row = & JTable::getInstance('NinjaboardProfileField');

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
				$condition = "id_profile_field_set = $row->id_profile_field_set";
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

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVEDORDER', JText::_('NB_PROFILEFIELD'));
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefield_view', $msg);
	}
	
	/**
	 * Changes the publish state of a profile field
	 * @param integer 0 = unpublishing, 1 = publishing
	 */
	function changeProfileFieldPublishState($state = 0) {
		
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
				$row =& JTable::getInstance('NinjaboardProfileField'); $row->load($cid[0]);
				$msg = JText::sprintf($msgText, JText::_('NB_PROFILEFIELD'), $row->name);
			} else {
				$msg = JText::sprintf($msgText, JText::_('NB_PROFILEFIELDS'), '');
			}

			$query = "UPDATE #__ninjaboard_profiles_fields"
					. "\n SET published = $state"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);

			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_profilefield_view', $msg, $msgType);
	}
			
}
?>
