<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
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
				'ninja:database.behavior.hittable',
				'com://admin/ninjaboard.database.behavior.configurable'
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
	
	/**
	 * Table maintenance method
	 *
	 * Runs a maintenance procedure
	 */
	public function maintenance()
	{
	    // Preventing timeouts
	    @set_time_limit(300);

        // Run as raw query, as some sites have huge amounts of data so we need it fast
		$query = 'UPDATE `#__ninjaboard_topics`, `#__ninjaboard_posts` SET `#__ninjaboard_topics`.`last_post_on` = `#__ninjaboard_posts`.`created_time`, `#__ninjaboard_topics`.`last_post_by` = `#__ninjaboard_posts`.`created_user_id` WHERE `#__ninjaboard_topics`.`last_post_id` = `#__ninjaboard_posts`.`ninjaboard_post_id`;';
		$this->getDatabase()->execute($query);
		
		// Delete orphan topics
		$tbl   = '`#__ninjaboard_topics`';
		$query = 'DELETE FROM '.$tbl.' USING '.$tbl.' LEFT  JOIN `#__ninjaboard_posts` AS `post` ON (`post`.`ninjaboard_post_id` = '.$tbl.'.`first_post_id`) WHERE ISNULL(`post`.`ninjaboard_post_id`)';
		$this->getDatabase()->execute($query);
		
		// Delete orphan posts, since we just deleted topics
		// @TODO make these two smarter, and make the most recent post in a topic the first post instead of deleting everything
		$tbl   = '`#__ninjaboard_posts`';
		$query = 'DELETE FROM '.$tbl.' USING '.$tbl.' LEFT  JOIN `#__ninjaboard_topics` AS `topic` ON (`topic`.`ninjaboard_topic_id` = '.$tbl.'.`ninjaboard_topic_id`) WHERE ISNULL(`topic`.`ninjaboard_topic_id`)';
		$this->getDatabase()->execute($query);

		return true;
	}
}