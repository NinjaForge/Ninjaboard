<?php
/**
 * @version $Id: search.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Search Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelSearch extends NinjaboardModel
{

	/**
	 * get search results
	 * 
	 * @access public
	 * @return array
	 */
	function getSearchResults() {
		$searchWords = JRequest::getVar('searchwords', '');
		
		$searchWords = trim($searchWords);
		$searchWords = strtolower($searchWords);
		
		$where = $this->_buildWhere($searchWords);
		
		$query = '';
		if ($searchWords != '') {
			$query = "SELECT p.*, t.id_first_post, t.status, ". $this->getUserAs('u') ." AS author, u.id AS id_user, "
					. "\n u.registerDate, ju.posts, ju.avatar_file, ju.show_online_state, lg.guest_name AS guest_author"
					. "\n FROM #__ninjaboard_posts AS p"
					. "\n INNER JOIN #__ninjaboard_topics AS t ON t.id = p.id_topic"
					. "\n LEFT JOIN #__users AS u ON p.id_user = u.id"
					. "\n LEFT JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
					. "\n LEFT JOIN #__ninjaboard_posts_guests AS lg ON p.id = lg.id_post"
					. $where
					. "\n ORDER BY p.date_post DESC"
					;
		}
	
		return $this->_getList($query);
	}
	
	function _buildWhere($searchWords) {
		$forumId = JRequest::getVar('forum', 0, '', 'int');
	
		$where = '';
		if ($forumId) {
			$where = "\n WHERE LOWER(p.subject) LIKE '%$searchWords%'"
					. "\n AND p.id_forum = $forumId"
					. "\n OR LOWER(p.text) LIKE '%$searchWords%'"
					. "\n AND p.id_forum = $forumId"
					;
		} else {
			$where = "\n WHERE LOWER(p.subject) LIKE '%$searchWords%'"
					. "\n OR LOWER(p.text) LIKE '%$searchWords%'"
					;		
		}

		return $where;
	}
}
?>