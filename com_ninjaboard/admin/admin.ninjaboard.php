<?php
/**
 * @version $Id: admin.ninjaboard.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

// uwalter: Will delete this rubbish trhouout the whole component.
define('DL', '/');
// Unnecessary string concatenations!

// Backend
define('NB_ADMINBASEPATH_LIVE', JURI::base().'components/com_ninjaboard');
define('NB_ADMINCSS_LIVE',      NB_ADMINBASEPATH_LIVE.'/css');
define('NB_ADMINIMAGES_LIVE',   NB_ADMINBASEPATH_LIVE.'/images');

define('NB_ADMINBASEPATH',    JPATH_COMPONENT_ADMINISTRATOR);
define('NB_ADMINCONTROLLERS', NB_ADMINBASEPATH.DS.'controllers');
define('NB_ADMINPARAMS',      NB_ADMINBASEPATH.DS.'params');
define('NB_ADMINTABLES',      NB_ADMINBASEPATH.DS.'tables');

// Load the error logging helper
require_once( JPATH_COMPONENT_SITE.DS.'helpers'.DS.'syslog.php' );

// Frontend
define('NB_BASEPATH_LIVE',  JURI::root().'components/com_ninjaboard');
define('NB_BUTTONS_LIVE',   NB_BASEPATH_LIVE.'/designs/buttons');
define('NB_ICONS_LIVE',     NB_BASEPATH_LIVE.'/designs/icons');
define('NB_EMOTICONS_LIVE', NB_BASEPATH_LIVE.'/designs/emoticons');
define('NB_STYLES_LIVE',    NB_BASEPATH_LIVE.'/designs/styles');
define('NB_TEMPLATES_LIVE', NB_BASEPATH_LIVE.'/designs/templates');

define('NB_BASEPATH',  JPATH_COMPONENT_SITE);
define('NB_BUTTONS',   NB_BASEPATH.DS.'designs'.DS.'buttons');
define('NB_ICONS',     NB_BASEPATH.DS.'designs'.DS.'icons');
define('NB_EMOTICONS', NB_BASEPATH.DS.'designs'.DS.'emoticons');
define('NB_STYLES',	   NB_BASEPATH.DS.'designs'.DS.'styles');
define('NB_TEMPLATES', NB_BASEPATH.DS.'designs'.DS.'templates');

JTable::addIncludePath(NB_ADMINTABLES);
jimport('joomla.application.component.controller');

// JooBB controllers
require_once(NB_ADMINCONTROLLERS.DS.'forum.php');
require_once(NB_ADMINCONTROLLERS.DS.'category.php');
require_once(NB_ADMINCONTROLLERS.DS.'config.php');
require_once(NB_ADMINCONTROLLERS.DS.'timezone.php');
require_once(NB_ADMINCONTROLLERS.DS.'timeformat.php');
require_once(NB_ADMINCONTROLLERS.DS.'design.php');
require_once(NB_ADMINCONTROLLERS.DS.'user.php');
require_once(NB_ADMINCONTROLLERS.DS.'group.php');
require_once(NB_ADMINCONTROLLERS.DS.'rank.php');
require_once(NB_ADMINCONTROLLERS.DS.'profilefield.php');
require_once(NB_ADMINCONTROLLERS.DS.'profilefieldset.php');
require_once(NB_ADMINCONTROLLERS.DS.'profilefieldlist.php');
require_once(NB_ADMINCONTROLLERS.DS.'profilefieldlistvalue.php');
require_once(NB_ADMINCONTROLLERS.DS.'usersync.php');
require_once(NB_ADMINCONTROLLERS.DS.'terms.php');

// Ninjaboard system
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.auth.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.breadcrumbs.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.buttonset.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.engine.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.config.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.editor.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.iconset.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.messagequeue.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.emoticonset.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.user.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.avatar.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.gd.php');

// Ninjaboard helper
require_once(NB_BASEPATH.DS.'helpers'.DS.'ninjaboard.php');

// XML library
require_once(JPATH_SITE.DS.'includes'.DS.'domit'.DS.'xml_domit_lite_include.php');

$task	= JRequest::getVar('task');

switch ($task) {
	case 'ninjaboard_forum_view':
		ControllerForum::showForums();
		break;
	case 'ninjaboard_forum_new':
	case 'ninjaboard_forum_edit':
		ControllerForum::editForum();
		break;
	case 'ninjaboard_forum_save':	
	case 'ninjaboard_forum_apply':
		ControllerForum::saveForum($task);
		break;
	case 'ninjaboard_forum_delete':
		ControllerForum::deleteForum();
		break;				
	case 'ninjaboard_forum_cancel':
		ControllerForum::cancelEditForum();
		break;
	case 'ninjaboard_forum_orderup':
		ControllerForum::orderForum(-1);
		break;
	case 'ninjaboard_forum_orderdown':
		ControllerForum::orderForum(1);
		break;						
	case 'ninjaboard_forum_saveorder':
		ControllerForum::saveForumOrder();
		break;
	case 'ninjaboard_forum_publish':
		ControllerForum::changeForumPublishState(1);
		break;
	case 'ninjaboard_forum_unpublish':
		ControllerForum::changeForumPublishState(0);
		break;		
		
	/*case 'ninjaboard_category_view':
		ControllerCategory::showCategories();
		break;	
	case 'ninjaboard_category_new':
	case 'ninjaboard_category_edit':
		ControllerCategory::editCategory();
		break;
	case 'ninjaboard_category_save':	
	case 'ninjaboard_category_apply':
		ControllerCategory::saveCategory($task);
		break;
	case 'ninjaboard_category_delete':
		ControllerCategory::deleteCategory();
		break;				
	case 'ninjaboard_category_cancel':
		ControllerCategory::cancelEditCategory();
		break;*/
	case 'ninjaboard_category_orderup':
		NinjaboardControllerCategory::orderCategory(-1);
		break;
	case 'ninjaboard_category_orderdown':
		NinjaboardControllerCategory::orderCategory(1);
		break;
	case 'ninjaboard_category_saveorder':
		NinjaboardControllerCategory::saveCategoryOrder();
		break;
	/*case 'ninjaboard_category_publish':
		ControllerCategory::changeCategoryPublishState(1);
		break;
	case 'ninjaboard_category_unpublish':
		ControllerCategory::changeCategoryPublishState(0);
		break;	*/	
				
	case 'ninjaboard_config_view':
		ControllerConfig::showConfigs();
		break;
	case 'ninjaboard_config_new':
	case 'ninjaboard_config_edit':
		ControllerConfig::editConfig();
		break;
	case 'ninjaboard_config_save':	
	case 'ninjaboard_config_apply':
		ControllerConfig::saveConfig($task);
		break;
	case 'ninjaboard_config_delete':
		ControllerConfig::deleteConfig();
		break;				
	case 'ninjaboard_config_cancel':
		ControllerConfig::cancelEditConfig();
		break;
	case 'ninjaboard_config_default':
		ControllerConfig::defaultConfig();
		break;
				
	case 'ninjaboard_timezone_view':
		ControllerTimeZone::showTimeZones();
		break;
	case 'ninjaboard_timezone_new':
	case 'ninjaboard_timezone_edit':
		ControllerTimeZone::editTimeZone();
		break;
	case 'ninjaboard_timezone_save':	
	case 'ninjaboard_timezone_apply':
		ControllerTimeZone::saveTimeZone($task);
		break;
	case 'ninjaboard_timezone_delete':
		ControllerTimeZone::deleteTimeZone();
		break;				
	case 'ninjaboard_timezone_cancel':
		ControllerTimeZone::cancelEditTimeZone();
		break;
	case 'ninjaboard_timezone_default':
		ControllerTimeZone::defaultTimeZone();
		break;
	case 'ninjaboard_timezone_orderup' :
		ControllerTimeZone::orderTimeZone(-1);
		break;
	case 'ninjaboard_timezone_orderdown' :
		ControllerTimeZone::orderTimeZone(1);
		break;
	case 'ninjaboard_timezone_saveorder' :
		ControllerTimeZone::saveTimeZoneOrder();
		break;		
	case 'ninjaboard_timezone_publish' :
		ControllerTimeZone::changeTimeZonePublishState(1);
		break;
	case 'ninjaboard_timezone_unpublish' :
		ControllerTimeZone::changeTimeZonePublishState(0);
		break;
				
	case 'ninjaboard_timeformat_view':
		ControllerTimeFormat::showTimeFormats();
		break;
	case 'ninjaboard_timeformat_new' :
	case 'ninjaboard_timeformat_edit' :
		ControllerTimeFormat::editTimeFormat();
		break;
	case 'ninjaboard_timeformat_save' :	
	case 'ninjaboard_timeformat_apply' :
		ControllerTimeFormat::saveTimeFormat($task);
		break;
	case 'ninjaboard_timeformat_delete' :
		ControllerTimeFormat::deleteTimeFormat();
		break;				
	case 'ninjaboard_timeformat_cancel' :
		ControllerTimeFormat::cancelEditTimeFormat();
		break;
	case 'ninjaboard_timeformat_default' :
		ControllerTimeFormat::defaultTimeFormat();
		break;		
	case 'ninjaboard_timeformat_publish' :
		ControllerTimeFormat::changeTimeFormatPublishState(1);
		break;
	case 'ninjaboard_timeformat_unpublish' :
		ControllerTimeFormat::changeTimeFormatPublishState(0);
		break;
			
	case 'ninjaboard_design_view' :
		ControllerDesign::showDesigns();
		break;	
	case 'ninjaboard_design_new' :
	case 'ninjaboard_design_edit' :
		ControllerDesign::editDesign();
		break;
	case 'ninjaboard_design_save' :	
	case 'ninjaboard_design_apply' :
		ControllerDesign::saveDesign($task);
		break;
	case 'ninjaboard_design_delete' :
		ControllerDesign::deleteDesign();
		break;				
	case 'ninjaboard_design_cancel' :
		ControllerDesign::cancelEditDesign();
		break;
	case 'ninjaboard_design_default' :
		ControllerDesign::defaultDesign();
		break;								

	case 'ninjaboard_user_view':
		ControllerUser::showUsers();
		break;
	case 'ninjaboard_user_new':
		ControllerUser::editUser();
		break;
	case 'ninjaboard_user_edit':
		ControllerUser::editUser();
		break;
	case 'ninjaboard_user_save':	
	case 'ninjaboard_user_apply':
		ControllerUser::saveUser();
		break;
	case 'ninjaboard_user_delete':
		ControllerUser::deleteUser();
		break;		
	case 'ninjaboard_user_cancel':
		ControllerUser::cancelEditUser();
		break;
	case 'ninjaboard_user_block':
		ControllerUser::changeUserBlock(1);
		break;
	case 'ninjaboard_user_unblock':
		ControllerUser::changeUserBlock(0);
		break;
	case 'ninjaboard_user_logout':
		ControllerUser::logoutUser();
		break;
		
	case 'ninjaboard_group_view':
		ControllerGroup::showGroups();
		break;
	case 'ninjaboard_group_new':
		ControllerGroup::editGroup();
		break;
	case 'ninjaboard_group_edit':
		ControllerGroup::editGroup();
		break;		
	case 'ninjaboard_group_save':	
	case 'ninjaboard_group_apply':
		ControllerGroup::saveGroup();
		break;
	case 'ninjaboard_group_delete':
		ControllerGroup::deleteGroup();
		break;				
	case 'ninjaboard_group_cancel':
		ControllerGroup::cancelEditGroup();
		break;
	case 'ninjaboard_group_publish':
		ControllerGroup::changeGroupPublishState(1);
		break;
	case 'ninjaboard_group_unpublish':
		ControllerGroup::changeGroupPublishState(0);
		break;
		
	case 'ninjaboard_rank_view':
		ControllerRank::showRanks();
		break;
	case 'ninjaboard_rank_new':
		ControllerRank::editRank();
		break;
	case 'ninjaboard_rank_edit':
		ControllerRank::editRank();
		break;		
	case 'ninjaboard_rank_save':	
	case 'ninjaboard_rank_apply':
		ControllerRank::saveRank($task);
		break;		
	case 'ninjaboard_rank_cancel':
		ControllerRank::cancelEditRank();
		break;
	case 'ninjaboard_rank_publish':
		ControllerRank::changeRankPublishState(1);
		break;
	case 'ninjaboard_rank_unpublish':
		ControllerRank::changeRankPublishState(0);
		break;

	case 'ninjaboard_profilefieldset_view':
		ControllerProfileFieldSet::showProfileFieldSets();
		break;	
	case 'ninjaboard_profilefieldset_new':
	case 'ninjaboard_profilefieldset_edit':
		ControllerProfileFieldSet::editProfileFieldSet();
		break;
	case 'ninjaboard_profilefieldset_save':	
	case 'ninjaboard_profilefieldset_apply':
		ControllerProfileFieldSet::saveProfileFieldSet($task);
		break;
	case 'ninjaboard_profilefieldset_delete':
		ControllerProfileFieldSet::deleteProfileFieldSet();
		break;				
	case 'ninjaboard_profilefieldset_cancel':
		ControllerProfileFieldSet::cancelEditProfileFieldSet();
		break;
	case 'ninjaboard_profilefieldset_orderup' :
		ControllerProfileFieldSet::orderProfileFieldSet(-1);
		break;
	case 'ninjaboard_profilefieldset_orderdown' :
		ControllerProfileFieldSet::orderProfileFieldSet(1);
		break;
	case 'ninjaboard_profilefieldset_saveorder':
		ControllerProfileFieldSet::saveProfileFieldSetOrder();
		break;
	case 'ninjaboard_profilefieldset_publish':
		ControllerProfileFieldSet::changeProfileFieldSetPublishState(1);
		break;
	case 'ninjaboard_profilefieldset_unpublish':
		ControllerProfileFieldSet::changeProfileFieldSetPublishState(0);
		break;
		
	case 'ninjaboard_profilefield_view':
		ControllerProfileField::showProfileFields();
		break;	
	case 'ninjaboard_profilefield_new':
	case 'ninjaboard_profilefield_edit':
		ControllerProfileField::editProfileField();
		break;
	case 'ninjaboard_profilefield_save':	
	case 'ninjaboard_profilefield_apply':
		ControllerProfileField::saveProfileField($task);
		break;
	case 'ninjaboard_profilefield_delete':
		ControllerProfileField::deleteProfileField();
		break;				
	case 'ninjaboard_profilefield_cancel':
		ControllerProfileField::cancelEditProfileField();
		break;
	case 'ninjaboard_profilefield_orderup' :
		ControllerProfileField::orderProfileField(-1);
		break;
	case 'ninjaboard_profilefield_orderdown' :
		ControllerProfileField::orderProfileField(1);
		break;
	case 'ninjaboard_profilefield_saveorder':
		ControllerProfileField::saveProfileFieldOrder();
		break;
	case 'ninjaboard_profilefield_publish':
		ControllerProfileField::changeProfileFieldPublishState(1);
		break;
	case 'ninjaboard_profilefield_unpublish':
		ControllerProfileField::changeProfileFieldPublishState(0);
		break;
		
	case 'ninjaboard_profilefieldlist_view':
		ControllerProfileFieldList::showProfileFieldLists();
		break;	
	case 'ninjaboard_profilefieldlist_new':
	case 'ninjaboard_profilefieldlist_edit':
		ControllerProfileFieldList::editProfileFieldList();
		break;
	case 'ninjaboard_profilefieldlist_save':	
	case 'ninjaboard_profilefieldlist_apply':
		ControllerProfileFieldList::saveProfileFieldList($task);
		break;
	case 'ninjaboard_profilefieldlist_delete':
		ControllerProfileFieldList::deleteProfileFieldList();
		break;				
	case 'ninjaboard_profilefieldlist_cancel':
		ControllerProfileFieldList::cancelEditProfileFieldList();
		break;
	case 'ninjaboard_profilefieldlist_publish':
		ControllerProfileFieldList::changeProfileFieldListPublishState(1);
		break;
	case 'ninjaboard_profilefieldlist_unpublish':
		ControllerProfileFieldList::changeProfileFieldListPublishState(0);
		break;
		
	case 'ninjaboard_profilefieldlistvalue_view':
		ControllerProfileFieldListValue::showProfileFieldListValues();
		break;	
	case 'ninjaboard_profilefieldlistvalue_new':
	case 'ninjaboard_profilefieldlistvalue_edit':
		ControllerProfileFieldListValue::editProfileFieldListValue();
		break;
	case 'ninjaboard_profilefieldlistvalue_save':	
	case 'ninjaboard_profilefieldlistvalue_apply':
		ControllerProfileFieldListValue::saveProfileFieldListValue($task);
		break;
	case 'ninjaboard_profilefieldlistvalue_delete':
		ControllerProfileFieldListValue::deleteProfileFieldListValue();
		break;				
	case 'ninjaboard_profilefieldlistvalue_cancel':
		ControllerProfileFieldListValue::cancelEditProfileFieldListValue();
		break;
	case 'ninjaboard_profilefieldlistvalue_orderup' :
		ControllerProfileFieldListValue::orderProfileFieldListValue(-1);
		break;
	case 'ninjaboard_profilefieldlistvalue_orderdown' :
		ControllerProfileFieldListValue::orderProfileFieldListValue(1);
		break;
	case 'ninjaboard_profilefieldlistvalue_saveorder':
		ControllerProfileFieldListValue::saveProfileFieldListValueOrder();
		break;
	case 'ninjaboard_profilefieldlistvalue_publish':
		ControllerProfileFieldListValue::publishedProfileFieldListValue(1);
		break;
	case 'ninjaboard_profilefieldlistvalue_unpublish':
		ControllerProfileFieldListValue::publishedProfileFieldListValue(0);
		break;
		
	case 'ninjaboard_terms_view':
		ControllerTerms::showTerms();
		break;
	case 'ninjaboard_terms_new':
	case 'ninjaboard_terms_edit':
		ControllerTerms::editTerms();
		break;
	case 'ninjaboard_terms_save':	
	case 'ninjaboard_terms_apply':
		ControllerTerms::saveTerms($task);
		break;
	case 'ninjaboard_terms_delete':
		ControllerTerms::deleteTerms();
		break;				
	case 'ninjaboard_terms_cancel':
		ControllerTerms::cancelEditTerms();
		break;
	case 'ninjaboard_terms_publish':
		ControllerTerms::changeTermsPublishState(1);
		break;
	case 'ninjaboard_terms_unpublish':
		ControllerTerms::changeTermsPublishState(0);
		break;
						
	case 'ninjaboard_usersync_view':
		ControllerUserSync::showUserSync();
		break;
	case 'ninjaboard_usersync_perform':
		ControllerUserSync::performUserSync();
		break;

	
	default:
		//Default action is to display the dashboard
		$controller = JRequest::getCmd('controller', 'dashboard');

		//a little bit of dirty code to direct all file related tasks to the files controller
		switch ($task) {
			case 'ninjaboard_controlpanel':
				$controller = 'dashboard';
				break;
			case 'removebuttonset':
			case 'removeiconset':
			case 'removestyle':
			case 'removetemplate':
			case 'removeemoticonset':
				$controller = 'files';
		}
		

		require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
		$classname	= 'NinjaBoardController'.$controller;
				
		// Create the controller
		$controller = new $classname();
		$old_err = error_reporting(E_ALL ^ E_NOTICE);
		
		$controller->registerTask('uploadbuttonset', 'saveuploadedfiles');
		$controller->registerTask('removebuttonset', 'remove');
		$controller->registerTask('uploadiconset', 'saveuploadedfiles');
		$controller->registerTask('removeiconset', 'remove');
		$controller->registerTask('uploadstyle', 'saveuploadedfiles');
		$controller->registerTask('removestyle', 'remove');
		$controller->registerTask('uploadtemplate', 'saveuploadedfiles');
		$controller->registerTask('removetemplate', 'remove');
		$controller->registerTask('uploademoticonset', 'saveuploadedfiles');
		$controller->registerTask('removeemoticonset', 'remove');
		$controller->registerTask('unpublish', 'publish');
		$controller->registerTask('apply', 'save');
		
		// Perform the Request task
		$controller->execute( JRequest::getVar('task') );
		error_reporting($old_err);
		// Redirect if set by the controller
		$controller->redirect();	
	
		//Removed by Dan - Replace with Dashboard
		//ControllerNinjaboard::showControlPanel();
		//break;
}

?>
