<?php
/**
 * @version $Id: board.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Board Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelBoard extends NinjaboardModel
{
	/**
	 * categories data array
	 *
	 * @var array
	 */
	var $_categories = null;
	
	/**
	 * forums data array
	 *
	 * @var array
	 */
	var $_forums = null;

	/**
	 * get the categories
	 * 
	 * @access public
	 * @return array
	 */
	function getCategories() {
	
		// load the categories
		if (empty($this->_categories)) {	
			$query = $this->_buildCategoriesQuery();
			$this->_categories = $this->_getList($query);
		}
		
		return $this->_categories;
	}
	
	function _buildCategoriesQuery() {

		$query = "SELECT c.*"
				. "\n FROM #__ninjaboard_categories AS c"
				. "\n WHERE c.published = 1"
				. "\n ORDER BY c.ordering"
				;
			
		return $query;
	}
	
	/**
	 * get the forums
	 * 
	 * @access public
	 * @return array
	 */
	function getForums() {
	
		// load the forums
		if (empty($this->_forums)) {	
			$query = $this->_buildForumsQuery();
			$this->_forums = $this->_getList($query);
		}
		
		return $this->_forums;
	}
		
	function _buildForumsQuery() {

		// Get the WHERE clause for the query
		$where	 = $this->_buildForumsWhere();
		
		$query = "SELECT f.*, lp.date_post, lp.id_topic, lp.id_user, lp.subject AS subject_last_post, ". $this->getUserAs('u') ." AS author, lg.guest_name AS guest_author"
				. "\n FROM #__ninjaboard_forums AS f"
				. "\n LEFT JOIN #__ninjaboard_posts AS lp ON f.id_last_post = lp.id"
				. "\n LEFT JOIN #__users AS u ON lp.id_user = u.id"
				. "\n LEFT JOIN #__ninjaboard_posts_guests AS lg ON lp.id = lg.id_post"
				. $where
				. "\n ORDER BY f.ordering"
				;
	
		return $query;
	}

	function _buildForumsWhere() {
		
		$category = JRequest::getVar('category', 0, '', 'int');
		
		$where = "\n WHERE f.state = 1";	
		
		if ($category != 0) {
			$where .= "\n AND f.id_cat = ". $category;
		}

		return $where;
	}

}
?>