<?php
/**
 * @version $Id: movetopic.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Move Topic Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelMoveTopic extends NinjaboardModel
{
	
	/**
	 * forums data array
	 *
	 * @var array
	 */
	var $_forums = null;

	/**
	 * get the forums
	 * 
	 * @access public
	 * @return array
	 */
	function getForums($forumId) {
	
		// load the forums
		if (empty($this->_forums)) {
			$query = "SELECT f.*, c.name AS category_name"
					. "\n FROM #__ninjaboard_forums AS f"
					. "\n INNER JOIN #__ninjaboard_categories AS c ON f.id_cat = c.id"
					. "\n WHERE f.state = 1"
					. "\n AND f.id <> $forumId"
					. "\n ORDER BY f.ordering"
					;
			$this->_forums = $this->_getList($query);
		}
		
		return $this->_forums;
	}
	
}
?>