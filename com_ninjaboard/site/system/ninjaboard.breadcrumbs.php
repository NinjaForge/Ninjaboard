<?php
/**
 * @version $Id: ninjaboard.breadcrumbs.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Bread Crumbs
 *
 * @package Ninjaboard
 */
class NinjaboardBreadCrumbs
{
	/**
	 * bread crumbs data
	 *
	 * @var array
	 */
	var $_breadCrumbs = null;
	
	/**
	 * bread crumb max length
	 *
	 * @var integer
	 */
	var $_maxLength;
	
	function NinjaboardBreadCrumbs() {
		global $mainframe;
		
		// initialize variables
		$ninjaboardConfig =& NinjaboardConfig::getInstance();
		$this->_maxLength = $ninjaboardConfig->getBoardSettings('breadcrumb_max_length');
	}
	
	/**
	 * get instance
	 *
	 * @access public
	 * @return object
	 */
	function &getInstance() {
	
		static $ninjaboardBreadCrumbs;

		if (!is_object($ninjaboardBreadCrumbs)) {
			$ninjaboardBreadCrumbs = new NinjaboardBreadCrumbs();
		}

		return $ninjaboardBreadCrumbs;
	}
		
	function addBreadCrumb($name, $href = '') {

		if (strlen($name) > $this->_maxLength) {
			$name = substr($name, 0, $this->_maxLength).'...';
		}

		$breadCrumb->name = $name;
		$breadCrumb->href = $href;
		$this->_breadCrumbs[] = $breadCrumb;
	}
			
	function getBreadCrumbs() {
		return $this->_breadCrumbs;
	}
						
}
?>