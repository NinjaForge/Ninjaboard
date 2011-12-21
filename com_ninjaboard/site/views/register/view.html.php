<?php defined('_JEXEC') or die('Restricted access');
/**
 * @version $Id: view.html.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

jimport('joomla.application.component.view');

/**
 * Ninjaboard Register View
 *
 * @package Ninjaboard
 */
class NinjaboardViewRegister extends JView
{
	function display($tpl = null)
	{
		// Instantiate objects

		$db						=& JFactory::getDBO();
		$app					=& JFactory::getApplication();
		$session				=& JFactory::getSession();
		$document				=& JFactory::getDocument();
		$model					=& $this->getModel();
		$ninjaboardConfig		=& NinjaboardConfig::getInstance();
		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		$messageQueue			=& NinjaboardMessageQueue::getInstance();
		$breadCrumbs			=& NinjaboardBreadCrumbs::getInstance();
		$ninjaboardUser			=& new NinjaboardUser(0, true);

		$ninjaboardRegisterForm = $session->get('ninjaboardRegisterForm');
		$session->set('ninjaboardRegisterForm', null);

		// is registration allowed? 
		if (!$this->allowUserRegistration)
		{
			$messageQueue->addMessage(JText::_('NB_MSGREGISTRATIONNOTALLOWED'));

			$app->redirect(JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$this->Itemid, false));
		}
		
		if ($ninjaboardConfig->getBoardSettings('enable_terms') && !JRequest::getVar('agreed', '0') && !$ninjaboardRegisterForm)
		{
			$app->redirect(JRoute::_('index.php?option=com_ninjaboard&view=terms&Itemid='.$this->Itemid, false));
		}
		
		if (NinjaboardHelper::isUserLoggedIn())
		{
			$messageQueue->addMessage(JText::_('NB_MSGALREADYLOGGEDIN'));

			$app->redirect(JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$this->Itemid, false));
		}

		if ($ninjaboardRegisterForm)
			$ninjaboardUser->bind($testpost);
		
		$profilefieldsets	=  $model->getProfileFieldSets();
		$profilefields		=  $model->getProfileFields($ninjaboardUser);

		// load form validation behavior
		JHTML::_('behavior.formvalidation');

		// handle page title
		$document->setTitle(JText::_('NB_REGISTRATION'));

		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::_('NB_REGISTRATION'), '');		

		$this->assignRef('ninjaboardUser',		$ninjaboardUser);
		$this->assignRef('profilefieldsets',	$profilefieldsets);
		$this->assignRef('profilefields',		$profilefields);
		
		// build the html radio buttons for
		#	show email and online state
		#	notify on pm, on reply
		#	enable bbcode,emoticons
		$lists = array(
			'show_email'		=> JHTML::_('select.booleanlist', 'show_email',        '', $ninjaboardUser->get('show_email')),
			'show_online_state'	=> JHTML::_('select.booleanlist', 'show_online_state', '', $ninjaboardUser->get('show_online_state')),
			'notify_on_pm'		=> JHTML::_('select.booleanlist', 'notify_on_pm',      '', $ninjaboardUser->get('notify_on_pm')),
			'enable_bbcode'		=> JHTML::_('select.booleanlist', 'enable_bbcode',     '', $ninjaboardUser->get('enable_bbcode')),
			'enable_emoticons'	=> JHTML::_('select.booleanlist', 'enable_emoticons',  '', $ninjaboardUser->get('enable_emoticons')),
			'notify_on_reply'	=> JHTML::_('select.booleanlist', 'notify_on_reply',   '', $ninjaboardUser->get('notify_on_reply'))
		);
		// list time zones		
		$db->setQuery('
			SELECT z.*
			FROM #__ninjaboard_timezones AS z
			ORDER BY z.ordering
		');
		# TODO: Result checks
		$timezoneslist = $db->loadObjectList();
		
		$timezones = array();

		foreach ($timezoneslist as $timezone)
		{
			# !!! WEIDA !!!
			// ToDo: set config default timeformat instead of '%d.%m.%Y %H:%M'
			$timezone->name = '('. $timezone->name .') ['. NinjaboardHelper::formatDate(time(), '%d.%m.%Y %H:%M', $timezone->offset) .'] '. $timezone->description;

			$timezones[] = JHTML::_('select.option', $timezone->offset, $timezone->name, 'offset', 'name');
		}
		
		$lists['timezones'] = JHTML::_('select.genericlist', $timezones, 'time_zone', 'class="nbInputBox" size="1"', 'offset', 'name', $ninjaboardUser->get('time_zone'));

		// list time formats		
		$db->setQuery('
			SELECT f.*
			FROM #__ninjaboard_timeformats AS f
			ORDER BY f.name
		');
		# TODO: Result checks
		$timeformatslist = $db->loadObjectList();
		
		$timeformats = array();

		foreach ($timeformatslist as $timeformat)
		{
			$timeformat->name = NinjaboardHelper::formatDate(time(), $timeformat->timeformat, $ninjaboardConfig->getTimeZoneOffset());

			$timeformats[] = JHTML::_('select.option', $timeformat->timeformat, $timeformat->name, 'timeformat', 'name');
		}		

		# Sort the time format list, for better overview.
		usort($timeformats, create_function('$x, $y', '

			# We use a little quick sort, as callback function.

			$x = substr($x->name, 0, 1);
			$y = substr($y->name, 0, 1);

			if ($x == $y) return 0;

			return $x > $y ? 1 : -1;
		'));
		
		$lists['timeformats'] = JHTML::_('select.genericlist',  $timeformats, 'time_format', 'class="nbInputBox" size="1"', 'timeformat', 'name', $ninjaboardUser->get('time_format'));
		
		$this->assignRef('lists',           $lists);
		$this->assignRef('captchaRegister', $ninjaboardConfig->getCaptchaSettings('captcha_register'));
		$this->assignRef('buttonRegister',	$ninjaboardButtonSet->buttonByFunction['buttonRegister']);
		$this->assignRef('buttonReset',		$ninjaboardButtonSet->buttonByFunction['buttonReset']);
		$this->assignRef('buttonCancel',	$ninjaboardButtonSet->buttonByFunction['buttonCancel']);
			
		parent::display($tpl);
	}

}
?>
