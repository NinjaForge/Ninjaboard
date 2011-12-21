<?php
/**
 * @version $Id: editpost.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Edit Post Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelEditPost extends NinjaboardModel
{

	/**
	 * get topic posts
	 * 
	 * @access public
	 * @return array
	 */
	function getTopicPosts($topicId) {

		$query = "SELECT p.*, t.id_first_post, t.status, ". $this->getUserAs('u') ." AS author, u.id AS id_user, u.registerDate, ju.posts, ju.avatar_file, ju.show_online_state"
				. "\n FROM #__ninjaboard_posts AS p"
				. "\n INNER JOIN #__ninjaboard_topics AS t ON t.id = p.id_topic"
				. "\n LEFT JOIN #__users AS u ON p.id_user = u.id"
				. "\n LEFT JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
				. "\n WHERE p.id_topic = $topicId"
				. "\n ORDER BY p.date_post DESC"
				;
		return $this->_getList($query);
	}

}
?>