<?php
/**
 * @version $Id: category.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

//require_once(JPATH_COMPONENT.DS.'views'.DS.'category.php');

jimport('joomla.application.component.controller');

/**
 * Ninjaboard Category Controller
 *
 * @package Ninjaboard
 */
class NinjaboardControllerCategory extends JController
{

	/**
	 * compiles a list of categories
	 */
	function display() {
		$view = JRequest::getVar('view');
		
		if (!$view) {
			JRequest::setVar('view', 'categoryall');
		}
		
		parent::display();
	}
	
	/**
	 * cancels edit category operation
	 */
	function cancel() {
		$app	=& JFactory::getApplication();
		
		// check in category so other can edit it
		$row =& JTable::getInstance('NinjaboardCategory', 'Table');
		$row->bind(JRequest::get('post'));
		$row->checkin();
		
		$link = 'index.php?option=com_ninjaboard&controller=category&task=display';
		$app->redirect($link);
	}
	
	/**
	 * edit / create a new category
	 */
	function add()
	{
		JRequest::setVar('view', 'categorysingle');

		$this->display();
		
	}
	
	function edit()
	{
		JRequest::setVar('view', 'categorysingle');

		$this->display();
		
	}
	
	/**
	 * save the category
	 */	
	function save() {
		
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$row	=& JTable::getInstance('NinjaboardCategory', 'Table');
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
		$row->reorder();

		switch ($this->getTask()) {
			case 'apply':
				$link = 'index.php?option=com_ninjaboard&controller=category&task=edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;
			case 'save':
			default:
				$link = 'index.php?option=com_ninjaboard&controller=category&task=display';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_CATEGORY'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the category
	 */
	function remove() {

		JRequest::checkToken() or jexit( 'Invalid Token' );

	//get the id's of the items to be removed		
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		
	//Only delete if something is selected
		if (count($cid)) 
		{
		
		//get an isntance of our table
			$row =& JTable::getInstance('NinjaboardCategory', 'Table');
		
		// how many categories are there to delete? If only one, get it's name for the message
			if (count($cid) == 1) {
				$row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_CATEGORY'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_CATEGORIES'), '');
			}
			
		//delete the records
			foreach ($cid as $id) {
				$id = (int) $id;
				
				if (!$row->delete($id)) {
					JError::raiseError(500, $row->getError() );
				}
			}
		
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_CATEGORY'), JText::_('NB_DELETE'));
		}
		
		$this->setRedirect('index.php?option=com_ninjaboard&controller=category&task=display', $msg);
	
	}
	
	/**
	 * moves the order of category up or down
	 * @param integer increment/decrement
	 */
	function orderCategory($direction) {

		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$cid	=  JRequest::getVar('cid', array(), 'post', 'array');

		if (isset($cid[0])) {
			$row =& JTable::getInstance('NinjaboardCategory', 'Table');
			$row->load((int) $cid[0]);
			$row->move($direction);
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYREORDERED', JText::_('NB_CATEGORY'), $row->name);
		$app->redirect('index.php?option=com_ninjaboard&controller=category&task=display', $msg);
	}
	
	/**
	 * save the category order 
	 */
	function saveCategoryOrder() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(0), 'post', 'array');
		$order		=  JRequest::getVar('order', array (0), 'post', 'array');
		$total		=  count($cid);
		$conditions	=  array();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// instantiate a category table object
		$row = & JTable::getInstance('NinjaboardCategory', 'Table');

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

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVEDORDER', JText::_('NB_CATEGORY'));
		$app->redirect('index.php?option=com_ninjaboard&controller=category&task=display', $msg);
	}
	
	/**
	 * Changes the publish state of a category
	 * @param integer 0 = unpublishing, 1 = publishing
	 */
	function oldpublish($state = 0) {
		
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

			$query = "UPDATE #__ninjaboard_categories"
					. "\n SET published = $state"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
	
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&controller=category&task=display', $msg, $msgType);
	}
	
	function publish()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		global $option;
		
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		
		$row =& JTable::getInstance('NinjaboardCategory', 'Table');
		
		$publish = 1;
	
		if ($this->getTask() == 'unpublish') {
			$publish = 0;
		}
		
		if(!$row->publish($cid, $publish))
		{
			JError::raiseError(500, $row->getError() );
		}
				
		// are there one or more rows to publish/unpublish?
		if (count($cid) == 1) {
			$row->load($cid[0]);
			$msg = JText::sprintf($msgText, JText::_('NB_CATEGORY'), $row->name);
		} else {
			$msg = JText::sprintf($msgText, JText::_('NB_CATEGORIES'), '');
		}
		
		$this->setRedirect('index.php?option=com_ninjaboard&controller=category&task=display', $msg);
		
	}
			
}
?>
