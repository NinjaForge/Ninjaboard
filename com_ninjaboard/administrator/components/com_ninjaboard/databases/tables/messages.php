<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: messages.php 1622 2011-03-01 21:16:11Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	 	http://ninjaforge.com
 */

class ComNinjaboardDatabaseTableMessages extends KDatabaseTableDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$config->append(array(
			'filters' => array(
				'text'	 => 'raw'
			),
			'behaviors' => array(
				'creatable'
			)
		));
	
		parent::__construct($config);
	}
}