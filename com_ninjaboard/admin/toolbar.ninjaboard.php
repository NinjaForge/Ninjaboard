<?php
/**
 * @version $Id: toolbar.ninjaboard.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

/**
 * Ninjaboard Toolbar
 *
 * @package Ninjaboard
 */

switch ( $task ) {
	case 'ninjaboard_forum_view':
		TOOLBAR_ninjaboard::_NB_FORUM();
		break;		
	case 'ninjaboard_forum_new':
	case 'ninjaboard_forum_edit':
		TOOLBAR_ninjaboard::_NB_FORUM_EDIT();
		break;
		
	case 'ninjaboard_category_view':
		TOOLBAR_ninjaboard::_NB_CATEGORY();
		break;			
	case 'ninjaboard_category_new':
	case 'ninjaboard_category_edit':
		TOOLBAR_ninjaboard::_NB_CATEGORY_EDIT();
		break;
							
	case 'ninjaboard_config_view':
		TOOLBAR_ninjaboard::_NB_CONFIG();
		break;
	case 'ninjaboard_config_new':
	case 'ninjaboard_config_edit':
		TOOLBAR_ninjaboard::_NB_CONFIG_EDIT();
		break;
		
	case 'ninjaboard_timezone_view':
		TOOLBAR_ninjaboard::_NB_TIMEZONE();
		break;
	case 'ninjaboard_timezone_new':
	case 'ninjaboard_timezone_edit':
		TOOLBAR_ninjaboard::_NB_TIMEZONE_EDIT();
		break;
		
	case 'ninjaboard_timeformat_view':
		TOOLBAR_ninjaboard::_NB_TIMEFORMAT();
		break;
	case 'ninjaboard_timeformat_new':
	case 'ninjaboard_timeformat_edit':
		TOOLBAR_ninjaboard::_NB_TIMEFORMAT_EDIT();
		break;
						
	case 'ninjaboard_design_view':
		TOOLBAR_ninjaboard::_NB_DESIGN();
		break;
	case 'ninjaboard_design_new':
	case 'ninjaboard_design_edit':
		TOOLBAR_ninjaboard::_NB_DESIGN_EDIT();
		break;
				
					
	case 'ninjaboard_user_view':
		TOOLBAR_ninjaboard::_NB_USER();
		break;	
	case 'ninjaboard_user_new':
	case 'ninjaboard_user_edit':
		TOOLBAR_ninjaboard::_NB_USER_EDIT();
		break;
			
	case 'ninjaboard_group_view':
		TOOLBAR_ninjaboard::_NB_GROUP();
		break;
	case 'ninjaboard_group_new':
	case 'ninjaboard_group_edit':
		TOOLBAR_ninjaboard::_NB_GROUP_EDIT();
		break;		
			
	case 'ninjaboard_rank_view':
		TOOLBAR_ninjaboard::_NB_RANK();
		break;
	case 'ninjaboard_rank_new':
	case 'ninjaboard_rank_edit':
		TOOLBAR_ninjaboard::_NB_RANK_EDIT();
		break;		
			
	case 'ninjaboard_profilefieldset_view':
		TOOLBAR_ninjaboard::_NB_PROFILEFIELDSET();
		break;
	case 'ninjaboard_profilefieldset_new':
	case 'ninjaboard_profilefieldset_edit':
		TOOLBAR_ninjaboard::_NB_PROFILEFIELDSET_EDIT();
		break;		
			
	case 'ninjaboard_profilefield_view':
		TOOLBAR_ninjaboard::_NB_PROFILEFIELD();
		break;
	case 'ninjaboard_profilefield_new':
	case 'ninjaboard_profilefield_edit':
		TOOLBAR_ninjaboard::_NB_PROFILEFIELD_EDIT();
		break;		
			
	case 'ninjaboard_profilefieldlist_view':
		TOOLBAR_ninjaboard::_NB_PROFILEFIELDLIST();
		break;
	case 'ninjaboard_profilefieldlist_new':
	case 'ninjaboard_profilefieldlist_edit':
		TOOLBAR_ninjaboard::_NB_PROFILEFIELDLIST_EDIT();
		break;		
			
	case 'ninjaboard_profilefieldlistvalue_view':
		TOOLBAR_ninjaboard::_NB_PROFILEFIELDLISTVALUE();
		break;
	case 'ninjaboard_profilefieldlistvalue_new':
	case 'ninjaboard_profilefieldlistvalue_edit':
		TOOLBAR_ninjaboard::_NB_PROFILEFIELDLISTVALUE_EDIT();
		break;
			
	case 'ninjaboard_terms_view':
		TOOLBAR_ninjaboard::_NB_TERMS();
		break;
	case 'ninjaboard_terms_new':
	case 'ninjaboard_terms_edit':
		TOOLBAR_ninjaboard::_NB_TERMS_EDIT();
		break;		
							
	case 'ninjaboard_usersync_view':
		TOOLBAR_ninjaboard::_NB_USERSYNC();
		break;

	default:
		TOOLBAR_ninjaboard::_DEFAULT();
		break;
}
?>