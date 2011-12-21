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
 * Ninjaboard Forum View
 *
 * @package Ninjaboard
 */
class NinjaboardViewForum extends JView
{

	function display($tpl = null) {
		global $mainframe;
				
		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardAuth		=& NinjaboardAuth::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();

		$forumId = JRequest::getVar('forum', 0, '', 'int');
		
		if (!$forumId) {
			$messageQueue->addMessage(JText::_('NB_MSGREQUESTNOTPERFORMED'));
			$link = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='. $this->Itemid, false);
			$mainframe->redirect($link);			
		}
		
		$model		=& $this->getModel();	
		$forum		= $model->getForum($forumId);
		$category	= $model->getCategory($forum->id_cat);

		// request variables
		$limit		= JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('topics_per_page'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		if (!$ninjaboardAuth->getAuth('auth_read', $forum->id)) {
			$msg = sprintf(JText::_('NB_MSGNOACCESSREAD'), $forum->name);
			$messageQueue->addMessage($msg);
			$link = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$this->Itemid, false);
			$mainframe->redirect($link);
		}

		// handle page title
		$document->setTitle($category->name.' - '.$forum->name);
		
		// handle metadata
		$document->setDescription($forum->description);
		$document->setMetadata('keywords', str_replace(' ', ', ', $forum->name));
		
		//set data model
		$topics =& $this->get('topics');
		$this->assignRef('topics', $topics);
		$announcements =& $this->get('announcements');
		$this->assignRef('announcements', $announcements);
		
		// handle bread crumb
		$redirect = 'index.php?option=com_ninjaboard&view=board&category='.$category->id.'&Itemid='.$this->Itemid;
		$breadCrumbs->addBreadCrumb($category->name, $redirect);
		$breadCrumbs->addBreadCrumb($forum->name, '');
		
		// total topics
		$this->assignRef('total', $this->get('total'));
		
		$showPagination = false;
		if ($this->total > $limit) {
			$showPagination = true;
		}
		$this->assign('showPagination', $showPagination);
		
		jimport('joomla.html.pagination');
		$this->pagination = new JPagination($this->total, $limitstart, $limit);
		
		// post icons
		$ninjaboardIconSet = new NinjaboardIconSet($ninjaboardConfig->getIconSetFile());
		$this->assignRef('ninjaboardIconSet', $ninjaboardIconSet);

		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();

		if ($forum->locked) {
			$msg = sprintf(JText::_('NB_MSGFORUMLOCKED'), $forum->name);
			$messageQueue->addMessage($msg);
			// ToDo: button locked => design a new button
			$buttonNewTopic = $ninjaboardButtonSet->buttonByFunction['buttonNewTopic'];		
			$buttonNewTopic->href = '';
		} else {
			$buttonNewTopic = $ninjaboardButtonSet->buttonByFunction['buttonNewTopic'];
			$buttonNewTopic->href = JRoute::_('index.php?option=com_ninjaboard&view=edittopic&forum='.$forum->id.'&topic=0&Itemid='.$this->Itemid);
		}
		$this->assign('buttonNewTopic', $buttonNewTopic);		

		$this->assignRef('searchInputBoxText', JText::_('NB_SEARCHTHISFORUM'));
		
		// search button
		$this->assignRef('buttonSearch', $ninjaboardButtonSet->buttonByFunction['buttonSearch']);
		$this->assignRef('actionSearch', JRoute::_('index.php?option=com_ninjaboard&view=search&forum='.$forumId.'&Itemid='.$this->Itemid));
		
		parent::display($tpl);
	}
	
	function &getAnnouncement($index = 0) {
		$announcement =& $this->announcements[$index];
		return $this->getExtData($announcement);
	}
	
	function &getTopic($index = 0) {
		$topic =& $this->topics[$index];
		return $this->getExtData($topic);
	}
	
	function &getExtData($topic) {
		
		// topic
		$topic->href = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$topic->id.'&Itemid='.$this->Itemid);
		
		$ninjaboardConfig =& NinjaboardConfig::getInstance();
		
		$topicInfoIcons = array();
		switch ($topic->type) {
			case 1:		// sticky
				$topicInfoIcons[] = $this->ninjaboardIconSet->iconByFunction['topicSticky'];				
				break;
			case 2:		// announcement
				$topicInfoIcons[] = $this->ninjaboardIconSet->iconByFunction['topicAnnouncement'];
				break;										
		}		
		switch ($topic->status) {
			case 1:		// locked
				$topicInfoIcons[] = $this->ninjaboardIconSet->iconByFunction['topicLocked'];
				break;
			case 2:		// solved
				$topicInfoIcons[] = $this->ninjaboardIconSet->iconByFunction['topicSolved'];
				break;
			case 3:		// trash
				$topicInfoIcons[] = $this->ninjaboardIconSet->iconByFunction['topicTrash'];
				break;																
		}		

		// Is this a new topic?
		$forum =& JTable::getInstance('NinjaboardForum');
		$forum->load($topic->id_forum);
		if ((strtotime(gmdate("Y-m-d H:i:s")) - strtotime($topic->date_last_post)) < ($forum->new_posts_time * 60)) {
			$topicInfoIcons[] = $this->ninjaboardIconSet->iconByFunction['topicNew'];
		}
		unset($forum);
		
		$topic->topicInfoIcons = $topicInfoIcons;
		
		// get the topic icon
		$topic->postIcon = $this->ninjaboardIconSet->iconByFunction[$topic->icon_function];
		
		$topic->date_topic = NinjaboardHelper::Date($topic->date_topic);
		$topic->date_last_post = NinjaboardHelper::Date($topic->date_last_post);

		$topic->lastPostLink = 'index.php?option=com_ninjaboard&view=topic&topic='.$topic->id.'&Itemid='.$this->Itemid.'#p'.$topic->id_last_post;

		$topic->authorLink = '';
		if ($topic->author) {
			$topic->authorLink = JRoute::_('index.php?option=com_ninjaboard&view=profile&id='.$topic->id_author.'&Itemid='.$this->Itemid);
		} else {
			if ($topic->guest_author) {
				$topic->author = $topic->guest_author;
			} else {
				$topic->author = JText::_('NB_GUEST');
			}
		}
		
		$topic->posterLink = '';
		if ($topic->poster) {
			$topic->posterLink = JRoute::_('index.php?option=com_ninjaboard&view=profile&id='.$topic->id_poster.'&Itemid='.$this->Itemid);
		} else {
			if ($topic->guest_poster) {
				$topic->poster = $topic->guest_poster;
			} else {
				$topic->poster = JText::_('NB_GUEST');
			}
		}
		
		return $topic;
	}
		
}
?>
