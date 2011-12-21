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
 * Ninjaboard Board View
 *
 * @package Ninjaboard
 */
class NinjaboardViewBoard extends JView
{

	function display($tpl = null) {
		
		// initialize variables
		$db				=& JFactory::getDBO();
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardAuth		=& NinjaboardAuth::getInstance();

		$this->assignRef('document', $document);
		
		$boardName = $ninjaboardConfig->getBoardSettings('board_name');
		
		// handle page title
		$document->setTitle($boardName);
		
		// handle metadata
		$document->setDescription($ninjaboardConfig->getBoardSettings('description'));
		$document->setMetadata('keywords', $ninjaboardConfig->getBoardSettings('keywords'));
		
		// set data model
		$model =& $this->getModel();
		$tempcategories =& $this->get('categories');
		$tempforums =& $this->get('forums');

		$forums = array();
		foreach ($tempforums as $forum) {
			if ($ninjaboardAuth->getAuth('auth_view', $forum->id)) {
				$forums[] = $forum;
			}
		}		
		
		// look up if there is a forum to display for the category
		$categories = array();
		foreach ($tempcategories as $category) {
			foreach ($forums as $forum) {
				if ($category->id == $forum->id_cat) {
					$categories[] = $category;
					break;
				}
			}
		}
		
		// board icons
		$ninjaboardIconSet = new NinjaboardIconSet($ninjaboardConfig->getIconSetFile());
		$this->assignRef('ninjaboardIconSet', $ninjaboardIconSet);
		
		// initialize latest items	
		if ($this->showBoxLatestItems) {
		
			// items to show. topics or posts?
			$latestItemsHeader = '';
			switch ((int)$ninjaboardConfig->getBoardSettings('latest_items_type')) {
				case 0:
					$latestItemsHeader = JText::_('NB_LATESTTOPICS');
					break;
				case 1:
					$latestItemsHeader = JText::_('NB_LATESTPOSTS');
					break;
			}
			$this->assignRef('latestItemsHeader', $latestItemsHeader);
			$this->assignRef('latestItems', $this->get('latestitems'));
		}
		
		// initialize statistic variables
		if ($this->showBoxStatistic) {
			$this->assignRef('totalTopics', $this->get('totaltopics'));
			$this->assignRef('totalPosts', $this->get('totalposts'));
			$this->assignRef('totalMembers', $this->get('totalmembers'));
			
			$latestMember = $this->get('latestmember');
			$latestMember->set('userLink', JRoute::_('index.php?option=com_ninjaboard&view=profile&id='.$latestMember->get('id').'&Itemid='.$this->Itemid));			
			$this->assignRef('latestMember', $latestMember);
		}
		
		// initialize whos online variables
		if ($this->showBoxWhosOnline) {		
			$sessions = $this->get('sessions');
	
			// calculate number of guests and members
			$membersOnline = 0;
			$guestsOnline = 0;
			foreach ($sessions as $session) {
				// if guest increase guest count by 1
				if ($session->id_user == 0) {
					$guestsOnline ++;
				}
				// if member increase member count by 1
				if ($session->id_user != 0) {
					$membersOnline ++;
				}
			}		
			$this->assignRef('membersOnline', $membersOnline);
			$this->assignRef('guestsOnline', $guestsOnline);		
			$this->assignRef('whosOnlineLink', JRoute::_('index.php?option=com_ninjaboard&view=whosonline&Itemid='.$this->Itemid));
		}
		
		$this->assignRef('categories', $categories);
		$this->assignRef('categoriesCount', count($categories));
		$this->assignRef('forums', $forums);
		
		// get online users without guests
		$onlineUsers =& $model->getOnlineUsers(1);
		$this->assignRef('onlineUsers', $onlineUsers);
			
		$this->assignRef('searchInputBoxText', JText::_('NB_SEARCHTHISBOARD'));
		
		// get buttons
		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		$this->assignRef('buttonSearch', $ninjaboardButtonSet->buttonByFunction['buttonSearch']);
		$this->assignRef('actionSearch', JRoute::_('index.php?option=com_ninjaboard&view=search&Itemid='.$this->Itemid));
		
		// get feed settings
		$this->assignRef('enableFeeds', $ninjaboardConfig->getFeedSettings('enable_feeds'));
		$this->assignRef('feedLink', JRoute::_('index.php?option=com_ninjaboard&task=ninjaboardfeed&Itemid='. $this->Itemid));
		
		if ($this->enableFeeds) {
			switch ((int)$ninjaboardConfig->getFeedSettings('feed_items_type')) {
				case 0:
					$feedText = sprintf(JText::_('NB_GETFEEDTOPICS'), $boardName);
					break;
				case 1:
					$feedText = sprintf(JText::_('NB_GETFEEDPOSTS'), $boardName);
					break;
				default:
					break;
			}
			$this->assign('feedText', $feedText);
		}
		
		parent::display($tpl);
	}

	function &getCategory($index = 0) {
		
		$category =& $this->categories[$index];
		$category->categoryLink = JRoute::_('index.php?option=com_ninjaboard&view=board&category='.$category->id.'&Itemid='.$this->Itemid);

		return $category;
	}
	
	function &getForum($forum) {
		
		$forum->forum_link = JRoute::_('index.php?option=com_ninjaboard&view=forum&forum='.$forum->id.'&Itemid='.$this->Itemid);
		
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		if (!$forum->locked) {
			if ((strtotime(gmdate("Y-m-d H:i:s")) - strtotime($forum->date_post)) > ($forum->new_posts_time * 60)) {
				$forum->forumIcon = $this->ninjaboardIconSet->iconByFunction['forumNormal'];
			} else {
				$forum->forumIcon = $this->ninjaboardIconSet->iconByFunction['forumNewPosts'];
			}
		} else {
			$forum->forumIcon = $this->ninjaboardIconSet->iconByFunction['forumLocked'];
		}
		
		$forum->authorLink = '';
		if ($forum->author) {
			$forum->authorLink = JRoute::_('index.php?option=com_ninjaboard&view=profile&id='.$forum->id_user.'&Itemid='.$this->Itemid);
		} else {
			if ($forum->guest_author) {
				$forum->author = $forum->guest_author;
			} else {
				$forum->author = JText::_('NB_GUEST');
			}
		}

		if ($forum->date_post) {
			$forum->date_post = NinjaboardHelper::Date($forum->date_post);
		}
		$forum->lastPostLink = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$forum->id_topic.'&Itemid='.$this->Itemid.'#p'.$forum->id_last_post);
		
		return $forum;
	}
	
	function &getForums($categoryId) {
		
		$catagoryForums = array();
		foreach ($this->forums as $forum) {
			if ($forum->id_cat == $categoryId) {
				$catagoryForums[] = $forum;
			}
		}
		return $catagoryForums;
	}
	
	function &getOnlineUser($index = 0) {
		
		$onlineUser =& $this->onlineUsers[$index];
		
		$onlineUser->userLink = '';
		if ($onlineUser->name) {
			$onlineUser->userLink = JRoute::_('index.php?option=com_ninjaboard&view=profile&id='.$onlineUser->id_user.'&Itemid='.$this->Itemid);
		} else {
			$onlineUser->name = JText::_('NB_GUEST');
		}

		return $onlineUser;
	}
	
	/**
	 * get latest item
	 *
	 * @return object
	 */
	function getLatestItem($index = 0) {
	
		$item =& $this->latestItems[$index];
		
		$item->itemLink = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$item->id_topic.'&Itemid='.$this->Itemid.'#p'.$item->id);
		
		// get the topic icon
		$item->itemIcon = $this->ninjaboardIconSet->iconByFunction[$item->icon_function];
		
		$item->authorLink = '';
		if ($item->author) {
			$item->authorLink = JRoute::_('index.php?option=com_ninjaboard&view=profile&id='.$item->id_user.'&Itemid='.$this->Itemid);
		} else {
			if ($item->guest_author) {
				$item->author = $item->guest_author;
			} else {
				$item->author = JText::_('NB_GUEST');
			}
		}
		
		if ($item->date_post) {
			$item->date_post = NinjaboardHelper::Date($item->date_post);
		}
				
		return $item;
	}

}
?>
