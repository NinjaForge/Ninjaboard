<?php
/**
 * @version $Id: topic.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Topic Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelTopic extends NinjaboardModel
{
	/**
	 * posts data array
	 *
	 * @var array
	 */
	var $_posts = null;

	/**
	 * total count of posts
	 *
	 * @var integer
	 */
	var $_total = 0;
	
	/**
	 * firstpost table object 
	 *
	 * @var object
	 */
	var $_firstpost = null;

	/**
	 * get firstpost object
	 *
	 * @return object
	 */
	function getFirstPost($id_firstpost) {

		// load the topic
		if (empty($this->_firstpost)) {
			$this->_firstpost =& JTable::getInstance('NinjaboardPost');
		}
		$this->_firstpost->load($id_firstpost);
			
		return $this->_firstpost;
	}
	
	/**
	 * get posts
	 * 
	 * @access public
	 * @return array
	 */
	function getPosts() {
	
		// load posts
		if (empty($this->_posts)) {
			$ninjaboardConfig =& NinjaboardConfig::getInstance();
			$limit = JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('posts_per_page'), '', 'int');
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');	
			$topicId	= JRequest::getVar('topic', 0, '', 'int');
	
			$query = "SELECT p.*, t.id_first_post, t.status, ". $this->getUserAs('u') ." AS author, u.id AS id_user, "
					. "\n u.registerDate, ju.posts, ju.avatar_file, ju.show_online_state, ju.signature, "
					. "\n lg.guest_name AS guest_author"
					. "\n FROM #__ninjaboard_posts AS p"
					. "\n INNER JOIN #__ninjaboard_topics AS t ON t.id = p.id_topic"
					. "\n LEFT JOIN #__users AS u ON p.id_user = u.id"
					. "\n LEFT JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
					. "\n LEFT JOIN #__ninjaboard_posts_guests AS lg ON p.id = lg.id_post"
					. "\n WHERE p.id_topic = $topicId"
					. "\n ORDER BY p.id"
					;
			$this->_total = $this->_getListCount($query);		
			$this->_posts = $this->_getList($query, $limitstart, $limit);
			
		}
		
		return $this->_posts;
	}

	/**
	 * get total
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal() {
		return $this->_total;
	}
	
	/**
	 * increment hit
	 *
	 * @access public
	 * @return boolean
	 */
	function incrementHit($id_topic) {
		$db			=& JFactory::getDBO();
		$session	=& JFactory::getSession();
		
		if (!empty($this->_topic)) {
		
			$topicHits = array();
			$topicHits = $session->get('_ninjaboardTopicHits', $topicHits);
			if (in_array($id_topic, $topicHits)) {
				return false;
			}		
			$topicHits[] = $id_topic;
			$session->set('_ninjaboardTopicHits', $topicHits);		

			$this->_topic->views++;
			if (!$this->_topic->store()) {
				JError::raiseError(500, $this->_topic->getError());
			}			
			return true;
		}
		return false;
	}	

}
?>