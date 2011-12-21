<?php defined('_JEXEC') or die('Restricted access');
/**
 * @version $Id: router.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

function NinjaboardBuildRoute(&$query)
{
	$segments = array();
	
	if(isset($query['view'])) {
		$segments[] = $query['view'];
		unset($query['view']);
	}
	
	if(isset($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	
	if(isset($query['forum'])) {
		$segments[] = NinjaboardHelper::getForumURL($query['forum']);
		unset($query['forum']);
	}
	
	if(isset($query['topic'])) {
		$segments[] = NinjaboardHelper::getTopicURL($query['topic']);
		unset($query['topic']);
	}
	
	if(isset($query['post'])) {
		$segments[] = $query['post'];
		unset($query['post']);
	}

	if(isset($query['user'])) {
		$segments[] = $query['user'];
		unset($query['user']);
	}
	
	if(isset($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}
				
	return $segments;
}

function NinjaboardParseRoute($segments)
{
	$vars = array();

	// get the active menu item
	$menu =& JSite::getMenu();
	$item =& $menu->getActive();
	
	// count route segments
	$count = count($segments);
	
	$vars['view'] = $segments[0];
	
	switch($vars['view']) {
		case 'board' :
			$vars['board'] = $segments[$count-1];
			break;
		case 'forum' :
			$vars['forum'] = substr($segments[$count-1], 0, strpos($segments[$count-1], ':'));
			break;
		case 'topic' :
			$vars['topic'] = substr($segments[$count-1], 0, strpos($segments[$count-1], ':'));
			break;
		case 'edittopic' :
			if ($segments[$count-1] == 0) {
				$vars['forum'] = $segments[$count-2];
				$vars['topic'] = $segments[$count-1];
			} else {
				$vars['topic'] = $segments[$count-2];
				$vars['post'] = $segments[$count-1];				
			}
			break;
		case 'editpost' :
			if ($count < 4) {
				$vars['topic'] = $segments[$count-2];
				$vars['post'] = $segments[$count-1];			
			} else {
				$vars['topic'] = $segments[$count-3];
				$vars['post'] = $segments[$count-2];
				$vars['quote'] = $segments[$count-1];				
			}
			break;
		case 'movetopic' :
			$vars['topic'] = substr($segments[$count-1], 0, strpos($segments[$count-1], ':'));
			break;
		case 'information' :
			if ($count == 2) {
				$vars['info'] = $segments[$count-1];
			} else {
				$vars['info'] = $segments[$count-2];
				$vars['user'] = $segments[$count-1];			
			}
			break;
		case 'profile' :
			$vars['id'] = $segments[$count-1];
			break;
		case 'reportpost' :
			$vars['post'] = $segments[$count-1];
			break;
		case 'login' :
			break;
		case 'editprofile' :
			$vars['id'] = $segments[$count-1];
			break;
		case 'userposts' :
			$vars['id'] = $segments[$count-1];
			break;
		case 'requestlogin' :
			break;
		case 'register' :
			break;
		case 'terms' :
			break;
		case 'search' :
			break;
		case 'latestposts' :
			break;
		case 'userlist' :
			break;
		case 'ninjaboarddeletetopic' :
			unset($vars['view']);
			$vars['task'] = $segments[0];
			$vars['topic'] = $segments[$count-1];
			break;
		case 'ninjaboarddeletepost' :
			unset($vars['view']);
			$vars['task'] = $segments[0];
			$vars['post'] = $segments[$count-1];
			break;
		case 'ninjaboardlogout' :
			unset($vars['view']);
			$vars['task'] = $segments[0];
			break;
		case 'ninjaboardlocktopic' || 'ninjaboardunlocktopic':
			unset($vars['view']);
			$vars['task'] = $segments[0];
			$vars['topic'] = $segments[$count-1];
			break;
		default:
			$vars['view']  = $segments[0];
	}
	
	return $vars;
}
