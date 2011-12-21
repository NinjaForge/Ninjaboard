<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: posts.php 1996 2011-06-29 15:44:02Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Posts model
 *
 * Fetches posts
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelPosts extends ComDefaultModelDefault
{
    /**
     * Flag for toggling on/off acl queries
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
		parent::__construct($config);
		
		$this->_state
						->insert('topic', 'int')
						->insert('post' , 'int');
	}

    /**
     * Sets the acl flag, for performance reasons
     *
     * @param  $acl boolean
     * @return $this
     */
    public function setAcl($acl)
    {
        $this->_acl = $acl;
        
        return $this;
    }

	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
		parent::_buildQueryJoins($query);

		$query
			->join('left', 'users AS user', 'user.id = tbl.created_user_id')
			->join('left', 'ninjaboard_people AS person', 'person.ninjaboard_person_id = tbl.created_user_id')
			->join('left', 'ninjaboard_topics AS topic', 'topic.ninjaboard_topic_id = tbl.ninjaboard_topic_id')
			->join('left', 'ninjaboard_posts AS first_post', 'first_post.ninjaboard_post_id = topic.first_post_id')
			->join('left', 'ninjaboard_forums AS forum', 'forum.ninjaboard_forum_id = topic.forum_id');
	}
	
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);
		
		if($post = $this->_state->post) $query->where('tbl.ninjaboard_post_id', '=', $post, 'and');
		if($topic = $this->_state->topic) $query->where('tbl.ninjaboard_topic_id', '=', $topic, 'and');
		
		if($search = $this->_state->search)
		{
		    $search = '\'%'.strtoupper($search).'%\'';
			$query->where("(tbl.subject LIKE $search OR tbl.text LIKE $search OR first_post.subject LIKE $search)");			
		}
		
		$query
				->where('forum.ninjaboard_forum_id', '!=', 'NULL')
				->where('topic.enabled', '=', 1)
				->where('forum.enabled', '=', 1)
				->where('tbl.enabled', '=', 1);

		//Building the permissions query WHERE clause
		if($this->_acl) KFactory::get('admin::com.ninjaboard.model.people')->buildForumsPermissionsWhere($query, 'forum.ninjaboard_forum_id');
	}
	
	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		parent::_buildQueryColumns($query);

		$query
			  ->select('user.usertype')
			  ->select('person.posts AS person_posts')
			  ->select('tbl.ninjaboard_post_id AS id')
			  ->select("IFNULL(person.avatar, '/media/com_ninjaboard/images/avatar.png') AS avatar")
			  ->select("IF(topic.first_post_id = tbl.ninjaboard_post_id, tbl.subject, CONCAT('RE: ', first_post.subject)) AS subject")
			  ->select('person.signature')
			  //@TODO figure out how we can combine the ranks query into a single query
			  ->select('(SELECT rank_file FROM #__ninjaboard_ranks WHERE person.posts >= min AND enabled = 1 ORDER BY min DESC LIMIT 1) AS rank_icon')
			  ->select('(SELECT title FROM #__ninjaboard_ranks WHERE person.posts >= min AND enabled = 1 ORDER BY min DESC LIMIT 1) AS rank_title');
		
		//Build query for the screen names
		KFactory::get('admin::com.ninjaboard.model.people')->buildScreenNameQuery($query, 'person', 'user', 'display_name', 'IFNULL(tbl.guest_name, \''.JText::_('Anonymous').'\')');
		
		if($search = $this->_state->search)
		{
			$query->select(array('forum.title AS forum', 'topic.ninjaboard_topic_id AS topic', 'topic.hits'));
		}
	}
	
	/**
	 * Gets the offset for a specific post, for usage in topic lists and like
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return int
	 */
	public function getOffset()
	{
		if(!isset($this->_offset))
		{
			if(!$this->_state->post || !$this->_state->topic || !$this->_state->limit) return $this->_offset = 0;
			
			$query		= KFactory::tmp('lib.koowa.database.query')
							->select('COUNT(*)')
							->where('ninjaboard_topic_id', 'in', $this->_state->topic)
							->where('ninjaboard_post_id', '<', $this->_state->post);
			$position	= $this->getTable()->select($query, KDatabase::FETCH_FIELD);
			$page		= floor($position / $this->_state->limit);

			$this->_offset = $this->_state->limit * $page;
		}
		
		return $this->_offset;
	}
}