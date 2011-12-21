<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: watches.php 2470 2011-11-01 14:22:28Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Watches model
 *
 * Email Updates
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelWatches extends ComDefaultModelDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$me			= $this->getService('com://admin/ninjaboard.model.people')->getMe();
		
		// Set the state
		$this->_state
			->insert('by', 'int', $me->id)
			->insert('type', 'int')
			//Convenience state if you don't want to call the table and convert the name to an int
			->insert('type_name', 'cmd')
			->insert('type_id', 'int')
			->insert('topic', 'int');
	}
	
	/**
	 * Builds a WHERE clause for the query
	 */
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);

		if($this->_state->type) {
			$query->where('tbl.subscription_type', '=', $this->_state->type);
		} elseif($this->_state->type_name) {
			$table = $this->getService('com://admin/ninjaboard.database.table.watches');
			$query->where('tbl.subscription_type', '=', $table->getTypeIdFromName($this->_state->type_name));
		}
		
		if($this->_state->type_id) {
			$query->where('tbl.subscription_type_id', '=', $this->_state->type_id);
		}
		
		if($this->_state->by) {
			$query->where('tbl.created_by', '=', $this->_state->by);
		}
	}
	
	/**
	 * Get a list over people to be notified about a topic with a new post
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return array
	 */
	public function getRecipients()
	{
		$me			= $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$table		= $this->getTable();
		$params		= $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$topic		= $this->getService('com://site/ninjaboard.model.topics')->id($this->_state->topic)->getItem();
		$forum		= $this->getService('com://site/ninjaboard.model.forums')->id($topic->forum_id)->getItem();
		$ids		= array();
		
		if($params->email_notification_settings->auto_notify_admins == 'yes') {
			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
															->select('id')
															->from('users')
															->where('sendEmail', '=', 1)
															->where('id', '!=', $me->id);
			foreach($table->getDatabase()->select($query, KDatabase::FETCH_FIELD_LIST) as $id)
			{
				$ids[$id] = $id;
			}
		}
		
		
		//Recipients subscribed to this topic
		if($this->_state->topic)
		{
			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
															->select('created_by')
															->where('subscription_type', '=', $table->getTypeIdFromName('topic'))
															->where('subscription_type_id', '=', $this->_state->topic)
															->where('created_by', '!=', $me->id)
															->from('ninjaboard_subscriptions');
			foreach($table->getDatabase()->select($query, KDatabase::FETCH_FIELD_LIST) as $id)
			{
				$ids[$id] = $id;
			}
		}
		
		
		//Recipients subscribed to this forum and/or parent forum(s)
		if($forum->id)
		{
			$forums = array_filter(explode('/', $forum->path));
			$forums[] = $forum->id;
			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
															->select('created_by')
															->where('subscription_type', '=', $table->getTypeIdFromName('forum'))
															->where('subscription_type_id', 'in', $forums)
															->where('created_by', '!=', $me->id)
															->from('ninjaboard_subscriptions');
			foreach($table->getDatabase()->select($query, KDatabase::FETCH_FIELD_LIST) as $id)
			{
				$ids[$id] = $id;
			}
		}
		
		//Recipients subscribed to me
		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
														->select('created_by')
														->where('subscription_type', '=', $table->getTypeIdFromName('person'))
														->where('subscription_type_id', 'in', $me->id)
														->where('created_by', '!=', $me->id)
														->from('ninjaboard_subscriptions');
		foreach($table->getDatabase()->select($query, KDatabase::FETCH_FIELD_LIST) as $id)
		{
			$ids[$id] = $id;
		}
		
		if(!$ids) return array();
		
		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
														->select(array('name', 'email'))
														->from('users')
														->where('id', 'in', $ids);
		return $table->getDatabase()->select($query, KDatabase::FETCH_OBJECT_LIST);
	}
	
	/**
	 * Get a list of items which represnts a  table rowset
	 *
	 * @return KDatabaseRowset
	 */
	public function getList()
	{
		if(!isset($this->_list))
		{
			parent::getList();
			
			$table = $this->getTable();
			$types = array(
				'forum' => $table->getTypeIdFromName('forum'),
				'topic' => $table->getTypeIdFromName('topic'),
				'person' => $table->getTypeIdFromName('person')
			);

			//@TODO optimize this to 3 queries in total, instead of multiple
			foreach($this->_list as $item)
			{
				//@TODO speed this up by adding it as a db column
				$item->modified_by = false;
				$item->modified_name = JText::_('n/a');
				
				if($item->subscription_type == $types['forum'])
				{
					$item->type  = 'forum';
					$item->title = $this->getService('com://admin/ninjaboard.model.forums')
																					->id($item->subscription_type_id)
																					->getItem()
																					->title;
					
					$icon = '/forums/default.png';
					$link = '&view=forum&id='.$item->subscription_type_id;
				}
				
				if($item->subscription_type == $types['topic'])
				{
					$item->type  = 'topic';
					$topic = $this->getService('com://admin/ninjaboard.model.topics')
																					->id($item->subscription_type_id)
																					->getItem();
					$item->title = $topic->subject;
					
					$icon = '/topic/32__default.png';
					$link = '&view=topic&id='.$item->subscription_type_id;
				}
				
				if($item->subscription_type == $types['person'])
				{
					$item->type  = 'person';
					$item->title = $this->getService('com://admin/ninjaboard.model.people')
																					->id($item->subscription_type_id)
																					->getItem()
																					->display_name;
					$icon = '/16/users.png';
					$link = '&view=person&id='.$item->subscription_type_id;
				}

				$item->link = JRoute::_('index.php?option=com_ninjaboard'.$link);
				$item->icon = $this->getService('ninja:template.helper.document')->img($icon);
			}			
		}
		
		return $this->_list;
	}
}