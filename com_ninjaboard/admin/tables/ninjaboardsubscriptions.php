<?php
/**
 * @version $Id: ninjaboardsubscriptions.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
/**
 * Ninjaboard Subscriptions Table Class
 *
 * @package Ninjaboard
 */
class JTableNinjaboardSubscription extends JTable {
	/** @var int */
	var $subs_type		= null;
	/** @var int userid*/
	var $id_user			= null;
	/** @var string */
	var $guest_name			= null;
	/** @var string */
	var $guest_email		= null;
	/** @var int */
	var $id_subs_item		= null;
	/** @var int */
	var $one_mail_or_many	= null;
	/** @var int */
	var $mail_sent			= null;
														
	/**
	 * @param database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__ninjaboard_subscriptions', 'id', $db);
	}
	
}
?>