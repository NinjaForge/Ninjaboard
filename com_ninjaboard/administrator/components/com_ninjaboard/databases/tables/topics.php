<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: topics.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	 	http://ninjaforge.com
 */

class ComNinjaboardDatabaseTableTopics extends KDatabaseTableDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config = null)
	{
		$config->append(array(
			'filters' => array(
				'params' => 'json'
			),
			'behaviors' => array(
				'hittable',
				//KFactory::tmp('admin::com.ninjaboard.behavior.configurable')
			)
		));
		
		parent::__construct($config);
	}

	/**
	 * Table delete method
	 *
	 * @param  object	A KDatabaseRow object
	 * @return boolean  TRUE if successfull, otherwise false
	 */
	public function delete( KDatabaseRowInterface $row )
	{
		
		
		parent::delete($row);
		
		
	}
}