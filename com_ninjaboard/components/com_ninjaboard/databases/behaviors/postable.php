<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: postable.php 2460 2011-10-11 21:21:19Z stian $
 * @package		Ninjaboard
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */ 

/**
 * A behavior that takes care of the topics, updating the user post stats and more
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @package     Ninjaboard
 * @subpackage 	Behaviors
 */
class ComNinjaboardDatabaseBehaviorPostable extends KDatabaseBehaviorAbstract
{
	protected function _beforeTableInsert(KCommandContext $context)
	{
		$row	= $context['data']; //get the row data being inserted
		$user	= JFactory::getUser();
		$topic	= $this->getService('com://site/ninjaboard.model.topics')->id($row->ninjaboard_topic_id)->getItem();
		$forum	= $this->getService('com://admin/ninjaboard.model.forums')->id($row->forum_id)->getItem();
		if(!$forum->isNestable()) return;
		$forums	= $forum->getParents();
		if(!$forum->id) {
			JError::raiseWarning(404, JText::_('No forum id!'));
			return false;
		}

		if(!$topic->id || !$topic->forum_id)
		{
			if(!$topic->forum_id) $topic->forum_id = $forum->id;

			if($forum->topic_permissions < 2 && $user->gid < 25) 
			{
				JError::raiseWarning(404, JText::_('You\'re not allowed to create topics.'));
				return false;
			}

			//@Todo add acl functionality for these
			$topic->resolved = 0;
			$topic->locked   = 0;
			$topic->sticky   = 0;
			
			$topic->save();
			
			$forum->topics++;
			$forum->save();
			foreach($forums as $parent)
			{
				$parent->topics++;
				$parent->save();
			}
			
			$row->ninjaboard_topic_id = $topic->id;
		}
		else
		{
			if($forum->post_permissions < 2 && $user->gid < 25)
			{
				JError::raiseWarning(404, JText::_('You\'re not allowed to reply topics.'));
				return false;
			}
			$topic->replies++;
			$topic->save();			
		}
		$row->user_ip = KRequest::get('server.REMOTE_ADDR', 'raw');
		
		//@Todo add acl functionality for these
		$row->locked  = 0;
	}
	
	protected function _afterTableInsert(KCommandContext $context)
	{
		$row 	= $context['data']; //get the row that was inserted
		$userid = $row->created_by ? $row->created_by : '00';

		$topic	= $this->getService('com://site/ninjaboard.model.topics')->id($row->ninjaboard_topic_id)->getItem();
		$forum	= $this->getService('com://site/ninjaboard.model.forums')->id($topic->forum_id)->getItem();
		if(!$forum->isNestable()) return;
		$forums	= $forum->getParents();
		$user	= $this->getService('com://admin/ninjaboard.model.people')->id($userid)->getItem();

		if(!$topic->id)
		{
			JError::raiseWarning(404, JText::_('Topic id is missing.' . $row->ninjaboard_topic_id));
			return false;
		}

		if(!$topic->first_post_id) $topic->first_post_id = $row->id;
		$topic->last_post_id = $row->id;
		$topic->last_post_on = $row->created_on;
		$topic->last_post_by = $row->created_by;
		if($topic->first_post_id == $row->id) $topic->params = is_string($row->params) ? new KConfig(json_decode($row->params, true)) : $row->params;
		$topic->save();
		
		$forum->last_post_id = $row->id;
		$forum->posts++;
		$forum->save();
		foreach($forums as $forum)
		{
			$forum->last_post_id = $row->id;
			$forum->posts++;
		
			$forum->save();	
		}
		
		if(!JFactory::getUser()->guest)
		{
			if($user->id != $userid) $user->id = $userid;
			$user->posts++;
			$user->save();
		}
	}
	
	protected function _beforeTableUpdate(KCommandContext $context)
	{
		$row	= $context['data']; //get the row data being inserted
		$topic	= $this->getService('com://admin/ninjaboard.model.topics')->id($row->ninjaboard_topic_id)->getItem();
		$forum	= $this->getService('com://admin/ninjaboard.model.forums')->id($topic->forum_id)->getItem();
		$user	= $this->getService('com://admin/ninjaboard.model.people')->getMe();

		if($forum->post_permissions < 2) return false;
		if($forum->post_permissions < 3 && $row->created_by != $user->id) return false;
	}
	
	protected function _afterTableUpdate(KCommandContext $context)
	{
		$row 	= $context['data']; //get the row that was inserted

		$topic	= $this->getService('com://site/ninjaboard.model.topics')->id($row->ninjaboard_topic_id)->getItem();
		if($topic->first_post_id === $row->id) {
			//To avoid getting inherited params saved to the table, we call getData(), which wont attach such data.
			$data			= $row->getData();
			$topic->params	= json_decode($data['params'], true);
		}
		
		$topic->save();		
	}
}