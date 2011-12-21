<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: logtopicreads.php 2460 2011-10-11 21:21:19Z stian $
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

	/**
	 * Table maintenance method
	 *
	 * Will remove any orphan log entries, and add log entries where appropriate
	 *
	 * Runs a maintenance procedure
	 */
	public function maintenance()
	{
	    // Delete all the orphans
	    $tbl   = '`#__ninjaboard_log_topic_reads`';
		$query = 'DELETE FROM '.$tbl.' USING '.$tbl.' LEFT  JOIN `#__ninjaboard_people` AS `person` ON (`person`.`ninjaboard_person_id` = '.$tbl.'.`created_by`)  LEFT  JOIN `#__ninjaboard_forums` AS `forum` ON (`forum`.`ninjaboard_forum_id` = '.$tbl.'.`ninjaboard_forum_id`)  LEFT  JOIN `#__ninjaboard_topics` AS `topic` ON (`topic`.`ninjaboard_topic_id` = '.$tbl.'.`ninjaboard_topic_id` AND `topic`.`forum_id` = '.$tbl.'.`ninjaboard_forum_id`) WHERE  ISNULL(`person`.`ninjaboard_person_id`) OR ISNULL(`forum`.`ninjaboard_forum_id`) OR ISNULL(`topic`.`ninjaboard_topic_id`)';
		$this->getDatabase()->execute($query);
        
        // Run as raw query, as some sites have huge amounts of data so we need it fast
        $me    = $this->getService('com://admin/ninjaboard.model.people')->getMe();
        $sub   = 'SELECT `tbl`.`last_post_by` AS `created_by` , `tbl`.`last_post_on` AS `created_on` , `tbl`.`forum_id` AS `ninjaboard_forum_id`, `tbl`.`ninjaboard_topic_id` FROM `#__ninjaboard_topics` AS `tbl` WHERE `tbl`.last_post_by = '.$me->id.'';
        $query = '
        INSERT INTO `#__ninjaboard_log_topic_reads` (`created_by`, `created_on`, `ninjaboard_forum_id`, `ninjaboard_topic_id`)
            '.$sub.'
            ON DUPLICATE KEY UPDATE `created_on` = `tbl`.`last_post_on`
        ;';
        $this->getDatabase()->execute($query);

		return true;
	}
}