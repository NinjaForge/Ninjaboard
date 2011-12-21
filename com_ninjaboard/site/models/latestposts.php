<?php
/**
 * @version $Id: latestposts.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Latest Posts Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelLatestPosts extends NinjaboardModel
{

	/**
	 * get latest posts
	 * 
	 * @access public
	 * @return array
	 */
	function getLatestPosts() {
		$query = $this->_buildLatestPostsQuery();
		return $this->_getList($query);
	}
	
	function _buildLatestPostsQuery() {
		$ninjaboardConfig =& NinjaboardConfig::getInstance();
		
		$where = '';
		if ($ninjaboardConfig->getLatestPostSettings('enable_filter')) {
			$hours = JRequest::getVar('hours', NULL, '', 'int');
		
			// ToDo: find a better solution ;)
			$factor = 0;
			if (isset($hours)) {
				$factor = $hours * 3600; // 60 * 60
			} else {
				$days = JRequest::getVar('days', NULL, '', 'int');
				if (isset($days)) {
					$factor = $days * 86400; // 24 * 60 * 60
				} else {
					$weeks = JRequest::getVar('weeks', NULL, '', 'int');
					if (isset($weeks)) {
						$factor = $weeks * 604800; // 7 * 24 * 60 * 60
					} else {
						$months = JRequest::getVar('months', NULL, '', 'int');
						if (isset($months)) {
							$factor = $months * 2592000; // 30 * 24 * 60 * 60
						} else {
							$years = JRequest::getVar('years', NULL, '', 'int');
							if (isset($years)) {
								$factor = $years * 31536000; // 365 * 24 * 60 * 60
							} else {
								$factor = 3600;
							}						
						}					
					}				
				}
			}
	
			$dateTime = gmdate("Y-m-d H:i:s", time() - $factor);
			$where = "\n AND p.date_post > '$dateTime'";			
		}

		$query = "SELECT p.*, t.id_first_post, t.status, ". $this->getUserAs('u') ." AS author, u.id AS id_user, u.registerDate, "
				. "\n ju.posts, ju.avatar_file, ju.show_online_state, ju.signature"
				. "\n FROM #__ninjaboard_posts AS p"
				. "\n INNER JOIN #__ninjaboard_topics AS t ON t.id = p.id_topic"
				. "\n LEFT JOIN #__users AS u ON p.id_user = u.id"
				. "\n LEFT JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
				. "\n LEFT JOIN #__ninjaboard_forums AS jf ON jf.id = t.id_forum"
				. "\n WHERE jf.status = 1"
				. $where
				. "\n ORDER BY p.date_post DESC"
				;

		return $query;
	}

}
?>
