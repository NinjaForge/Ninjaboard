<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: users.php 2460 2011-10-11 21:21:19Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Users model
 *
 * Gets the user, with avatar, forum signature and such
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelUsers extends ComDefaultModelDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
				
		// Set the state
		$this->_state
			->insert('usergroup', 'int')
			->insert('gid'		, 'boolean', false)
			->insert('new'		, 'boolean', false)
			->insert('me'       , 'boolean', true);
	}

	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
		$query->join('left', 'ninjaboard_people AS person', 'person.ninjaboard_person_id = tbl.id');

/*
		$query
			->join('left', 'ninjaboard_joomla_user_group_maps AS joomla_map', 'joomla_map.joomla_gid = tbl.gid')
			->select("IFNULL((SELECT GROUP_CONCAT(ninjaboard_map.ninjaboard_user_group_id SEPARATOR '|') FROM #__ninjaboard_user_group_maps AS ninjaboard_map WHERE ninjaboard_map.joomla_user_id = tbl.id), joomla_map.ninjaboard_gid) AS ninjaboard_usergroup_id");
//*/	
		//Get the default gid for users that's not mapped
		$gid	= (int)$this->getService('com://admin/ninjaboard.model.joomlausergroupmaps')->getGuest()->gid;

		if(JVersion::isCompatible('1.6.0'))
		{
			//$query
					//->join('left', 'user_usergroup_map AS joomla_usergroup', 'joomla_usergroup.user_id = tbl.id')
					//->select('GROUP_CONCAT(joomla_usergroup.group_id SEPARATOR \'|\') AS ninjaboard_usergroup_id');
		}
		else
		{
			$query
					->join('left', 'ninjaboard_joomla_user_group_maps AS joomla_map', 'joomla_map.joomla_gid = tbl.gid')
					->select("IFNULL(IFNULL((SELECT GROUP_CONCAT(ninjaboard_map.ninjaboard_user_group_id SEPARATOR '|') FROM #__ninjaboard_user_group_maps AS ninjaboard_map WHERE ninjaboard_map.joomla_user_id = tbl.id), joomla_map.ninjaboard_gid), $gid) AS ninjaboard_usergroup_id");
		}

		parent::_buildQueryJoins($query);
	}
	
	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		$query
			  ->select(array('person.*', 'tbl.*'))
			  ->select(array(
			  	'IFNULL(person.posts, 0) AS posts',
			  	//@TODO All this just to avoid quoting
			  	'IFNULL(person.avatar, CONCAT(LOWER(\'/MEDIA/COM_NINJABOARD/IMAGES/AVATAR\'), LOWER(\'.PNG\'))) AS avatar',
			  	'person.signature AS signature'
			  ))
			  //Reduce this to one query
			  ->select(array(
			  	'(SELECT rank_file FROM #__ninjaboard_ranks WHERE IFNULL(person.posts, 0) >= min AND enabled = 1 ORDER BY min DESC LIMIT 0, 1) AS rank_icon',
			  	'(SELECT title FROM #__ninjaboard_ranks WHERE IFNULL(person.posts, 0) >= min AND enabled = 1 ORDER BY min DESC LIMIT 0, 1) AS rank_title',
			  	//'rank.rank_file AS rank_icon',
			  	//'rank.title AS rank_title',
			  ));

		parent::_buildQueryColumns($query);
		
		//Build query for the screen names
		$this->getService('com://admin/ninjaboard.model.people')
			->buildScreenNameQuery($query, 'person', 'tbl', 'display_name');
	}

	/**
	 * Filter list based on search keyword
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param KDatabaseQuery $query
	 * @return $this
	 */
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
       	if($search = trim($this->_state->search))
		{
			$parts	= explode(' ', $search);
			$states	= array();
			foreach($parts as $i => $part)
			{
				if(strpos($part, ':') === false) continue;
				$keys = explode(':', $part);
				$states[$keys[0]] = $keys[1];
				unset($parts[$i]);
			}
			
			if($states)
			{
				$conditions = array();
				foreach(array('username', 'email') as $key)
				{
					if(isset($states[$key]))
					{
						$query->where('tbl.'.$key, 'LIKE', '%'.$states[$key].'%');
						
					}
				}
				if($parts) $query->where('tbl.name', 'LIKE',  '%'.implode($parts).'%');
			}
			else
			{
				//To avoid auto quoting, and also trim trailing whitespace
				$search = $this->getTable()->getDatabase()->quoteValue('%'.strtoupper($search).'%');
				
				$query
					  ->where("(tbl.name LIKE $search OR tbl.username LIKE $search OR tbl.email LIKE $search OR person.alias LIKE $search)")
					  /*->where('tbl.name', 'LIKE',  '%'.$search.'%', 'or')
					  ->where('tbl.username', 'LIKE',  '%'.$search.'%', 'or')
					  ->where('tbl.email', 'LIKE',  '%'.$search.'%', 'or')*/;
			}
		}
		
		$group = $this->_state->usergroup;
		if($group)
		{
			$query->where('tbl.gid', '=',  (int)$group);
		}
		
		if(!$this->_state->me)
		{
		    $me = $this->getService('com://admin/ninjaboard.model.people')->getMe();
			$query->where('tbl.id', '!=', $me->id);
		}
		
		//Don't show blocked users
		$query->where('tbl.block', '=', 0);

		parent::_buildQueryWhere($query);
	}
}