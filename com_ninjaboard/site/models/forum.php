<?php
/**
 * @version $Id: forum.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Forum Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelForum extends NinjaboardModel
{
	/**
	 * announcements data array
	 *
	 * @var array
	 */
	var $_announcements = null;
	
	/**
	 * topics data array
	 *
	 * @var array
	 */
	var $_topics = null;

	/**
	 * total count of topics
	 *
	 * @var integer
	 */
	var $_total = 0;
		
	/**
	 * get announcements
	 * 
	 * @access public
	 * @return array
	 */
	function getAnnouncements() {
	
		// load announcements
		if (empty($this->_announcements)) {	
			$query = $this->_buildTopicsQuery("\n AND t.type = 2");
			$this->_announcements = $this->_getList($query);
		}
		
		return $this->_announcements;
	}

	/**
	 * get topics
	 * 
	 * @access public
	 * @return array
	 */
	function getTopics() {
	
		// load topics
		if (empty($this->_topics)) {
			$ninjaboardConfig =& NinjaboardConfig::getInstance();
			$limit = JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('topics_per_page'), '', 'int');
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
			
			$query = $this->_buildTopicsQuery("\n AND t.type < 2");
			$this->_total = $this->_getListCount($query);
			$this->_topics = $this->_getList($query, $limitstart, $limit);
		}
		
		return $this->_topics;
	}

	function _buildTopicsQuery($type) {
		$forum	= JRequest::getVar('forum', 0, '', 'int');
		
		$query = "SELECT t.*, fp.subject, fp.date_post AS date_topic, fp.id_user AS id_author, fp.icon_function, fp.text,"
				. "\n ". $this->getUserAs('fu') ." AS author, fg.guest_name AS guest_author, lp.subject AS subject_last_post,"
				. "\n lp.date_post AS date_last_post, lp.id_user AS id_poster, ". $this->getUserAs('lu') ." AS poster,"
				. "\n lg.guest_name AS guest_poster"
				. "\n FROM #__ninjaboard_topics AS t"
				. "\n INNER JOIN #__ninjaboard_posts AS fp ON t.id_first_post = fp.id"
				. "\n LEFT JOIN #__users AS fu ON fp.id_user = fu.id"
				. "\n LEFT JOIN #__ninjaboard_posts_guests AS fg ON fp.id = fg.id_post"
				. "\n INNER JOIN #__ninjaboard_posts AS lp ON t.id_last_post = lp.id"
				. "\n LEFT JOIN #__users AS lu ON lp.id_user = lu.id"
				. "\n LEFT JOIN #__ninjaboard_posts_guests AS lg ON lp.id = lg.id_post"
				. "\n WHERE t.id_forum = ". $forum
				. $type
				. "\n ORDER BY t.type DESC, lp.date_post DESC";

		return $query;
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

}
?>