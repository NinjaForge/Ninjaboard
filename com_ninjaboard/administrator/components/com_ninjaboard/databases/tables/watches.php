<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Watches Table
 *
 * Contains all the watches, in order to notify people subscribed to a person, forum or topic
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTableWatches extends KDatabaseTableAbstract
{
	/**
	 * Subscription type constants for the subscription_type column
	 *
	 * @var array
	 */
	private $_types = array(
		'forum'		=> 1,
		'person'	=> 2,
		'topic'		=> 3
	);

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$config->name		= 'ninjaboard_subscriptions';
		$config->identity_column	= 'ninjaboard_subscription_id';
		
		$config->behaviors = array('creatable');
		
		parent::__construct($config);
	}
	
	/**
	 * Translates the subcription name to the type id
	 *
	 * @param  string			$type	name, like 'forum'
	 * @return integer|boolean			the type id, or false when the type is undefined
	 */
	public function getTypeIdFromName($type)
	{
		return isset($this->_types[$type]) ? $this->_types[$type] : false;
	}
}