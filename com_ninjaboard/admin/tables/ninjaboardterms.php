<?php
/**
 * @version $Id: ninjaboardterms.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Terms Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardTerms extends JTable {
	/** @var int Unique id*/
	var $id					= null;
	/** @var string */
	var $terms				= null;
	/** @var string */
	var $termstext			= null;
	/** @var string */
	var $agreement			= null;
	/** @var string */
	var $agreementtext		= null;	
	/** @var string */
	var $locale				= null;	
	/** @var int */
	var $published			= null;
	/** @var int */
	var $checked_out		= null;	
	/** @var datetime */
	var $checked_out_time	= null;	
														
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_terms', 'id', $db);
	}
	
}
?>