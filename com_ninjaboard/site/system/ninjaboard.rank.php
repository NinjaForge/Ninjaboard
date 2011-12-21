<?php
/**
 * @version $Id: ninjaboard.rank.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Rank
 *
 * @package Ninjaboard
 */
class NinjaboardRank
{
	
	/**
	 * ranks data array
	 *
	 * @var array
	 */
	var $ranks = null;
	
	/**
	 * total ranks
	 *
	 * @var integer
	 */
	var $total = null;
	
	/**
	 * get ninjaboard rank object
	 *
	 * @access public
	 * @return object of NinjaboardRank
	 */		
	function NinjaboardRank() {
		$this->loadRanks();
	}
	
	/**
	 * get ninjaboard rank object
	 *
	 * @access public
	 * @return object of NinjaboardRank
	 */
	function &getInstance() {
	
		static $ninjaboardRank;

		if (!is_object($ninjaboardRank)) {
			$ninjaboardRank = new NinjaboardRank();
		}

		return $ninjaboardRank;
	}
		
	/**
	 * load ranks
	 * 
	 * @access public
	 */
	function loadRanks() {
	
		// load ranks
		if (empty($this->ranks)) {
			$db		=& JFactory::getDBO();
				
			$query = "SELECT r.*"
					. "\n FROM #__ninjaboard_ranks AS r"
					. "\n ORDER BY r.min_posts DESC"
					;
			$db->setQuery($query);
			
			$this->ranks = $db->loadObjectList();
			$this->total = count($this->ranks);
		}
	}
	
	/**
	 * get rank
	 * 
	 * @access public
	 * @return object
	 */
	function getRank($totalUserPosts) {

		$rank = null;
		for ($i=0; $i < $this->total; $i++) {
			if ($this->ranks[$i]->min_posts <= $totalUserPosts) {
				$rank = $this->ranks[$i];
				break;
			}
		}
			
		return $rank;
	}

}
?>