<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Forums model
 *
 * Fetches forums, and all that comes with the forums
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelForums extends NinjaModelTable
{
	/**
	 * This is for the frontend router
	 *
	 * @var boolean
	 */
	public $sef = true;
	
	/**
	 * Parents count cache
	 *
	 * @var array
	 */
	private $_parent_count = array();
	
	/**
	 * Toggle wether to bypass acl or not, SEF needs to bypass acl for building urls for instance
	 *
	 * @var boolean
	 */
	protected $_acl = true;

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$config->append(array(
			'acl' => true
		));

		parent::__construct($config);

		$this->_acl = $config->acl;

		// Set the state
		// @TODO we need to look at what states we can purge, is getting messy here
		$this->_state
			->insert('acl'       , 'cmd', 'auth_view')
			->insert('exclude', 'int', 0)
			->insert('hierarchy', 'boolean', false)
			->insert('enabled', 'int', JFactory::getApplication()->isSite() ? 1 : null)
			->insert('recurse', 'boolean', false)
			->insert('flat', 'boolean', false)
			->insert('path', 'int')
			->insert('parent'   , 'int')
			->insert('level'   , 'int', 1)
			->insert('levels', 'int')
			->insert('sort', 'cmd', 'path_sort')
			->insert('indent', 'boolean', false)
			->insert('sort', 'cmd', 'path_sort_ordering');
	}
	
	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
		$query
				->join('left', 'ninjaboard_posts AS last_post', 'last_post.ninjaboard_post_id = tbl.last_post_id')
				->join('left', 'ninjaboard_topics AS topic', 'last_post.ninjaboard_topic_id = topic.ninjaboard_topic_id')
				->join('left', 'ninjaboard_posts AS first_post', 'first_post.ninjaboard_post_id = topic.first_post_id')
				->join('left', 'users AS usr', 'usr.id = last_post.created_user_id')
				->join('left', 'ninjaboard_people AS person', 'person.ninjaboard_person_id = last_post.created_user_id');
	}
	
	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		parent::_buildQueryColumns($query);

		/* TODO finish this function and remove the placeholder */
		$query->select('first_post.subject')
			  ->select('last_post.created_user_id');
		
		$query->select('tbl.last_post_id')
			  ->select('last_post.created_time AS last_post_date')
			  ->select('last_post.ninjaboard_topic_id AS last_topic_id');
		
		//Build query for the screen names
		$this->getService('com://admin/ninjaboard.model.people')
			->buildScreenNameQuery($query, 'person', 'usr', 'last_post_username', 'IFNULL(last_post.guest_name, \''.JText::_('COM_NINJABOARD_ANONYMOUS').'\')');
		
		if(JFactory::getUser()->guest) {
		    $query->select(array('0 AS new', '1 AS unread'));
		} else {
		    $me     = $this->getService('com://admin/ninjaboard.model.people')->getMe();
		    $table  = $this->getService('com://admin/ninjaboard.database.table.logtopicreads');
		    $select = $this->getService('koowa:database.adapter.mysqli')->getQuery()
		                  ->select('UNIX_TIMESTAMP(IFNULL(MIN(created_on), NOW()))')
		                  ->where('created_by', '=', $me->id)
		                  ;
		    $start = $table->select($select, KDatabase::FETCH_FIELD);
		    //The conversion to unix timestamp and back is because koowa will quote raw datetime strings in select queries
		    $query->select('IF(UNIX_TIMESTAMP(topic.last_post_on) > '.(int)$start.', 1, 0) AS new');
		    

            //Count all the read topics by this user
            $reads = $this->getService('koowa:database.adapter.mysqli')->getQuery()
                            ->select('COUNT(*)')
                            ->where('reads.ninjaboard_forum_id = tbl.ninjaboard_forum_id')
                            ->where('reads.created_by = '.$me->id)
                            ->where('reads.created_on >= tmp.last_post_on')
                            ->join('left', 'ninjaboard_topics AS tmp', 'tmp.ninjaboard_topic_id = reads.ninjaboard_topic_id')
                            ->from('ninjaboard_log_topic_reads AS reads');

            //Count all the topics directly nesting in this forum, not counting down the tree
            $topics = $this->getService('koowa:database.adapter.mysqli')->getQuery()
                            ->select('COUNT(*)')
                            ->where('topics.forum_id = tbl.ninjaboard_forum_id')
                            ->from('ninjaboard_topics AS topics');

            $query->select('IF(tbl.topics = 0, 0, ('.$topics.') - ('.$reads.')) AS unread');
            //$query->select('('.$topics.') AS total_topics');
            //$query->select('('.$reads.') AS total_reads');
		}
	}
	
	protected function _buildQueryOrder(KDatabaseQuery $query)
    {
    	$sort      = $this->_state->sort;
       	$direction  = strtoupper($this->_state->direction);

		if(!$sort) {
			//@TODO we no longer need this CONCAT part
			//$query->select("CONCAT(tbl.path, tbl.ninjaboard_forum_id, '/') AS path_ordering");
			$query->order('path_sort', 'asc');
		} elseif($sort) {
			//Our model don't support DESC yet
			//$query->order($sort, $direction);
			$query->order($sort, 'asc');
    	}
    }
	
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);

		if(is_numeric($this->_state->enabled))
		{
			$query->where('tbl.enabled', '=', $this->_state->enabled);
		}
		
		//Build the query for fetching the permissions
		if($this->_acl) $this->getService('com://admin/ninjaboard.model.people')->buildForumsPermissionsWhere($query);

		// If we have an id, we shouldn't add other where statements
		// @TODO check if it's safe to do this
		if($this->_state->id) return;

		
		if($this->_state->recurse && $this->_state->path && !$this->_state->flat)
		{
			//$query->where('tbl.path', 'like', '%'.$this->_state->path);
			//$query->where('tbl.path', 'like', '%/'.$this->_state->path.'/%');
			$query->where('tbl.path', 'like', '%/'.$this->_state->path.'/');
		}
		else if($this->_state->recurse && !$this->_state->flat)
		{
			$query->where('tbl.path', '=', '/');
		}
		
		if($this->_state->levels !== null)
		{
			$level  = $this->_state->level;
			$levels = $this->_state->levels;
			$query->where('tbl.level', '>=', $level)->where('tbl.level', '<', $levels + $level);
		}
		
		if($search = $this->_state->search)
		{
			$query->where("CONCAT(tbl.title, ' ', tbl.description)", 'LIKE', '%'.$search.'%');			
		}
	}
	
	public function getList()
    {
	    if(!isset($this->_list))
	    {
	        parent::getList();

	        if($this->_state->levels)
	        {
	        	$copied = array();

	        	$list = $this->_list->getIterator()->getArrayCopy();
	        	$table = $this->getTable();
	        	//usort($list, array($this, '_sort'));
	        	
	        	foreach($list as $row)
	        	{
	        		if($row->level == 1) continue;

	        		$path = array_filter(explode('/', $row->path));
	        		$parent = end($path);
	        		
	        		//If this forum havent been queried yet, then do a select count
	        		if(!isset($this->_parent_count[$parent]))
	        		{
	        			$this->_parent_count[$parent] = $table->count(array('id' => $parent, 'enabled' => 1));
	        		}
	        		
	        		//If parent isn't enabled, don't try to find it
	        		if(!$this->_parent_count[$parent]) continue;
	        		
	        		$parent = $this->_list->find($parent);

	        		//@TODO perhaps move this into the table object instead, as it's a virtual property
	        		if(!isset($parent->subforums)) $parent->subforums = array();

	        		$subforums = $parent->subforums;
	        		$subforums[] = $row;
	        		$parent->subforums = $subforums;

	        		$copied[] = $row;
	        	}

	        	// Remove copied forums from main list
	        	foreach($copied as $row)
	        	{
	        		$this->_list->extract($row);
	        	}
	        }
	        
	        if($this->_state->indent)
	        {
	        	foreach($this->_list as $item)
	        	{
	        		$item->title = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $item->level-1) . $item->title;
	        	}
	        }
	        
	        if($this->_state->recurse) $this->_total = count($this->_list);
		}

		return $this->_list;
    }
    
    public function _sort($a, $b)
    {
    	return $b->level - $a->level;
    }

	/**
	 * Get a list over parent forums, including current forum
	 *
	 * @author	Stian Didriksen <stian@ninjaforge.com>
	 * @return	KDatabaseRowsetInterface
	 */
	public function getListWithParents()
	{
		$database = $this->getService('koowa:database.adapter.mysqli');
		$table = $this->getTable();
		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
					->select('path')
					->where('ninjaboard_forum_id', '=', $this->_state->id);
		
		$path = $table->select($query, KDatabase::FETCH_FIELD);
		
		$ids = array_merge(array($this->_state->id), array_filter(explode('/', $path)));
		
		return $table->select($ids, KDatabase::FETCH_ROWSET);
	}
}