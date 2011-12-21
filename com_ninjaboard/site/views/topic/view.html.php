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
 * Ninjaboard Topic View
 *
 * @package Ninjaboard
 */
class NinjaboardViewTopic extends JView
{

	function display($tpl = null) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$fp 				= FirePHP::getInstance(true);
		
		$topicId = JRequest::getVar('topic', 0, '', 'int');
		
		if (!$topicId) {
			$messageQueue->addMessage(JText::_('NB_MSGREQUESTNOTPERFORMED'));
			$link = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='. $this->Itemid, false);
			$mainframe->redirect($link);
		}
		
		$model		=& $this->getModel();
		$topic		= $model->getTopic($topicId);
		$this->assignRef('topic', $topic);
		$forum		= $model->getForum($topic->id_forum);
		$category	= $model->getCategory($forum->id_cat);
		$firstpost	= $model->getFirstPost($topic->id_first_post);
		$this->assignRef('firstpost' , $firstpost);
		
		// request variables
		$limit		= JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('posts_per_page'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		
		$fp->log('message');
		// handle page title
		$document->setTitle($forum->name.' - '.$firstpost->subject);
		
		// handle metadata
		$document->setDescription($forum->name.' - '.$firstpost->subject);
		$document->setMetadata('keywords', str_replace(' ', ', ', $firstpost->subject));
		
		// increment hit			
		$model->incrementHit($topic->id);
		
		// set data model
		$posts	= new NinjaboardPost($this->get('posts'));
		$this->assignRef('posts', $posts);

		// handle bread crumb
		$catRedirect = 'index.php?option=com_ninjaboard&view=board&category='.$category->id.'&Itemid='.$this->Itemid;
		$breadCrumbs->addBreadCrumb($category->name, $catRedirect);
		$redirect = 'index.php?option=com_ninjaboard&view=forum&forum='.$forum->id.'&Itemid='.$this->Itemid;
		$breadCrumbs->addBreadCrumb($forum->name, $redirect);
		$breadCrumbs->addBreadCrumb($firstpost->subject, '');
		
		// total posts
		$this->assignRef('total', $this->get('total'));
			
		$showPagination = false;
		if ($this->total > $limit) {
			$showPagination = true;
		}
		$this->assign('showPagination', $showPagination);
			
		jimport('joomla.html.pagination');
		$this->pagination = new JPagination($this->total, $limitstart, $limit);

		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		
		if ($forum->locked || $topic->status == 1) {
			if ($forum->locked) {
				$msg = sprintf(JText::_('NB_MSGFORUMLOCKED'), $forum->name);
				$messageQueue->addMessage($msg);
			}
			// ToDo: Design a new button locked! REMOVE NinjaboardHelper::getButtonFolder() when buttons are done!
			$buttonNewPost = $ninjaboardButtonSet->buttonByFunction['buttonPostReply'];		
			$buttonNewPost->href = '';
		} else {
			$buttonNewPost = $ninjaboardButtonSet->buttonByFunction['buttonPostReply'];
			$buttonNewPost->href = JRoute::_("index.php?option=com_ninjaboard&view=editpost&topic=".$topic->id."&post=0&Itemid=".$this->Itemid);
		}		
		$this->assign('buttonNewPost', $buttonNewPost);
		
		parent::display($tpl);
	}

}
?>
