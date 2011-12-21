<?php
/**
 * @version $Id: user.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'user.php');

/**
 * Ninjaboard User Controller
 *
 * @package Ninjaboard
 */
class ControllerUser extends JController
{

	/**
	 * compiles a list of users
	 */
	function showUsers() {
	
		$app			=& JFactory::getApplication();
		$db				=& JFactory::getDBO();
		$currentUser	=& JFactory::getUser();
		$acl			=& JFactory::getACL();
		
		$context			= 'com_ninjaboard.ninjaboard_user_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order",		'filter_order', 	'a.name');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$filter_type		= $app->getUserStateFromRequest("$context.filter_type", 		'filter_type', 		0);
		$filter_logged		= $app->getUserStateFromRequest("$context.filter_logged", 	'filter_logged', 	0);
		$search 			= $app->getUserStateFromRequest("$context.search", 			'search', 			'');
		$search 			= $db->getEscaped(trim(JString::strtolower($search)));
		$where 				= array();

		$limit			= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart		= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
	
		if (isset($search) && $search!= '') {
			$where[] = "(a.username LIKE '%$search%' OR a.email LIKE '%$search%' OR a.name LIKE '%$search%')";
		}
		if ($filter_type) {
			if ($filter_type == 'Public Frontend') {
				$where[] = "a.usertype = 'Registered' OR a.usertype = 'Author' OR a.usertype = 'Editor' OR a.usertype = 'Publisher'";
			} else if ($filter_type == 'Public Backend') {
				$where[] = "a.usertype = 'Manager' OR a.usertype = 'Administrator' OR a.usertype = 'Super Administrator'";
			} else {
				$where[] = "a.usertype = LOWER('$filter_type')";
			}
		}
		if ($filter_logged == 1) {
			$where[] = "s.userid = a.id";
		} else if ($filter_logged == 2) {
			$where[] = "s.userid IS NULL";
		}
	
		// exclude any child group id's for this user
		$pgids = $acl->get_group_children($currentUser->get('gid'), 'ARO', 'RECURSE');
	
		if (is_array($pgids) && count($pgids) > 0) {
			$where[] = "(a.gid NOT IN (" . implode(',', $pgids) . "))";
		}
		$filter = '';
		if ($filter_logged == 1 || $filter_logged == 2) {
			$filter = "\n INNER JOIN #__session AS s ON s.userid = a.id";
		}

		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		$where = (count($where) ? "\n WHERE " . implode(' AND ', $where) : '');
	
		$query = "SELECT COUNT(a.id)"
				. "\n FROM #__users AS a"
				. $filter
				. $where
				;
		$db->setQuery($query);
		$total = $db->loadResult();
	
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		$query = "SELECT a.*, g.name AS groupname, ju.role"
				. "\n FROM #__users AS a"
				. "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"
				. "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.id"
				. "\n INNER JOIN #__core_acl_aro_groups AS g ON g.id = gm.group_id"
				. "\n LEFT JOIN #__ninjaboard_users AS ju ON ju.id = a.id"
				. $filter
				. $where
				. "\n GROUP BY a.id"
				. $orderby
				;
		$db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$rows = $db->loadObjectList();
			
		$n = count($rows);
		$template = "SELECT COUNT(s.userid)"
				. "\n FROM #__session AS s"
				. "\n WHERE s.userid = %d"
				;
		for ($i = 0; $i < $n; $i++) {
			$row = &$rows[$i];
			$query = sprintf($template, intval($row->id));
			$db->setQuery($query);
			$row->loggedin = $db->loadResult();
		}

		// get list of Groups for dropdown filter
		$query = "SELECT name AS value, name AS text"
				. "\n FROM #__core_acl_aro_groups"
				. "\n WHERE name != 'ROOT'"
				. "\n AND name != 'USERS'"
				;
		$db->setQuery($query);
		$types[] = JHTML::_('select.option', '0', '- '.JText::_('NB_SELECTGROUP').' -', 'value', 'text');
		$types = array_merge($types, $db->loadObjectList());
		$lists['type'] = JHTML::_('select.genericlist',  $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter_type);		
		
		// ninjaboard user roles
		$roles = array();
		$roles[0] = JText::_('NB_NONE');
		$roles[1] = JText::_('NB_REGISTERED');
		$roles[2] = JText::_('NB_PRIVATE');
		$roles[3] = JText::_('NB_MODERATOR');		
		$roles[4] = JText::_('NB_ADMINISTRATOR');
		$lists['roles'] = $roles;
		
		// get list of Log Status for dropdown filter
		$logged[] = JHTML::_('select.option', 0, '- '.JText::_('NB_SELECTLOGSTATUS').' -', 'value', 'text');
		$logged[] = JHTML::_('select.option', 1, '- '.JText::_('NB_LOGGEDIN').' -', 'value', 'text');
		$lists['logged'] = JHTML::_('select.genericlist',  $logged, 'filter_logged', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter_logged);		
	
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order'] = $filter_order;

		// search filter
		$lists['search'] = $search;
			
		ViewUser::showUsers($rows, $pageNav, $lists);				

	}

	/**
	 * cancels edit user operation
	 */
	function cancelEditUser() {
		$app    =& JFactory::getApplication();
		$link   =  'index.php?option=com_ninjaboard&task=ninjaboard_user_view';
		$app->redirect($link);
	}

	/**
	 * edit the user
	 */
	function editUser() {
		
		$app			    =& JFactory::getApplication();
		$db				    =& JFactory::getDBO();
		$ninjaboardConfig   =& NinjaboardConfig::getInstance();

		$cid 	= JRequest::getVar('cid', array(0));
		$option = JRequest::getVar('option');
		
		if (!is_array($cid)) {
			$cid = array(0);
		}

		$ninjaboardUser =& NinjaboardUser::getInstance($cid[0]);

		$lists = array();

		// build the html radio buttons for block
		$lists['block'] = JHTML::_('select.booleanlist', 'block', '', $ninjaboardUser->get('block'));				
		// build the html radio buttons for sendEmail
		$lists['sendEmail'] = JHTML::_('select.booleanlist', 'sendEmail', '', $ninjaboardUser->get('sendEmail'));
		// build the html radio buttons for show email
		$lists['show_email'] = JHTML::_('select.booleanlist', 'show_email', '', $ninjaboardUser->get('show_email'));
		// build the html radio buttons for show online state
		$lists['show_online_state'] = JHTML::_('select.booleanlist', 'show_online_state', '', $ninjaboardUser->get('show_online_state'));		
		// build the html radio buttons for notify on reply
		$lists['notify_on_reply'] = JHTML::_('select.booleanlist', 'notify_on_reply', '', $ninjaboardUser->get('notify_on_reply'));	
		// build the html radio buttons for enable bbcode
		$lists['enablebbcode'] = JHTML::_('select.booleanlist', 'enable_bbcode', '', $ninjaboardUser->get('enable_bbcode'));
		// build the html radio buttons for enable emoticons
		$lists['enableemoticons'] = JHTML::_('select.booleanlist', 'enable_emoticons', '', $ninjaboardUser->get('enable_emoticons'));
		// build the html radio buttons for enable bbcode
		$lists['systememails'] = JHTML::_('select.booleanlist', 'system_emails', '', $ninjaboardUser->get('system_emails'));

		// list time zones		
		$query = "SELECT z.*"
				. "\n FROM #__ninjaboard_timezones AS z"
				. "\n ORDER BY z.ordering"
				;
		$db->setQuery($query);
		$lists['timezones'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'time_zone', 'class="inputbox" size="1"', 'offset', 'name', $ninjaboardUser->get('time_zone'));

		// list time formats		
		$query = "SELECT f.*"
				. "\n FROM #__ninjaboard_timeformats AS f"
				. "\n ORDER BY f.name"
				;
		$db->setQuery($query);
		$lists['timeformats'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'time_format', 'class="inputbox" size="1"', 'timeformat', 'name', $ninjaboardUser->get('time_format'));

		$roles = array();
		$roles[] = JHTML::_('select.option', 1, JText::_('NB_REGISTERED'));
		$roles[] = JHTML::_('select.option', 2, JText::_('NB_PRIVATE'));
		$roles[] = JHTML::_('select.option', 3, JText::_('NB_MODERATOR'));		
		$roles[] = JHTML::_('select.option', 4, JText::_('NB_ADMINISTRATOR'));
		$lists['roles'] = JHTML::_('select.genericlist',  $roles, 'role', 'class="inputbox" size="1"', 'value', 'text', $ninjaboardUser->get('role'));

		// list forums
		$query = "SELECT f.*" 
				. "\n FROM #__ninjaboard_forums AS f"
				. "\n WHERE f.state = 1"	
				. "\n ORDER BY f.name"
				;
		$db->setQuery($query);
		$forumrows = $db->loadObjectList();
			
		$forums = array();
		foreach($forumrows as $forumsrow) {
			$forums[] = JHTML::_('select.option', $forumsrow->id, $forumsrow->name);
		}
		
		// get administrated forums by the user
		$query = "SELECT fa.id_forum AS value" 
				. "\n FROM #__ninjaboard_forums_auth AS fa"
				. "\n WHERE fa.role = 4"
				. "\n AND fa.id_user = ".$ninjaboardUser->get('id')
				;
		$db->setQuery($query);
		$lists['administratedforums'] = JHTML::_('select.genericlist',  $forums, 'administratedforums[]', 'class="inputbox" size="10" multiple="multiple"', 'value', 'text', $db->loadObjectList());
			
		// get moderated forums by the user
		$query = "SELECT fa.id_forum AS value" 
				. "\n FROM #__ninjaboard_forums_auth AS fa"
				. "\n WHERE fa.role = 3"
				. "\n AND fa.id_user = ".$ninjaboardUser->get('id')
				;
		$db->setQuery($query);
		$selectedgroups = $db->loadObjectList();
		$lists['moderatedforums'] = JHTML::_('select.genericlist',  $forums, 'moderatedforums[]', 'class="inputbox" size="10" multiple="multiple"', 'value', 'text', $db->loadObjectList());
			
		// get forums with private access for the user
		$query = "SELECT fa.id_forum AS value" 
				. "\n FROM #__ninjaboard_forums_auth AS fa"
				. "\n WHERE fa.role = 2"
				. "\n AND fa.id_user = ".$ninjaboardUser->get('id')
				;
		$db->setQuery($query);
		$lists['privateforums'] = JHTML::_('select.genericlist',  $forums, 'privateforums[]', 'class="inputbox" size="10" multiple="multiple"', 'value', 'text', $db->loadObjectList());
	
		// list groups
		$query = "SELECT g.*" 
				. "\n FROM #__ninjaboard_groups AS g"
				. "\n WHERE g.published = 1"	
				. "\n ORDER BY g.name"
				;
		$db->setQuery($query);
		$grouprows = $db->loadObjectList();

		$groups = array();
		foreach($grouprows as $group) {
			$groups[] = JHTML::_('select.option', $group->id, $group->name);
		}
		$query = "SELECT gu.id_group AS value" 
				. "\n FROM #__ninjaboard_groups_users AS gu"
				. "\n WHERE gu.id_user = ".$ninjaboardUser->get('id')
				;
		$db->setQuery($query);
		$selectedgroups = $db->loadObjectList();
		$lists['groups'] = JHTML::_('select.genericlist',  $groups, 'groups[]', 'class="inputbox" size="10" multiple="multiple"', 'value', 'text', $selectedgroups);

		// get profile data
		$query = "SELECT s.*"
				. "\n FROM #__ninjaboard_profiles_fields_sets AS s"
				. "\n WHERE s.published = 1"
				. "\n ORDER BY s.ordering"
				;
		$db->setQuery($query);
		$lists['fieldsets'] = $db->loadObjectList();
		
		$query = "SELECT f.*"
				. "\n FROM #__ninjaboard_profiles_fields AS f"
				. "\n WHERE f.published = 1"
				. "\n ORDER BY f.ordering"
				;
		$db->setQuery($query);
		$fieldrows = $db->loadObjectList();
		
		$fields = array();
		foreach($fieldrows as $fieldrow) {
			$fields[] = NinjaboardHelper::createElement($fieldrow, $ninjaboardUser->get($fieldrow->name));
		}
		$lists['fields'] = $fields;
		
		ViewUser::editUser($ninjaboardUser, $row, $lists);
	}
	
	/**
	 * save the user
	 */	
	function saveUser() {
	
		$task 	= JRequest::getCmd('task');
		$option = JRequest::getCmd('option');
		$post	= JRequest::get('post');
	
		// initialize some variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$MailFrom	=  $app->getCfg('mailfrom');
		$FromName	=  $app->getCfg('fromname');
		$SiteName	=  $app->getCfg('sitename');

		// create a new user
		$cid = JRequest::getVar('id', 0, 'post', 'int');
		$ninjaboardUser = new NinjaboardUser($cid);		
		
		$isNewUser = ($ninjaboardUser->get('id') < 1);
		
		if (!$ninjaboardUser->bind($post)) {
			$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $ninjaboardUser->getError());
			return false;
		}
			
		// lets save the user
		if (!$ninjaboardUser->save()) {
			$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $ninjaboardUser->getError());
			return false;
		}

		// save forums administrated by the user
		$administratedforums = JRequest::getVar('administratedforums', array(), 'post', 'array');
		if (is_array($administratedforums)) {
		
			// first delete all moderating authentifications
			$query = "DELETE FROM #__ninjaboard_forums_auth"
					. "\n WHERE id_user = ".$ninjaboardUser->get('id')
					. "\n AND role = 4"
					;
			$db->setQuery($query);
			if (!$db->query()) {
				$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $db->getErrorMsg());
			}
			
			// add all selected forums which the user should modarate		
			foreach ($administratedforums as $administratedforum) {
				$query = "INSERT INTO #__ninjaboard_forums_auth"
						. "\n SET id_forum = $administratedforum, role = 4, id_user = ".$ninjaboardUser->get('id')
						;
				$db->setQuery($query);
				if (!$db->query()) {
					$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $db->getErrorMsg());	
				}
			}
		}	

		// save forums moderated by the user
		$moderatedforums = JRequest::getVar('moderatedforums', array(), 'post', 'array');
		if (is_array($moderatedforums)) {
		
			// first delete all moderating authentifications
			$query = "DELETE FROM #__ninjaboard_forums_auth"
					. "\n WHERE id_user = ".$ninjaboardUser->get('id')
					. "\n AND role = 3"
					;
			$db->setQuery($query);
			if (!$db->query()) {
				$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $db->getErrorMsg());
			}
			
			// add all selected forums which the user should modarate		
			foreach ($moderatedforums as $moderatedforum) {
				$query = "INSERT INTO #__ninjaboard_forums_auth"
				. "\n SET id_forum = $moderatedforum, role = 3, id_user = ".$ninjaboardUser->get('id')
				;
				$db->setQuery($query);
				if (!$db->query()) {
					$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $db->getErrorMsg());	
				}
			}
		}

		// save forums with private access for the user
		$privateforums = JRequest::getVar('privateforums', array(), 'post', 'array');
		if (is_array($privateforums)) {
		
			// first delete all moderating authentifications
			$query = "DELETE FROM #__ninjaboard_forums_auth"
					. "\n WHERE id_user = ".$ninjaboardUser->get('id')
					. "\n AND role = 2"
					;
			$db->setQuery($query);
			if (!$db->query()) {
				$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $db->getErrorMsg());
			}
			
			// add all selected forums which the user should modarate		
			foreach ($privateforums as $privateforum) {
				$query = "INSERT INTO #__ninjaboard_forums_auth"
						. "\n SET id_forum = $privateforum, role = 2, id_user = ".$ninjaboardUser->get('id')
						;
				$db->setQuery($query);
				if (!$db->query()) {
					$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $db->getErrorMsg());	
				}
			}
		}
				
		// save groups
		$groups = JRequest::getVar('groups', array(), 'post', 'array');
		if (is_array($groups)) {
		
			// first delete all user groups
			$query = "DELETE FROM #__ninjaboard_groups_users"
					. "\n WHERE id_user = ".$ninjaboardUser->get('id')
					;
			$db->setQuery($query);
			if (!$db->query()) {
				$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $db->getErrorMsg());
			}
			
			// add all selected groups to the user		
			foreach ($groups as $group) {
				$query = "INSERT INTO #__ninjaboard_groups_users"
						. "\n SET id_group = $group, id_user = ".$ninjaboardUser->get('id')
						;
				$db->setQuery($query);
				if (!$db->query()) {
					$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $cid .'&hidemainmenu=1', $db->getErrorMsg());	
				}
			}
		}
		
		// handle email sending
		if ($isNewUser) {
			JRequest::setVar('password', $ninjaboardUser->password_clear);
			NinjaboardHelper::sendRegistrationMail($ninjaboardUser);
		}
				
		switch ($task) {
			case 'ninjaboard_user_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_user_edit&cid[]='. $ninjaboardUser->get('id') .'&hidemainmenu=1';;
				break;
			case 'ninjaboard_user_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_user_view';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_USER'), $ninjaboardUser->get('name'));
		$app->redirect($link, $msg);

	}
	
	/**
	 * delete the user
	 */	
	function deleteUser() {
		
		// initialize variables
		$app			=& JFactory::getApplication();
		$db 			=& JFactory::getDBO();
		$currentUser 	=& JFactory::getUser();
		$acl			=& JFactory::getACL();
		$cid 			=  JRequest::getVar('cid', array(), '', 'array');

		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			JError::raiseError(500, JText::_('Select a User to delete', true));
		}
		
		// ToDo: original joomla code => rewrite for ninjaboard needs
		foreach ($cid as $id) {
			// check for a super admin ... can't delete them
			$objectID 	= $acl->get_object_id('users', $id, 'ARO');
			$groups 	= $acl->get_object_groups($objectID, 'ARO');
			$this_group = strtolower($acl->get_group_name($groups[0], 'ARO'));

			$success = false;
			if ($this_group == 'super administrator') {
				$msg = JText::_('You cannot delete a Super Administrator');
			} else if ($id == $currentUser->get('id')) {
				$msg = JText::_('You cannot delete Yourself!');
			} else if (($this_group == 'administrator') && ($currentUser->get('gid') == 24)) {
				$msg = JText::_('WARNDELETE');
			} else {
				$ninjaboardUser =& NinjaboardUser::getInstance((int)$id);
				$count = 2;

				if ($ninjaboardUser->get('gid') == 25) {
					// count number of active super admins
					$query = "SELECT COUNT(id)"
							. "\n FROM #__users"
							. "\n WHERE gid = 25"
							. "\n AND block = 0"
					;
					$db->setQuery($query);
					$count = $db->loadResult();
				}

				if ($count <= 1 && $ninjaboardUser->get('gid') == 25) {
					// cannot delete Super Admin where it is the only one that exists
					$msg = "You cannot delete this Super Administrator as it is the only active Super Administrator for your site";
				} else {
					// delete user
					$ninjaboardUser->delete();
					$msg = '';

					JRequest::setVar('task', 'remove');
					JRequest::setVar('cid', $id);

					// delete user acounts active sessions
					ControllerUser::logoutUser();
				}
			}
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_view', $msg);
	}
	
	/**
	 * blocks or unblocks user
	 * @param integer 0 if unblock, 1 if blocking
	 */
	function changeUserBlock($block=1) {
	
		$app	=& JFactory::getApplication();
		$db 	=& JFactory::getDBO();
		$cid 	=  JRequest::getVar('cid', array(), '', 'array');
	
		JArrayHelper::toInteger($cid);
	
		if (count($cid) < 1) {
			$action = $block ? 'block' : 'unblock';
			echo "<script> alert('". JText::_('Select an item to', true) ." ". $action ."'); window.history.go(-1);</script>\n";
			exit;
		}
	
		$cids = implode(',', $cid);
	
		$query = "UPDATE #__users"
				. "\n SET block = $block"
				. "\n WHERE id IN ($cids)"
				;
		$db->setQuery($query);
	
		if (!$db->query()) {
			echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
	
		// if action is to block a user
		if ($block == 1) {
			foreach($cid as $id) {
				JRequest::setVar('cid', $id);
					
				// delete user acounts active sessions
				ControllerUser::logoutUser();
			}
		}
		
		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_view');
	}
	
	/**
	 * logout user
	 */
	function logoutUser() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		$task		= JRequest::getVar('task');
		$msgType	= '';
		
		JArrayHelper::toInteger($cid);

		if (count($cid)) {
			
			// how many users are there to logout?
			if (count($cid) == 1) {
				$ninjaboardUser =& NinjaboardUser::getInstance($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYLOGGEDOUT', JText::_('NB_USER'), $ninjaboardUser->get('name'));
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYLOGGEDOUT', JText::_('NB_USERS'), '');
			}

			$query = "DELETE FROM #__session"
					. "\n WHERE userid = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
			
			if ($task == 'ninjaboard_user_block') {
				return;
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_USER'), JText::_('NB_LOGOUT'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_user_view', $msg, $msgType);
	}
		
}
?>
