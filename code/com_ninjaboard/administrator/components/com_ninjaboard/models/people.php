<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard People model
 *
 * Stores the user, with avatar, forum signature and such
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelPeople extends ComDefaultModelDefault
{
	/**
	 * The current active user
	 *
	 * @var KDatabaseRow
	 */
	protected $_me;

	/**
	 * Cache over forum ids used in permissions queries
	 *
	 * @var array
	 */
	private $_forums;
	
	

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
				
		// Set the state
		$this->_state
			->insert('user', 'boolean', true)
			->insert('alias', 'string')
			->insert('not', 'int');
	}
	
	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		parent::_buildQueryColumns($query);
		
		$query
				->select(array(
					'user.*',
					'IFNULL(tbl.posts, 0) AS posts'
				))
				//Reduce this to one query
				->select(array(
					'(SELECT rank_file FROM #__ninjaboard_ranks WHERE IFNULL(tbl.posts, 0) >= min AND enabled = 1 ORDER BY min DESC LIMIT 0, 1) AS rank_icon',
					'(SELECT title FROM #__ninjaboard_ranks WHERE IFNULL(tbl.posts, 0) >= min AND enabled = 1 ORDER BY min DESC LIMIT 0, 1) AS rank_title',
					//'rank.rank_file AS rank_icon',
					//'rank.title AS rank_title',
				));
				
		$this->buildScreenNameQuery($query);
	}
	
	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
		$query
				//->join('left', 'ninjaboard_people AS person', 'tbl.id = person.ninjaboard_person_id');
				->join('left', 'users AS user', 'user.id = tbl.ninjaboard_person_id');
				//->join('left', 'ninjaboard_ranks AS rank', 'IFNULL(person.posts, 0) >= rank.min')
				//->order('rank.min', 'DESC');
		
		if(JVersion::isCompatible('1.6.0'))
		{
			$query
					->join('left', 'user_usergroup_map AS joomla_usergroup', 'joomla_usergroup.user_id = user.id')
					->select('GROUP_CONCAT(joomla_usergroup.group_id SEPARATOR \'|\') AS ninjaboard_usergroup_id');
		}
		else
		{
			$query
					->join('left', 'ninjaboard_joomla_user_group_maps AS joomla_map', 'joomla_map.joomla_gid = user.gid')
					->select("IFNULL((SELECT GROUP_CONCAT(ninjaboard_map.ninjaboard_user_group_id SEPARATOR '|') FROM #__ninjaboard_user_group_maps AS ninjaboard_map WHERE ninjaboard_map.joomla_user_id = user.id), joomla_map.ninjaboard_gid) AS ninjaboard_usergroup_id");
		}
		
		parent::_buildQueryJoins($query);
	}

	/**
	 * Builds a WHERE clause for the query
	 * Without tbl. alias, as it caused errors frontend when posting
	 */
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		if($search = $this->_state->search)
		{
			$query
				  ->where('user.name', 'LIKE',  '%'.$search.'%', 'or')
				  ->where('user.username', 'LIKE',  '%'.$search.'%', 'or')
				  ->where('user.email', 'LIKE',  '%'.$search.'%', 'or');
		}
		
		if($this->_state->alias)
		{
			$query->where('tbl.alias', '=', $this->_state->alias);
		}
		
		if($this->_state->not)
		{
			$query->where('tbl.ninjaboard_person_id', 'not in', $this->_state->not);
		}
		
		parent::_buildQueryWhere($query);
	}

	/**
	 * Gets the current joomla user with data like permissions
	 *
	 * If the user is a guest, we still have some guest data to load
	 *
	 * @return KDatabaseRow
	 */
	public function getMe()
	{
		if (!isset($this->_me))
        {
        	$user = JFactory::getUser();
        	$id   = $user->id;

        	$table  = $this->getTable();
			$query = $table->getDatabase()->getQuery();
		
			$query
				->select(array(
					'tbl.*',
					'user.*'
				))
				->from('users AS user')
				->join('left', 'ninjaboard_people AS tbl', 'user.id = tbl.ninjaboard_person_id')
				->where('user.id', '=', $id)
				->limit(1);

			$this->buildScreenNameQuery($query);

			$this->_me = $table->select($query, KDatabase::FETCH_ROW);
		}
		
		return $this->_me;
	}
	
	/**
	 * Gets a list over forum ids of which the current user can access.
	 *
	 * The acl logic for forum permissions are very simple.
	 * There are two main scenarios that alter how the permissions query works.
	 * a) The user got No Access to forums by default.
	 * b) The user Has Access to forums by default.
	 *
	 * a) In this scenario we only need to query any forum
	 * 	  that grants the user access. If only one of the assigned usergroups
	 *    do, then that's enough to gain access.
	 * 
	 * b) In this scenario the user can access all forums
	 * 	  by default. This means we only need to find the
	 * 	  forums that deny access. Unlike a), b) only deny access
	 *	  when all of the persons assigned usergroups are denied access.
	 * 	  If even just a single usergroup grants access, then the deny will be overriden.
	 *    This is done in two queries. First we get all the No Access forums,
	 *    then we query those No Access forums to see if any of them have Has Access
	 *    in any of the other usergroups.
	 *
	 * This logic essentially allows the following things:
	 * 1. The more usergroups assigned, the more access rights a person have.
	 * 2. Makes it possible to specialize forum permissions, no repeated adjustements.
	 * 3. No limits to how far and advanced the user can setup the forum, but this
	 *    advanced layer is not exposed to or require interaction by the end-user.
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return array with integers
	 */
	public function buildForumsPermissionsWhere(KDatabaseQuery $query, $key = 'tbl.ninjaboard_forum_id')
	{
		$me			= $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$no_access	= $me->forum_permissions < 1;

		if(!isset($this->_forums))
		{
			$table		= $this->getService('com://admin/ninjaboard.database.table.assets');
			$query2		= $this->getService('koowa:database.adapter.mysqli')->getQuery();
			$gids		= explode('|', $me->ninjaboard_usergroup_id);
			$ids 		= array();

			//If super admin, then no forums are without access
			if($me->gid == 25 || (JVersion::isCompatible('1.6.0') && in_array('8', JFactory::getUser($me->id)->groups))) 
				return $this->_forums = array();
			
			$query2
					->select('tbl.name')
					->from('ninjaboard_assets AS tbl');

			if($no_access)
			{
				$forums = array();
			
				$query2->where('tbl.level', '!=', 0);
			}
			else
			{
				$query2->where('tbl.level', '=', 0);
			}

			foreach($gids as $gid)
			{
				$query3	= clone $query2;
				$query3->where('tbl.name', 'like', 'com_ninjaboard.forum.%.'.$gid.'.forum');

				$results = $table->getDatabase()->select($query3, KDatabase::FETCH_FIELD_LIST);
				foreach($results as $result)
				{
					$parts	= explode('.', $result);
					$id		= $parts[2];

					if($no_access)
					{
						$forums[] = $id;
					}
					else
					{
						$ids[$id][] = $gid;
					}
				}
			}


			if(!$no_access)
			{
				$forums = array();

				//Filter out overrides that don't override all assigned usergroups
				foreach($ids as $id => $has_access)
				{
					if(count($has_access) === count($gids)) $forums[] = $id;
				}
			}

			$this->_forums = array_unique(array_filter($forums));
		}

		if($no_access)
		{
			if($this->_forums)
			{
				$query->where($key, 'in', $this->_forums);
			}
			else
			{
				//Person doesn't have any access by default, and no forums are overriding this so we need to block all forums
				$query->where($key, '=', '');
			}
		}
		else
		{
			if($this->_forums) $query->where($key, 'not in', $this->_forums);
		}
	}

	/**
	 * Function for creating screen name query IF block to get the screen name of a person
	 *
	 * LOWER() is used in order to avoid KDatabaseQuery from quoting the string values
	 *
	 * @param  KDatabaseQuery	$query				The database query instance we're adding these statements to
	 * @param  string			$person_alias		This is for defining the key for the alias for the jos_ninjaboard_people table
	 * @param  string			$user_alias			This is for defining the key for the alias for the jos_users table
	 * @param  string			$column				Column name you want it to alias to, default is display_name
	 * @param  string			$fallback			The fallback if no name could be found in the database
	 * @return $this
	 */
	public function buildScreenNameQuery(KDatabaseQuery $query, $person_alias = 'tbl', $user_alias = 'user', $column = 'display_name', $fallback = '')
	{
		if(!$fallback) $fallback = '\''.JText::_('Anonymous').'\'';

		//Decide wether to display realname or username
		$params			= $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$display_name	= $params['view_settings']['display_name'];
		if(!in_array($display_name, array('name', 'username'))) $display_name = 'username';
		
		$permissions = $params['view_settings']['change_display_name'];

		//User is allowed to change screen name, and set an alias
		if($permissions == 'custom')
		{
			$query->select(
				"IF($person_alias.which_name = LOWER('USERNAME'), $user_alias.username, ".
					"IF($person_alias.which_name = LOWER('NAME'), $user_alias.name, ".
						"IF($person_alias.which_name = LOWER('ALIAS') AND $person_alias.alias != '', $person_alias.alias, ".
							'IFNULL('.$user_alias.'.'.$display_name.', '.$fallback.')'.
						')'.
					')'.
				') AS '.$column
			);
		}

		//User is allowed to change screen name
		elseif($permissions == 'yes')
		{
			$query->select(
				"IF($person_alias.which_name = LOWER('USERNAME'), $user_alias.username, ".
					"IF($person_alias.which_name = LOWER('NAME'), $user_alias.name, ".
						'IFNULL('.$user_alias.'.'.$display_name.', '.$fallback.')'.
					')'.
				') AS '.$column
			);
		}

		//Users can't configure the screen name
		else
		{
			$query->select('IFNULL('.$user_alias.'.'.$display_name.', '.$fallback.') AS '.$column);
		}
		
		return $this;
	}
}