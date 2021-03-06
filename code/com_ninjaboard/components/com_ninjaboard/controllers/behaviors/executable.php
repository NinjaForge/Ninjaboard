<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category    Ninjaboard
 * @copyright   Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://ninjaforge.com
 */

/**
 * Ninjaboard Exectuable Controller Behavior
 *
 * @todo   refactor the attachment creation so that we can run checks here
 * @package Ninjaboard
 */
class ComNinjaboardControllerBehaviorExecutable extends ComDefaultControllerBehaviorExecutable
{
	/**
	 * Decide if a user has the ability to add things
	 *
	 * @return boolean	true or false depending on permissions
	 */
	public function canAdd()
    {
    	$caller 	= $this->getMixer()->getIdentifier()->name;
    	$request 	= $this->getRequest();
    	$forum 		= $this->getService('com://site/ninjaboard.model.forums')->id($request->forum)->getItem();
        $params     = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
        $guest      = JFactory::getUser()->guest;

    	// no one is allowed to add forums or users or avatars
    	if ($caller == 'forum' || $caller == 'user' || $caller == 'avatar') return false;

    	//only users with post permission > 1 can post
    	if ($caller == 'post' && $forum->post_permissions == 1) return false;

    	//only users with a topic permission > 1 can create topics
    	if ($caller == 'topic' && $forum->topic_permissions == 1) return false;

        //only users who are registerd can message people, and only if its enabled
        if ($caller == 'message' && ($guest || !$params['messaging_settings']['enable_messaging'])) return false;

        //only users with a attachment permission > 1 can add attachements
        if ($caller == 'attachment') {};

        //only registered users can add watches / people
        if (($caller == 'watch' || $caller == 'person') && $guest) return false;

    	return true;
    }

    /**
	 * Decide if a user has the ability to edit things
	 *
	 * @return boolean	true or false depending on permissions
	 */
    public function canEdit()
    {
    	$caller 	= $this->getMixer()->getIdentifier()->name;
    	$request 	= $this->getRequest();
        $me         = $this->getService('com://admin/ninjaboard.model.people')->getMe();
    	$forum 		= $this->getService('com://site/ninjaboard.model.forums')->id($request->forum)->getItem();
        $post       = $this->getService('com://site/ninjaboard.model.post')->id($request->id)->getItem();

    	// no one is allowed to edit forums or users or messages or avatars or watchers
    	if ($caller == 'forum' || $caller == 'user' || $caller == 'message' || $caller == 'avatar' || $caller == 'watch') return false;

        //if we are the owner of the post then we can edit
        if ($caller == 'post' && $post->created_by == $me->id) return true;
        
    	//if we were not the owner only users with manage post permission of 3 can edit
    	if ($caller == 'post' && $forum->post_permissions != 3) return false;

        //only users with a attachment permission > 1 can add attachements
        if ($caller == 'attachment') {};

        //only users with manage topic permission of 3 can edit
        if ($caller == 'topic' && $forum->topic_permissions != 3) return false;

        //only registred users can edit themself
        if ($caller == 'person' && JFactory::getUser()->guest) return false;

    	return true;
    }

    /**
	 * Decide if a user has the ability to Delete things
	 *
	 * @return boolean	true or false depending on permissions
	 */
    public function canDelete()
    {
    	$caller 	= $this->getMixer()->getIdentifier()->name;
    	$request 	= $this->getRequest();
    	$me 		= $this->getService('com://admin/ninjaboard.model.people')->getMe();
    	$forum 		= $this->getService('com://site/ninjaboard.model.forums')->id($request->forum)->getItem();
    	
    	// no one is allowed to delete forums or users or people or avatars
    	if ($caller == 'forum' || $caller == 'user' || $caller == 'person' || $caller == 'message' || $caller == 'avatar') return false;

    	// we can only delete watches if we exist
    	if ($caller == 'watch' && !$me->id) return false;

         //only users with a attachment permission > 1 can add attachements
        if ($caller == 'attachment') {};

    	//only users with manage post permission of 3 can delete
    	if ($caller == 'post' && $forum->post_permissions != 3) return false;

    	//only users with a manage topic permission of 3 can delete
    	if ($caller == 'topic' && $forum->topic_permissions != 3) return false;

    	return true;
    }

    /**
     * Decide if a user has the ability ot Read things
     */
    public function canRead()
    {
        $caller     = $this->getMixer()->getIdentifier()->name;
        $request    = $this->getRequest();
        $me         = $this->getService('com://admin/ninjaboard.model.people')->getMe();
        $forum      = $this->getService('com://site/ninjaboard.model.forums')->id($request->forum)->getItem();
        $post       = $this->getService('com://site/ninjaboard.model.post')->id($request->id)->getItem();

        // we can do what we please if we can mangage 
        if ($caller == 'post' && $forum->post_permissions == 3) return true;

        // if we have permission to post then allow it
        if ($caller == 'post' && $forum->post_permissions >= 2 && $post->isNew()) return true;

        // if we dont own the post and were editing (hmvc call calls actionRead on post view so check we are not a topic view)
        // we sure as hell cant read aka edit it
        if ($caller == 'post' && $post->created_by != $me->id && KRequest::get('get.view', 'string') != 'topic' ) return false;
    }
}