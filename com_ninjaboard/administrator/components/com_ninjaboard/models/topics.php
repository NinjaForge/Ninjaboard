<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: topics.php 1920 2011-05-23 12:14:11Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Topics model
 *
 * Fetches forums, and such
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelTopics extends ComDefaultModelDefault
{

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_state
						->insert('forum', 'int')
						->insert('post' , 'int')
						->insert('at'	, 'int', false);
	}

	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
		parent::_buildQueryJoins($query);

		$query->join('LEFT', 'ninjaboard_posts AS first_post', 'first_post.ninjaboard_post_id = tbl.first_post_id')
				->join('LEFT', 'ninjaboard_posts AS last_post', 'last_post.ninjaboard_post_id = tbl.last_post_id')
				->join('LEFT', 'users AS first_post_user', 'first_post_user.id = first_post.created_user_id')
				->join('LEFT', 'users AS last_post_user', 'last_post_user.id = last_post.created_user_id')
				->join('LEFT', 'ninjaboard_people AS first_post_person', 'first_post_person.ninjaboard_person_id = first_post.created_user_id')
				->join('LEFT', 'ninjaboard_people AS last_post_person', 'last_post_person.ninjaboard_person_id = last_post.created_user_id')
				->join('LEFT', 'ninjaboard_forums AS forum', 'forum.ninjaboard_forum_id = tbl.forum_id')
				->join('LEFT', 'ninjaboard_topic_symlinks AS symlink', '(symlink.ninjaboard_topic_id = tbl.ninjaboard_topic_id AND symlink.ninjaboard_forum_id != tbl.forum_id)');

        if(!KFactory::get('lib.joomla.user')->guest)
        {
            $me = KFactory::get('admin::com.ninjaboard.model.people')->getMe();

    		$query->join('left', 'ninjaboard_log_topic_reads AS log', 
    		    'log.created_by = '.$me->id.' AND '.
    		    'log.ninjaboard_forum_id = tbl.forum_id AND '.
    		    'log.ninjaboard_topic_id = tbl.ninjaboard_topic_id'
    		);
    	}
	}
	
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);
		
		if($this->_state->at) $query->where('first_post.created_user_id', '=', $this->_state->at);
		
		// @TODO commeted out until we figure out why it breaks the posting
		//$query->where('first_post.subject', '!=', '', 'AND');
		if($this->_state->forum) {
			$forum = $this->_state->forum;
			$query->where("( `tbl`.`forum_id` = '$forum' OR ('$forum' = `symlink`.`ninjaboard_forum_id` AND `tbl`.`show_symlinks` = 1 ) )");
		}
		
		$query->where('forum.ninjaboard_forum_id', '!=', '')
			  ->where('forum.enabled', '=', 1)
			  ->where('tbl.enabled', '=', 1);
		
		//Building the permissions query WHERE clause
		KFactory::get('admin::com.ninjaboard.model.people')->buildForumsPermissionsWhere($query, 'forum.ninjaboard_forum_id');
	}
	
	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		parent::_buildQueryColumns($query);

		$query
				->select(array(
					'tbl.ninjaboard_topic_id',
					'tbl.replies',
					'tbl.status',
					'tbl.vote',
					'tbl.topic_type_id',
					'tbl.forum_id',
					'tbl.first_post_id',
					'tbl.last_post_id',
					'tbl.hits',
					'tbl.enabled',
					'tbl.sticky',
					'tbl.locked',
					'tbl.resolved'
				))
				->select('first_post.subject')
				->select('first_post.subject AS title')
				->select('first_post.subject AS alias')
				->select('last_post.subject AS last_post_subject')
				->select('first_post.created_time AS first_post_date')
				->select('last_post.created_user_id')
				->select('last_post.created_time AS last_post_date');
		
		//Build query for the screen names
		KFactory::get('admin::com.ninjaboard.model.people')
				->buildScreenNameQuery($query, 'first_post_person', 'first_post_user', 'first_post_username')
				->buildScreenNameQuery($query, 'last_post_person', 'last_post_user', 'last_post_username');
		
		if($this->_state->forum)
		{
			$query->select('IF((symlink.ninjaboard_forum_id = '.$this->_state->forum.'), forum.title, NULL) AS moved_to_forum_title');
		}
		
		if(KFactory::get('lib.joomla.user')->guest) {
		    $query->select(array('0 AS new', '1 AS unread'));
		} else {
		    $me     = KFactory::get('admin::com.ninjaboard.model.people')->getMe();
		    $table  = KFactory::get('admin::com.ninjaboard.database.table.logtopicreads');
		    $select = KFactory::tmp('lib.koowa.database.query')
		                  ->select('UNIX_TIMESTAMP(IFNULL(MIN(created_on), NOW()))')
		                  ->where('created_by', '=', $me->id)
		                  ;
		    if($this->_state->forum) $select->where('ninjaboard_forum_id', '=', $this->_state->forum);
		    $start = $table->select($select, KDatabase::FETCH_FIELD);

		    $query->select(array(
		        //The conversion to unix timestamp and back is because koowa will quote raw datetime strings in select queries
		        'IF(UNIX_TIMESTAMP(last_post.created_time) > '.(int)$start.', 1, 0) AS new',
		        'IF(log.created_on > last_post.created_time || last_post.created_user_id = '.(int)$me->id.', 0, 1) AS unread'
		    ));
		}
	}

    /**
     * Get a topic row object
     *
     * @return KDatabaseRowset
     */
    public function getItem()
    {
    	if (!isset($this->_item))
    	{
	        parent::getItem();

	        //@TODO acl workaround
	        if(!$this->_item->forum_id && $this->_state->forum) $this->_item->forum_id = $this->_state->forum;
		}

        return $this->_item;
    }
}