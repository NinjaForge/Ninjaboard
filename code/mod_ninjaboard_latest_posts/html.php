<?php
/**
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_latest_news
 * @copyright	Copyright (C) 2010 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 defined( '_JEXEC' ) or die( 'Restricted access' );

 /**
 * Jump Module Class
 *   
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_latest_news
 */
class ModNinjaboard_latest_postsHtml extends ModDefaultHtml
{		
	/**
	 * Cache over forum ids used in permissions queries
	 *
	 * @var array
	 */
	private $_forums;

	/**
 	 * Render the latest post module
 	 */
	public function display()
	{
		$this->assign('items', $this->getList());
		$this->assign('links', $this->getLinks());
		$this->assign('itemID', $this->getItemId());
		$this->assign('params', $this->module->params);
		
		return parent::display();
	}

	/**
	 * Method for getting a list of forum posts based on set parameters
	 */
	public function getList()
	{
		$params = $this->module->params;
		$db 	= $this->getService('koowa:database.adapter.mysqli');
		$query 	= $db->getQuery();
		$me 	= $this->getService('com://admin/ninjaboard.model.people')->getMe();

		// build our query
		$query->select(array(
						'tbl.*', 
						'f.title AS forum',
						't.ninjaboard_topic_id',
						'f.ninjaboard_forum_id',
						'a.avatar',
						"IF(t.first_post_id = tbl.ninjaboard_post_id, tbl.subject, CONCAT('RE: ', first_post.subject)) AS subject"
						));

		$query->from('ninjaboard_posts AS tbl');

		// Determine what posts we are display 0 = all posts, 1 = all topics by first post, 2 = all topics by latest post
		$which = $params->get('which_posts', 0);
		if ($which == 0) {
			$query->join('left', 'ninjaboard_topics AS t', 'tbl.ninjaboard_topic_id = t.ninjaboard_topic_id');
		} elseif ($which == 1) {
			$query->join('left', 'ninjaboard_topics AS t', 'tbl.ninjaboard_post_id = t.first_post_id');
		} else {
			$query->join('left', 'ninjaboard_topics AS t', 'tbl.ninjaboard_post_id = t.last_post_id');
		}

		$query->join('left', 'users AS user', 'user.id = tbl.created_user_id')
			->join('left', 'ninjaboard_forums AS f', 't.forum_id = f.ninjaboard_forum_id')
			->join('left', 'ninjaboard_posts AS first_post', 'first_post.ninjaboard_post_id = t.first_post_id')
			->join('left', 'ninjaboard_people AS person', 'person.ninjaboard_person_id = tbl.created_user_id')
			->where('tbl.enabled', '=', 1)
			->where('t.enabled', '=', 1)
			->where('f.enabled', '=', 1);

		// If we are not a guest then we are joining the log table
		if(!JFactory::getUser()->guest)
        {
    		$query->join('left', 'ninjaboard_log_topic_reads AS log', 
    		    'log.created_by = '.$me->id.' AND '.
    		    'log.ninjaboard_topic_id = tbl.ninjaboard_topic_id'
    		);
    	}

		// only show posts within our time frame
		if ($timeframe = $params->get('show_recent', 0)) {
			$timeframe = (time() - ($timeframe * 3600));
			$query->where('unix_timestamp(tbl.created_time)', '>=', $timeframe);
		}

		//determine where we are linking to and where we are getting the avatar from
		$extension = $params->get('name_link');
		if ($extension == 'cbe') {
			$query->join('left', 'cbe AS a', 'tbl.created_user_id = a.user_id');
		} elseif ($extension == 'cb') {
			$query->join('left', 'comprofiler AS a', 'tbl.created_user_id = a.user_id');
		} elseif ($extension == 'js') {
			$query->join('left', 'community_users AS a', 'tbl.created_user_id = a.userid');
		} else {
			$query->join('left', 'ninjaboard_people AS a', 'tbl.created_user_id = a.ninjaboard_person_id');
		}

		// only get the posts/topics from the specified forums
		if ($only = $params->get('only_forums', 0)) {
			$query->where('f.ninjaboard_forum_id', 'IN', $only);
		}

		// determine wether we are unread
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

		    $query->select(array(
		        //The conversion to unix timestamp and back is because koowa will quote raw datetime strings in select queries
		        'IF(UNIX_TIMESTAMP(t.last_post_on) > '.(int)$start.', 1, 0) AS new',
		        'IF(log.created_on > t.last_post_on || t.last_post_by = '.(int)$me->id.', 0, 1) AS unread'
		    ));
		}

		// Build our permission query
		$this->buildForumsPermissionsWhere($query, 'f.ninjaboard_forum_id');

		// build our screenname query
		$this->getService('com://admin/ninjaboard.model.people')->buildScreenNameQuery($query, 'person', 'user', 'display_name', 'IFNULL(tbl.guest_name, \''.JText::_('COM_NINJABOARD_ANONYMOUS').'\')');

		// order by
		$query->order('tbl.created_time', $params->get('order_by') ? 'ASC' : 'DESC');

		$query->limit($params->get('num_posts', 5));

		return $db->select($query, KDatabase::FETCH_ROWSET);
	}

	/**
	 * Method for determining the links to things
	 */
	public function getLinks()
	{
		$extension  = $this->module->params->get('name_link');
		$links		= array();

		$links['avatar'] = JURI::base();

		if ($extension == 'cbe') {
			$links['profile'] 	= 'index.php?option=com_cbe&task=userProfile&user=';
			$links['davatar']	= JURI::base() . 'components/com_cbe/images/english/nophoto.png';
			$links['avatar']	= 'images/cbe/';
		} elseif ($extension == 'cb') {
			$links['profile'] 	= 'index.php?option=com_comprofiler&task=userProfile&user=';
			$links['avatar']	= 'images/comprofiler/';
			$links['davatar']	= JURI::base() . 'components/com_comprofiler/plugin/templates/luna/images/avatar/tnnophoto_n.png';
		} elseif ($extension == 'js') {
			$links['profile'] 	= 'index.php?option=com_community&view=profile&userid=';
			$links['davatar']	= JURI::base() . 'components/com_community/assets/default_thumb.jpg';
		} else {
			$links['profile']  	= 'index.php?option=com_ninjaboard&view=person&id=';
		}

		return $links;
	}

	/**
	 * Method for getting the component itemid
	 */
	public function getItemId()
	{
		$extension  = $this->module->params->get('name_link');

		if ($extension == 'cbe') {
			$component = JComponentHelper::getComponent('com_cbe');
		} elseif ($extension == 'cb') {
			$component = JComponentHelper::getComponent('com_comprofiler');
		} elseif ($extension == 'js') {
			$component = JComponentHelper::getComponent('com_community');
		} else {
			$component = JComponentHelper::getComponent('com_ninjaboard');
			$view	   = 'forums';
		}

		$menu	= JFactory::getApplication()->getMenu();
        $items	= $menu->getItems(version_compare(JVERSION,'1.6.0','ge') ? 'component_id' : 'componentid', $component->id);
        
        if (is_array($items))
    	{
    		foreach ($items as $item)
    		{
    		    if((isset($item->query['view']) && $item->query['view'] == $view) || ($item->query['option'] == 'com_comprofiler'))
    		    {
    		        return $item->id;
    		    }
    		}
    	}
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
			if($me->gid == 25 || (version_compare(JVERSION,'1.6.0','ge') && $me->gid == 8)) 
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

			// remove the forums we specified in the parameters
			if (is_array($this->module->params->get('not_forums', 0))) {
				$forums = array_merge($this->module->params->get('not_forums'), $forums);
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
			if($this->_forums) {
				$query->where($key, 'NOT IN', $this->_forums);
				foreach ($this->_forums as $forum)
					$query->where('path', 'NOT LIKE', '%/'.$forum.'/%');
			}
		}
	}
}