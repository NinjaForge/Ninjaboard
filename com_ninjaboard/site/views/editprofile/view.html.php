<?php
/**
 * @version $Id: view.html.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Ninjaboard Edit Profile View
 *
 * @package Ninjaboard
 */
class NinjaboardViewEditProfile extends JView
{

	function display($tpl = null) {
		global $mainframe;

		// initialize variables
		$db				=& JFactory::getDBO();
		$document		=& JFactory::getDocument();
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
		
		if ($ninjaboardUser->get('id') < 1) {
			$msg = JText::_('NB_MSGNOPERMISSIONEDITPROFILE');
			$messageQueue->addMessage($msg);
			$redirect = 'index.php?option=com_ninjaboard&view=board&Itemid='.$this->Itemid;
			$mainframe->redirect($redirect);
		}
				
		$model				=& $this->getModel();
		$profilefieldsets	= $model->getProfileFieldSets();
		$profilefields		= $model->getProfileFields($ninjaboardUser);

		// load form validation behavior
		JHTML::_('behavior.formvalidation');
		
		// handle page title
		$document->setTitle(JText::_('NB_EDITPROFILE'));
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_('NB_EDITPROFILE'), '');
	
		// ToDo: remeber last url (site) and set redirect to it	
		$redirect = 'index.php?option=com_ninjaboard&view=board&Itemid='.$this->Itemid;
				
		$this->assignRef('ninjaboardUser', $ninjaboardUser);
		$this->assignRef('profilefieldsets', $profilefieldsets);
		$this->assignRef('profilefields', $profilefields);
		$this->assignRef('redirect', $redirect);
		
		$lists = array();
		
		// build the html radio buttons for show email
		$lists['show_email'] = JHTML::_('select.booleanlist', 'show_email', '', $ninjaboardUser->get('show_email'));
		// build the html radio buttons for show online state
		$lists['show_online_state'] = JHTML::_('select.booleanlist', 'show_online_state', '', $ninjaboardUser->get('show_online_state'));

		// build the html radio buttons for enable bbcode
		$lists['enable_bbcode'] = JHTML::_('select.booleanlist', 'enable_bbcode', '', $ninjaboardUser->get('enable_bbcode'));
		// build the html radio buttons for enable emoticons
		$lists['enable_emoticons'] = JHTML::_('select.booleanlist', 'enable_emoticons', '', $ninjaboardUser->get('enable_emoticons'));
		// build the html radio buttons for notify on reply
		$lists['notify_on_reply'] = JHTML::_('select.booleanlist', 'notify_on_reply', '', $ninjaboardUser->get('notify_on_reply'));		

		// list time zones		
		$query = "SELECT z.*"
				. "\n FROM #__ninjaboard_timezones AS z"
				. "\n ORDER BY z.ordering"
				;
		$db->setQuery($query);
		$timezoneslist = $db->loadObjectList();
		
		$timezones = array();
		foreach ($timezoneslist as $timezone) {
			// ToDo: set config default timeformat instead of '%d.%m.%Y %H:%M'
			$timezone->name = $timezone->name .' - '. $timezone->description .' ('. NinjaboardHelper::formatDate(time(), '%d.%m.%Y %H:%M', $timezone->offset) .') ';
			$timezones[] = JHTML::_('select.option', $timezone->offset, $timezone->name, 'offset', 'name');
		}
		$lists['timezones'] = JHTML::_('select.genericlist', $timezones, 'time_zone', 'class="jbInputBox" size="1"', 'offset', 'name', $ninjaboardUser->get('time_zone'));

		// list time formats		
		$query = "SELECT f.*"
				. "\n FROM #__ninjaboard_timeformats AS f"
				. "\n ORDER BY f.name"
				;
		$db->setQuery($query);
		$timeformatslist = $db->loadObjectList();
		
		$timeformats = array();
		foreach ($timeformatslist as $timeformat) {
			$timeformat->name = NinjaboardHelper::formatDate(time(), $timeformat->timeformat, $ninjaboardConfig->getTimeZoneOffset());
			$timeformats[] = JHTML::_('select.option', $timeformat->timeformat, $timeformat->name, 'timeformat', 'name');
		}
		$lists['timeformats'] = JHTML::_('select.genericlist',  $timeformats, 'time_format', 'class="jbInputBox" size="1"', 'timeformat', 'name', $ninjaboardUser->get('time_format'));
		
		$this->assignRef('lists', $lists);
		
		// handle avatar
		$enableAvatars = $ninjaboardConfig->getAvatarSettings('enable_avatars');
		$this->assignRef('enableAvatars', $enableAvatars);
		
		if ($enableAvatars) {
			$avatarFile = $ninjaboardUser->get('ninjaboardAvatar')->avatarFile;
			$avatarFileAlt = $ninjaboardUser->get('name');
			$this->assignRef('avatarFile', $avatarFile);
			$this->assignRef('avatarFileAlt', $avatarFileAlt);
		}	
		
		// get buttons
		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		$this->assignRef('buttonSubmit', $ninjaboardButtonSet->buttonByFunction['buttonSubmit']);
		$this->assignRef('buttonCancel', $ninjaboardButtonSet->buttonByFunction['buttonCancel']);
							
		parent::display($tpl);
	}

}
?>
