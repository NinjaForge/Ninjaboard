<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: posts.php 2472 2011-11-02 03:19:21Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	 	http://ninjaforge.com
 */

class ComNinjaboardDatabaseTablePosts extends KDatabaseTableDefault
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
				//@TODO turn back on once bbcode parser is stable
				//'text'	 => 'com://admin/ninjaboard.filter.bbcode',
				'text'	 => 'raw',
				'params' => 'json'
			),
			'behaviors' => array(
				'creatable',
				'modifiable',
				//$this->getService('com://admin/ninjaboard.behavior.configurable'),
				'com://site/ninjaboard.database.behavior.postable'
			)
		));
	
		parent::__construct($config);

		$this->_column_map = array_merge(
			$this->_column_map,
			array(
				'created_by'	=> 'created_user_id',
				'created_on'	=> 'created_time',
				'modified_by'	=> 'modified_user_id',
				'modified_on'	=> 'modified'
			)
		);
	}
}