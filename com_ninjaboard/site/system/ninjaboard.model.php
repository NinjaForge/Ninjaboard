<?php
/**
 * @version $Id: ninjaboard.model.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Ninjaboard Model
 *
 * @package Ninjaboard
 */
class NinjaboardModel extends JModel
{
	/**
	 * category table object
	 *
	 * @var object
	 */
	var $_category = null;
	
	/**
	 * forum table object
	 *
	 * @var object
	 */
	var $_forum = null;
	
	/**
	 * topic table object
	 *
	 * @var object
	 */
	var $_topic = null;
	
	/**
	 * The subject of the first post of the topic
	 *
	 * @var object
	 */
	var $_topicSubject = null;	

	/**
	 * post table object
	 *
	 * @var object
	 */
	var $_post = null;

	/**
	 * post table object
	 *
	 * @var object
	 */
	var $_postQuote = null;	
	
	/**
	 * ranks data array
	 *
	 * @var array
	 */
	var $_ranks = null;
	
	/**
	 * total topics
	 *
	 * @var integer
	 */
	var $_totalTopics = null;
	
	/**
	 * total posts
	 *
	 * @var integer
	 */
	var $_totalPosts = null;
	
	/**
	 * total members
	 *
	 * @var integer
	 */
	var $_totalMembers = null;
	
	/**
	 * latest member
	 *
	 * @var string
	 */
	var $_latestMember = null;
	
	/**
	 * latest posts
	 *
	 * @var array
	 */
	var $_latestItems = null;
		
	/**
	 * sessions data array
	 *
	 * @var array
	 */
	var $_sessions = null;
	
	/**
	 * ninjaboard user
	 *
	 * @var object
	 */
	var $_ninjaboardUser = null;
	
	/**
	 * online users
	 *
	 * @var array
	 */
	var $_onlineUsers = null;
			
	/**
	 * get the category object
	 *
	 * @access public
	 * @return object
	 */
	function getCategory($id_cat) {

		// load the category
		if (empty($this->_category)) {
			$this->_category =& JTable::getInstance('NinjaboardCategory', 'Table');
		}
		$this->_category->load($id_cat);
			
		return $this->_category;
	}
				
	/**
	 * get the forum object
	 *
	 * @access public
	 * @return object
	 */
	function getForum($id_forum) {

		// load the forum
		if (empty($this->_forum)) {
			$this->_forum =& JTable::getInstance('NinjaboardForum');
		}
		$this->_forum->load($id_forum);
			
		return $this->_forum;
	}
	
	/**
	 * get the topic object
	 * 
	 * @access public
	 * @return object
	 */
	function getTopic($id_topic) {

		// load the topic
		if (empty($this->_topic)) {
			$this->_topic =& JTable::getInstance('NinjaboardTopic');
			$this->_topic->load($id_topic);
		}
		
		return $this->_topic;
	}
	
	/**
	 * get the subject of the topic
	 * 
	 * @access public
	 * @return object
	 */
	function getTopicSubject($id_topicFirstPost) {

		// load the post
		if (empty($this->_post)) {
			$tempPost =& JTable::getInstance('NinjaboardPost');
		}
		$tempPost->load($id_topicFirstPost);
		
		if ($tempPost){
			$this->_topicSubject = $tempPost->subject;
		} else {
			$this->_topicSubject = null;
		}		
		
		return $this->_topicSubject;
	}
	
	/**
	 * get the post object
	 * 
	 * @access public
	 * @return object
	 */
	function getPost($id_post) {

		// load the post
		if (empty($this->_post)) {
			$this->_post =& JTable::getInstance('NinjaboardPost');
		}
		$this->_post->load($id_post);
		
		return $this->_post;
	}
	
	/**
	 * get the post quote object
	 * 
	 * @access public
	 * @return object
	 */
	function getPostQuote($id_post) {

		// load the post
		if (empty($this->_postQuote)) {
			$this->_postQuote =& JTable::getInstance('NinjaboardPost');
		}
		$this->_postQuote->load($id_post);
		
		return $this->_postQuote;
	}
	
	/**
	 * get ranks
	 * 
	 * @access public
	 * @return array
	 */
	function getRanks() {
	
		// load ranks
		if (empty($this->_ranks)) {	
			$query = "SELECT r.*"
					. "\n FROM #__ninjaboard_ranks AS r"
					. "\n ORDER BY r.min_posts DESC"
					;
			$this->_ranks = $this->_getList($query);
		}
		
		return $this->_ranks;
	}
	
	/**
	 * get total topics
	 * 
	 * @access public
	 * @return integer
	 */
	function getTotalTopics() {
	
		// load total topics
		if (empty($this->_totalTopics)) {
			$db =& $this->getDBO();	
			$query = "SELECT SUM(topics)" .
					 "\n FROM #__ninjaboard_forums"
					 ;
			$db->setQuery($query);
			$this->_totalTopics = $db->loadResult();
		}
		
		return $this->_totalTopics;
	}
	
	/**
	 * get total posts
	 * 
	 * @access public
	 * @return integer
	 */
	function getTotalPosts() {
	
		// load posts
		if (empty($this->_totalPosts)) {
			$db =& $this->getDBO();	
			$query = "SELECT SUM(posts)" .
					 "\n FROM #__ninjaboard_forums"
					 ;
			$db->setQuery($query);
			$this->_totalPosts = $db->loadResult();
		}
		
		return $this->_totalPosts;
	}
	
	/**
	 * get total members
	 * 
	 * @access public
	 * @return integer
	 */
	function getTotalMembers() {
	
		// load total members
		if (empty($this->_totalMembers)) {
			$db =& $this->getDBO();	
			$query = "SELECT COUNT(*)"
					 . "\n FROM #__users AS u"
					 . "\n INNER JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
					 ;
			$db->setQuery($query);
			$this->_totalMembers = $db->loadResult();
		}
		
		return $this->_totalMembers;
	}
	
	/**
	 * get latest member
	 * 
	 * @access public
	 * @return integer
	 */
	function getLatestMember() {
	
		// load latest member
		if (empty($this->_latestMember)) {
			$db =& $this->getDBO();
				
			// ToDo: there should be a better solution!
			$query = "SELECT u.id"
					. "\n FROM #__users AS u"
					. "\n ORDER BY u.registerDate DESC"
					;
			$db->setQuery($query);
			
			$this->_latestMember =& NinjaboardUser::getInstance($db->loadResult());
		}
		
		return $this->_latestMember;
	}
	
	/**
	 * get sessions
	 * 
	 * @access public
	 * @return array
	 */
	function getSessions() {
	
		// load sessions
		if (empty($this->_sessions)) {	
			$query = 'SELECT id_user' .
					' FROM #__ninjaboard_session'
					;
			$this->_sessions = $this->_getList($query);
		}
		
		return $this->_sessions;
	}
	
	/**
	 * get ninjaboard user
	 *
	 * @return object
	 */
	function getNinjaboardUser($id_user) {

		// load the ninjaboard user
		if (empty($this->_ninjaboardUser)) {
			$this->_ninjaboardUser =& JTable::getInstance('NinjaboardUser');
		}
		$this->_ninjaboardUser->load($id_user);
			
		return $this->_ninjaboardUser;
	}

	/**
	 * get online users
	 * 
	 * @access public
	 * @return array
	 */
	function getOnlineUsers($filterGuests = 0) {
		
		$where = '';
		if ($filterGuests) {
			$where = "\n WHERE s.id_user <> 0";
		}
		
		// load online users
		if (empty($this->_onlineUsers)) {	
			$query = "SELECT s.id_user, s.action_time, s.current_action, s.action_url, ". $this->getUserAs('u') ." AS name"
					. "\n FROM #__ninjaboard_session AS s"
					. "\n LEFT JOIN #__users AS u ON u.id = s.id_user"
					. $where
					;
			$this->_onlineUsers = $this->_getList($query);
		}
		
		return $this->_onlineUsers;
	}

	/**
	 * get latest items
	 * 
	 * @access public
	 * @return array
	 */
	function getLatestItems() {
	
		// load latest posts
		if (empty($this->_latestItems)) {
			$ninjaboardConfig =& NinjaboardConfig::getInstance();
			
			// get the count of items to show
			$limit = (int)$ninjaboardConfig->getBoardSettings('latest_items_count');
			
			// items to show. topics or posts?
			switch ((int)$ninjaboardConfig->getBoardSettings('latest_items_type')) {
				case 0:
					$innerJoin = "\n INNER JOIN #__ninjaboard_topics AS t ON t.id_first_post = p.id";
					break;
				case 1:
					$innerJoin = "\n INNER JOIN #__ninjaboard_topics AS t ON t.id = p.id_topic";
					break;
				default:
					break;
			}
							
			$query = "SELECT p.*, t.id_first_post, t.status, ". $this->getUserAs('u') ." AS author, pg.guest_name AS guest_author, "
					. "\n u.id AS id_user, u.registerDate, ju.posts, ju.avatar_file, ju.show_online_state, f.name AS forum_name, c.name AS category_name"
					. "\n FROM #__ninjaboard_posts AS p"
					. $innerJoin
					. "\n INNER JOIN #__ninjaboard_forums AS f ON f.id = p.id_forum"
					. "\n INNER JOIN #__ninjaboard_categories AS c ON c.id = f.id_cat"
					. "\n LEFT JOIN #__users AS u ON p.id_user = u.id"
					. "\n LEFT JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
					. "\n LEFT JOIN #__ninjaboard_posts_guests AS pg ON p.id = pg.id_post"
					. "\n ORDER BY p.date_post DESC LIMIT 0, $limit"
					;

			$this->_latestItems = $this->_getList($query);
		}
		
		return $this->_latestItems;
	}
	
	/**
	 * get user as
	 *
	 * @return string
	 */
	function getUserAs($alias) {
		$ninjaboardConfig =& NinjaboardConfig::getInstance();
		return ($ninjaboardConfig->getViewSettings('show_user_as') == 0) ? "$alias.name" : "$alias.username";
	}
		
}
?>