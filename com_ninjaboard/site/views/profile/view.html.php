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
class NinjaboardViewProfile extends JView
{

	function display($tpl = null) {
		global $mainframe, $Itemid, $option;

		// initialize variables
		$db				=& JFactory::getDBO();
		$document		=& JFactory::getDocument();
		$ninjaboardUserView	=& NinjaboardUser::getInstance(JRequest::getVar('id', 0));
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();
				
		$model				=& $this->getModel();
		$profilefieldsets	= $model->getProfileFieldSets();
		$profilefields		= $model->getProfileFields($ninjaboardUserView);

		// load form validation behavior
		JHTML::_('behavior.formvalidation');
		
		// handle page title
		$document->setTitle(JText::sprintf('NB_PROFILEFROM', $ninjaboardUserView->get('name'), $ninjaboardConfig->getBoardSettings('board_name')));
		
		// handle metadata
		$document->setDescription(JText::sprintf('NB_PROFILEFROMDESC', $ninjaboardUserView->get('name'), $ninjaboardConfig->getBoardSettings('board_name')));
		$keywords = JText::_($ninjaboardUserView->get('name')) .', '. JText::_($ninjaboardConfig->getBoardSettings('board_name'));
		$document->setMetadata('keywords', $keywords);
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::sprintf('NB_PROFILEFROM', $ninjaboardUserView->get('name'), $ninjaboardConfig->getBoardSettings('board_name')));
				
		$this->assignRef('ninjaboardUserView', $ninjaboardUserView);
		$this->assignRef('profilefieldsets', $profilefieldsets);
		$this->assignRef('profilefields', $profilefields);
		
		if (!$ninjaboardUserView->get('show_email')) {
			$ninjaboardUserView->set('email', '');
		}
		
		// handle avatar
		$enableAvatars = $ninjaboardConfig->getAvatarSettings('enable_avatars');
		$this->assignRef('enableAvatars', $enableAvatars);
		
		if ($enableAvatars) {
			$avatarFile = $ninjaboardUserView->get('ninjaboardAvatar')->avatarFile;
			$avatarFileAlt = $ninjaboardUserView->get('name');
			$this->assignRef('avatarFile', $avatarFile);
			$this->assignRef('avatarFileAlt', $avatarFileAlt);
		}	
					
		parent::display($tpl);
	}

}
?>