<?php
/**
 * @version $Id: usersync.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'usersync.php');

/**
 * Ninjaboard User Synchronization Controller
 *
 * @package Ninjaboard
 */
class ControllerUserSync extends JController
{

	/**
	 * compiles a list of rank
	 */
	function showUserSync() {
		
		// initialize variables
        $app                =& JFactory::getApplication();
		$db				    =& JFactory::getDBO();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		
		$context		= 'com_ninjaboard.ninjaboard_usersync_view';
		$limit			= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart		= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// parameter list
		$lists = array();
		
		$joomlaGroup = array();
		$joomlaGroup[] = JHTML::_('select.option', 0, JText::_('NB_SELECTJOOMLAGROUP'));
		$joomlaGroup[] = JHTML::_('select.option', 18, JText::_('NB_REGISTERED'));
		$joomlaGroup[] = JHTML::_('select.option', 19, JText::_('NB_AUTHOR'));
		$joomlaGroup[] = JHTML::_('select.option', 20, JText::_('NB_EDITOR'));
		$joomlaGroup[] = JHTML::_('select.option', 21, JText::_('NB_PUBLISHER'));
		$joomlaGroup[] = JHTML::_('select.option', 23, JText::_('NB_MANAGER'));
		$joomlaGroup[] = JHTML::_('select.option', 24, JText::_('NB_ADMINISTRATOR'));
		$joomlaGroup[] = JHTML::_('select.option', 25, JText::_('NB_SUPERADMINISTRATOR'));

		$lists['joomlagroup'] = JHTML::_('select.genericlist',  $joomlaGroup, 'joomlagroup', 'class="inputbox" size="1"', 'value', 'text', 0);

		// build the html radio buttons for show email
		$lists['show_email'] = JHTML::_('select.booleanlist', 'show_email', '', $ninjaboardConfig->getUserSettingsDefaults('show_email'));
		// build the html radio buttons for show online state
		$lists['show_online_state'] = JHTML::_('select.booleanlist', 'show_online_state', '', $ninjaboardConfig->getUserSettingsDefaults('show_online_state'));		
		// build the html radio buttons for notify on reply
		$lists['notify_on_reply'] = JHTML::_('select.booleanlist', 'notify_on_reply', '', $ninjaboardConfig->getUserSettingsDefaults('notify_on_reply'));	
		// build the html radio buttons for enable bbcode
		$lists['enablebbcode'] = JHTML::_('select.booleanlist', 'enable_bbcode', '', $ninjaboardConfig->getUserSettingsDefaults('enable_bbcode'));
		// build the html radio buttons for enable smilies
		$lists['enablesmilies'] = JHTML::_('select.booleanlist', 'enable_smilies', '', $ninjaboardConfig->getUserSettingsDefaults('enable_smilies'));
		// build the html radio buttons for enable bbcode
		$lists['systememails'] = JHTML::_('select.booleanlist', 'system_emails', '', 0);

		// list time zones		
		$query = "SELECT z.*"
				. "\n FROM #__ninjaboard_timezones AS z"
				. "\n ORDER BY z.ordering"
				;
		$db->setQuery($query);
		$lists['timezones'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'time_zone', 'class="inputbox" size="1"', 'offset', 'name', $ninjaboardConfig->getUserSettingsDefaults('time_zone'));

		// list time formats		
		$query = "SELECT f.*"
				. "\n FROM #__ninjaboard_timeformats AS f"
				. "\n ORDER BY f.name"
				;
		$db->setQuery($query);
		$lists['timeformats'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'time_format', 'class="inputbox" size="1"', 'timeformat', 'name', $ninjaboardConfig->getUserSettingsDefaults('time_format'));

		$roles = array();
		$roles[] = JHTML::_('select.option', 1, JText::_('NB_REGISTERED'));
		$roles[] = JHTML::_('select.option', 2, JText::_('NB_PRIVATE'));
		$roles[] = JHTML::_('select.option', 3, JText::_('NB_MODERATOR'));		
		$roles[] = JHTML::_('select.option', 4, JText::_('NB_ADMINISTRATOR'));
		$lists['roles'] = JHTML::_('select.genericlist',  $roles, 'role', 'class="inputbox" size="1"', 'value', 'text', $ninjaboardConfig->getUserSettingsDefaults('role'));
				
		ViewUserSync::showUserSync($lists);	
	}
	
	/**
	 * save the user
	 */	
	function performUserSync() {
	
        $app            =& JFactory::getApplication();
		$task		 	=  JRequest::getVar('task');
		$post			=  JRequest::get('post');
		$joomlaGroup	=  JRequest::getVar('joomlagroup', 0);

		if (!$joomlaGroup) {
			$msg = JText::_('NB_MSGSELECTJOOMLAGROUP');
			return false;
		}
		
		// initialize some variables
		$db	= & JFactory::getDBO();

		// get all existing joomla users
		$query = "SELECT u.id"
				. "\n FROM #__users AS u"
				. "\n WHERE u.gid = $joomlaGroup"
				;
		$db->setQuery($query);
		$joomlaUsers = $db->loadObjectList();	
	
		foreach ($joomlaUsers as $joomlaUser) {
			$ninjaboardUser = new NinjaboardUser($joomlaUser->id);
			
			if (!$ninjaboardUser->saveProfile($post)) {
				return false;
			}			
		}

		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSYNCHRONIZED', JText::_('NB_USERS'));
	}
	
}
?>
