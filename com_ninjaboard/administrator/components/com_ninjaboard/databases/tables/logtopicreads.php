<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: logtopicreads.php 1915 2011-05-22 23:44:43Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	 	http://ninjaforge.com
 */

class ComNinjaboardDatabaseTableLogtopicreads extends KDatabaseTableDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$config->append(array(
		    'name'      => 'ninjaboard_log_topic_reads',
		    'behaviors' => array('creatable')
		));
	
		parent::__construct($config);
	}
}