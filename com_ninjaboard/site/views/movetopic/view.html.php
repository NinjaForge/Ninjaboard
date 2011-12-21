<?php
/**
 * @version $Id: view.html.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Ninjaboard Move Topic View
 *
 * @package Ninjaboard
 */
class NinjaboardViewMoveTopic extends JView
{

	function display($tpl = null) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardAuth		=& NinjaboardAuth::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$topicId		= JRequest::getVar('topic', 0, '', 'int');

		if (!$topicId) {
			$messageQueue->addMessage(JText::_('NB_MSGREQUESTNOTPERFORMED'));
			$link = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='. $this->Itemid, false);
			$mainframe->redirect($link);
		}
		
		$model	=& $this->getModel();
		$topic = $model->getTopic($topicId);
		$this->assignRef('topic', $topic);
		$post = $model->getPost($topic->id_first_post);
		$this->assignRef('post', $post);
		
		if ($ninjaboardAuth->getUserRole($topic->id_forum) < 2) {
			$messageQueue->addMessage(sprintf(JText::_('NB_MSGNOPERMISSION'), JText::_('NB_MOVETOPIC')));
			$link = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$topicId.'&Itemid='. $this->Itemid, false);
			$mainframe->redirect($link);
		}
		
		$forums = $model->getForums($topic->id_forum);
		$this->assignRef('forums', $forums);
		
		// load form validation behavior
		JHTML::_('behavior.formvalidation');
		
		// handle page title
		$document->setTitle(JText::_('NB_MOVETOPIC'));
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_('NB_MOVETOPIC'), '');
		
		$action = 'index.php?option=com_ninjaboard&task=ninjaboardmovetopic&topic='.$topicId.'&Itemid='. $this->Itemid;
		$this->assignRef('action', $action);
		
		// get buttons
		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		$this->assignRef('buttonSubmit', $ninjaboardButtonSet->buttonByFunction['buttonSubmit']);
		$this->assignRef('buttonCancel', $ninjaboardButtonSet->buttonByFunction['buttonCancel']);
				
		parent::display($tpl);
	}
	
	function &getForum($index = 0) {
		$forum =& $this->forums[$index];
		return $forum;
	}
	
}
?>