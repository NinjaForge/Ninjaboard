<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Topic Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerTopic extends ComNinjaboardControllerAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		//Delete related event handlers
		$this->registerCallback('after.delete', array($this, 'cleanupDelete'));
		
		$this->registerCallback('after.edit', array($this, 'updateForums'));
		
		if(!JFactory::getUser()->guest) $this->registerCallback('after.read', array($this, 'setLog'));
		
		if($this->isDispatched()) $this->registerCallback('after.read', array($this, 'setCanonicalAfterRead'));
	}

    /**
     * This tiny ugly proxy will be fixed in 1.2, it's here because the offset and limit states are set in the view, 
     * after.display so we can't simply run this on after.read
     */
    public function setCanonicalAfterRead(KCommandContext $context)
    {
        $this->registerCallback('after.display', array($this, 'setCanonical'), array('id' => $context->result->id));
    }

	/**
	 * Set the canonical meta info to eliminate duplicate content
	 */
	public function setCanonical(KCommandContext $context)
	{
	    $document  = JFactory::getDocument();
	    $root      = KRequest::url()->get(KHttpUrl::BASE ^ KHttpUrl::PATH);
	    $base      = 'index.php?option=com_ninjaboard&view=topic';
	    $append    = $this->getRequest()->layout != 'default' ? '&layout='.$this->getRequest()->layout : '';
	    $state     = $this->getModel()->getState();
	    $canonical = $root.JRoute::_($base.'&id='.$context->id.'&limit='.$state->limit.'&offset='.$state->offset.$append);
	    if(method_exists($document, 'addCustomTag')) {
	        $document->addCustomTag('<link rel="canonical" href="'.$canonical.'" />');
	    }
	}

	/**
	 * Sets the layout to 'default' if view is singular
	 *
	 * @author stian didriksen <stian@ninjaforge.com>
	 * @return void
	 */
	public function _actionRead(KCommandContext $context)
	{
		/*
		if(KRequest::has('get.id') && !KRequest::has('get.layout'))
		{
			
			KRequest::set('get.layout', 'default');
		}
		//*/

		$item = $this->getModel()->getItem();

		if ($item->isHittable()) {
			$item->hit();
		}
		//Force the default layout to form for read actions
		if(!isset($this->_request->layout)) {
			$this->_request->layout = 'default';
		}
		
		return parent::_actionRead($context);
	}
	
	/**
	 * Checks wether the we can delete this post or not
	 *
	 * @TODO	currently if one post fails the permission check, 
	 *			the command chain is stopped by returning false.
	 *			Change it so posts that can be deleted, are deleted.
	 *
	 * @return boolean	returns false if permission check fails
	 */
	public function canDelete()
	{
		$user = $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$topics = $this->getModel()->getList();
		foreach($topics as $topic)
		{
			$forum = $this->getService('com://site/ninjaboard.model.forums')
																		->id($topic->forum_id)
																		->getItem();
			$post = $this->getService('com://site/ninjaboard.model.posts')
																		->id($topic->first_post_id)
																		->getItem();

			// @TODO we migth want to add an option later, wether or not to allow users to delete their own post.
			if($forum->post_permissions < 3 && $post->created_by != $user->id) {
				return false;
			}
		}
	}

	/**
	 * Updating forums, topics and users when a post successfully deleted.
	 *
	 * @param $param
	 */
	public function cleanupDelete(KCommandContext $context)
	{
		$topics		= $context->result;
		$table		= $this->getService('com://site/ninjaboard.database.table.posts');
		$symlinks	= $this->getService('com://site/ninjaboard.database.table.topic_symlinks');

		// make sure we are not a row
		if ($topics instanceof KDatabaseRowDefault) $topics = array($topics);
		
		foreach($topics as $topic)
		{
			//@TODO find a better way to set the redirect
			$this->_redirect   		 = 'index.php?option=com_ninjaboard&view=forum&id='.$topic->forum_id;
			$this->_redirect_message = sprintf(JText::_('COM_NINJABOARD_TOPIC_DELETED'), $topic->subject);
		
			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()->where('ninjaboard_topic_id', '=', $topic->id);
			$table->select($query, KDatabase::FETCH_ROWSET)->delete();

			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()->where('ninjaboard_topic_id', '=', $topic->id);
			$symlinks->select($query, KDatabase::FETCH_ROWSET)->delete();
			
			//Update the forums' topics and posts count, and correct the last_post_id column
			$forums	= $this->getService('com://site/ninjaboard.model.forums')->limit(0)->id($topic->forum_id)->getListWithParents();
			$forums->recount();
		}
	}
	
	/**
	 * Only people with level 3 permissions can edit topics
	 */
	public function canEdit()
	{
		foreach($this->getModel()->getList() as $topic)
		{
			$forum = $this->getService('com://site/ninjaboard.model.forums')
																		->id($topic->forum_id)
																		->getItem();

			// @TODO we migth want to add an option later, wether or not to allow users to delete their own post.
			if($forum->topic_permissions < 3) {
				return false;
			}
		}
	}
	
	/**
	 * Updating forums topics, posts and last post stats after edit
	 *
	 * @param KCommandContext $context
	 */
	public function updateForums(KCommandContext $context)
	{
		$topics = $context->result;

		// make sure we are not a row
		if ($topics instanceof KDatabaseRowDefault) $topics = array($topics);
		
		foreach($topics as $topic)
		{
			//Update the forums' topics and posts count, and correct the last_post_id column
			$forums	= $this->getService('com://site/ninjaboard.model.forums')->limit(0)->id($topic->forum_id)->getListWithParents();
			$forums->recount();
			
			//@TODO this needs to run on the departure forum wether a ghost is left behind or not
			if($topic->moved_from_forum_id)
			{
			    //Fix topic read log tables
			    $table = $this->getService('com://admin/ninjaboard.database.table.logtopicreads');			    
			    // Run as raw query, as some sites have huge amounts of data so we need it fast
			    $query = 'UPDATE IGNORE `#__ninjaboard_log_topic_reads` SET `ninjaboard_forum_id` = \''.(int)$topic->forum_id.'\' WHERE `ninjaboard_forum_id` = \''.(int)$topic->moved_from_forum_id.'\' AND `ninjaboard_topic_id` = \''.$topic->id.'\';';
			    $table->getDatabase()->execute($query);

			
				//Update the forums' topics and posts count, and correct the last_post_id column
				$forums	= $this->getService('com://site/ninjaboard.model.forums')->limit(0)->id($topic->moved_from_forum_id)->getListWithParents();
				$forums->recount();
				
				try {
					$table	 = $this->getService('com://site/ninjaboard.database.table.topic_symlinks');
					$symlink = $table
									->select(null, KDatabase::FETCH_ROW)
									->setData(array(
										'ninjaboard_topic_id'	=> $topic->id,
										'ninjaboard_forum_id'	=> $topic->moved_from_forum_id
									))
									->save();
				} catch(KDatabaseException $e) {
					//Do nothing yet
				}
			}
		}
	}
	
	/**
	 * Logs topics the user have read
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	public function setLog($context)
	{
        $me    = $this->getService('com://admin/ninjaboard.model.people')->getMe();
        $table = $this->getService('com://admin/ninjaboard.database.table.logtopicreads');
        $topic = $context->result;

        //Don't log if there is no topic id
        if(!$topic->id) return;

        $data = array(
            'created_by' => $me->id,
            'ninjaboard_forum_id' => $topic->forum_id,
            'ninjaboard_topic_id' => $topic->id
        );
        
        if($table->count($data))
        {
            $table->select($data, KDatabase::FETCH_ROW)->setData(array('created_on' => gmdate('Y-m-d H:i:s')))->save();
            return $context;
        }

        $table->getRow()->setData($data)->save();
	    return $context;
	}
}