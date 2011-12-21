<?php
/**
 * @version $Id: toolbar.ninjaboard.html.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
 // no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Toolbar Html
 *
 * @package Ninjaboard
 */
class TOOLBAR_ninjaboard {
	function _NB_FORUM() {
		JToolBarHelper::title(JText::_('NB_FORUMMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_forum_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_forum_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_forum_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
		
	function _NB_FORUM_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
	
		JToolBarHelper::title(JText::_('NB_FORUMMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_forum_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);		
		JToolBarHelper::custom('ninjaboard_forum_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);
		JToolBarHelper::custom('ninjaboard_forum_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}
	
	function _NB_CATEGORY() {
		JToolBarHelper::title(JText::_('NB_CATEGORYMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_category_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_category_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_category_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_CATEGORY_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
	
		JToolBarHelper::title(JText::_('NB_CATEGORYMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_category_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_category_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_category_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);		
	}
		
	function _NB_CONFIG() {
		JToolBarHelper::title(JText::_('NB_CONFIGMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_config_default', 'default.png', 'default_f2.png', 'NB_DEFAULT', false);
		JToolBarHelper::custom('ninjaboard_config_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_config_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_config_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_CONFIG_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
	
		JToolBarHelper::title(JText::_('NB_CONFIGMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_config_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_config_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_config_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);		
	}
		
	function _NB_TIMEZONE() {
		JToolBarHelper::title(JText::_('NB_TIMEZONEMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_timezone_default', 'default.png', 'default_f2.png', 'NB_DEFAULT', false);
		JToolBarHelper::custom('ninjaboard_timezone_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_timezone_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_timezone_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_TIMEZONE_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
	
		JToolBarHelper::title(JText::_('NB_TIMEZONEMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_timezone_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_timezone_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_timezone_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);		
	}
		
	function _NB_TIMEFORMAT() {
		JToolBarHelper::title(JText::_('NB_TIMEFORMATMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_timeformat_default', 'default.png', 'default_f2.png', 'NB_DEFAULT', false);
		JToolBarHelper::custom('ninjaboard_timeformat_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_timeformat_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_timeformat_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_TIMEFORMAT_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
	
		JToolBarHelper::title(JText::_('NB_TIMEFORMATMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_timeformat_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_timeformat_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_timeformat_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);		
	}
		
	function _NB_DESIGN() {
		JToolBarHelper::title(JText::_('NB_DESIGNMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_design_default', 'default.png', 'default_f2.png', 'NB_DEFAULT', false);
		JToolBarHelper::custom('ninjaboard_design_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_design_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_design_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_DESIGN_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
	
		JToolBarHelper::title(JText::_('NB_DESIGNMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_design_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_design_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_design_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}

						
	function _NB_USER() {
		JToolBarHelper::title(JText::_('NB_USERMANAGER'), 'ninjahdr');	
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_user_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_user_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_user_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
		JToolBarHelper::custom('ninjaboard_user_logout', 'cancel.png', 'cancel_f2.png', 'NB_LOGOUT', false);
	}
	
	function _NB_USER_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
			
		JToolBarHelper::title(JText::_('NB_USERMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_user_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_user_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_user_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}

	function _NB_GROUP() {
		JToolBarHelper::title(JText::_('NB_GROUPMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_group_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_group_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_group_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_GROUP_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
			
		JToolBarHelper::title(JText::_('NB_GROUPMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_group_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_group_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_group_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}

	function _NB_RANK() {
		JToolBarHelper::title(JText::_('NB_RANKMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_rank_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_rank_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_rank_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_RANK_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
			
		JToolBarHelper::title(JText::_('NB_RANKMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_rank_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_rank_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_rank_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}

	function _NB_PROFILEFIELDSET() {
		JToolBarHelper::title(JText::_('NB_PROFILEFIELDSETMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_profilefieldset_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_profilefieldset_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_profilefieldset_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_PROFILEFIELDSET_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
			
		JToolBarHelper::title(JText::_('NB_PROFILEFIELDSETMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_profilefieldset_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_profilefieldset_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_profilefieldset_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}

	function _NB_PROFILEFIELD() {
		JToolBarHelper::title(JText::_('NB_PROFILEFIELDMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_profilefield_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_profilefield_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_profilefield_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_PROFILEFIELD_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
			
		JToolBarHelper::title(JText::_('NB_PROFILEFIELDMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_profilefield_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_profilefield_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_profilefield_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}

	function _NB_PROFILEFIELDLIST() {
		JToolBarHelper::title(JText::_('NB_PROFILEFIELDLISTMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_profilefieldlist_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_profilefieldlist_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_profilefieldlist_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_PROFILEFIELDLIST_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
			
		JToolBarHelper::title(JText::_('NB_PROFILEFIELDLISTMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_profilefieldlist_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_profilefieldlist_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_profilefieldlist_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}

	function _NB_PROFILEFIELDLISTVALUE() {
		JToolBarHelper::title(JText::_('NB_PROFILEFIELDLISTVALUEMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_profilefieldlistvalue_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_profilefieldlistvalue_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_profilefieldlistvalue_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_PROFILEFIELDLISTVALUE_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
			
		JToolBarHelper::title(JText::_('NB_PROFILEFIELDLISTVALUEMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_profilefieldlistvalue_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_profilefieldlistvalue_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_profilefieldlistvalue_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}

	function _NB_TERMS() {
		JToolBarHelper::title(JText::_('NB_TERMSMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_terms_new', 'new.png', 'new_f2.png', 'NB_NEW', false);
		JToolBarHelper::custom('ninjaboard_terms_edit', 'edit.png', 'edit_f2.png', 'NB_EDIT', false);
		JToolBarHelper::custom('ninjaboard_terms_delete', 'delete.png', 'delete_f2.png', 'NB_DELETE', false);
	}
	
	function _NB_TERMS_EDIT() {
		$cid = JRequest::getVar('cid', array(0) );
		$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
			
		JToolBarHelper::title(JText::_('NB_TERMSMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_terms_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
		JToolBarHelper::custom('ninjaboard_terms_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);		
		JToolBarHelper::custom('ninjaboard_terms_cancel', 'cancel.png', 'cancel_f2.png', 'NB_CANCEL', false);
	}
												
	function _NB_USERSYNC() {
		JToolBarHelper::title(JText::_('NB_USERSYNCMANAGER'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
		JToolBarHelper::custom('ninjaboard_usersync_perform', 'apply.png', 'apply_f2.png', 'NB_PERFORM', false);
	}
		
	function _NB_CREDITS() {
		JToolBarHelper::title(JText::_('NB_NBCREDITS'), 'ninjahdr');
		JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
	}
	
	function _DEFAULT() {
		//JToolBarHelper::title(JText::_('NB_NB'), 'ninjahdr');
		//JToolBarHelper::help('screen.content');
	}
}
?>