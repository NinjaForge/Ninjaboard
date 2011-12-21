<?php defined('_JEXEC') or die('Restricted access');
/**
 * @version $Id: ninjaboard.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */


# Will delete this rubbish throughout the whole project!
define('DL', '/'); 
# Unecessary string concatenations.

/**
 * Ninjaboard
 *
 * @package Ninjaboard
 */
define('NB_BASEPATH_LIVE',	JURI::root().'components/com_ninjaboard');
define('NB_BUTTONS_LIVE',	NB_BASEPATH_LIVE.'/designs/buttons');
define('NB_CAPTCHAS_LIVE',	NB_BASEPATH_LIVE.'/designs/captchas');
define('NB_ICONS_LIVE',		NB_BASEPATH_LIVE.'/designs/icons');
define('NB_EMOTICONS_LIVE',	NB_BASEPATH_LIVE.'/designs/emoticons');
define('NB_STYLES_LIVE',	NB_BASEPATH_LIVE.'/designs/styles');
define('NB_TEMPLATES_LIVE',	NB_BASEPATH_LIVE.'/designs/templates');

define('NB_BASEPATH',		JPATH_COMPONENT);
define('NB_BUTTONS',		NB_BASEPATH.DS.'designs'.DS.'buttons');
define('NB_CAPTCHAS',		NB_BASEPATH.DS.'designs'.DS.'captchas');
define('NB_ICONS',			NB_BASEPATH.DS.'designs'.DS.'icons');
define('NB_EMOTICONS',		NB_BASEPATH.DS.'designs'.DS.'emoticons');
define('NB_STYLES',			NB_BASEPATH.DS.'designs'.DS.'styles');
define('NB_TEMPLATES',		NB_BASEPATH.DS.'designs'.DS.'templates');

define('NB_MEDIA',			JPATH_SITE.DS.'media'.DS.'ninjaboard');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

// Load the error logging helper
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'syslog.php' );
//load our debug helper firephp
require_once(NB_BASEPATH.DS.'helpers'.DS.'FirePHP.class.php');


require_once(NB_BASEPATH.DS.'controller'.DS.'ninjaboard.php');
require_once(NB_BASEPATH.DS.'helpers'.DS.'ninjaboard.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.auth.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.breadcrumbs.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.buttonset.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.engine.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.config.php');
//require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.editor.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.iconset.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.messagequeue.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.model.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.emoticonset.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.user.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.avatar.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.post.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.rank.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.mail.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.html.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.gd.php');
require_once(NB_BASEPATH.DS.'system'.DS.'ninjaboard.feed.php');

// component helper
jimport('joomla.application.component.helper');

$document =& JFactory::getDocument();

// create the controller
$controller = new NinjaboardController();
$controller->registerTask('ninjaboardpreviewtopic',		'ninjaboardPreview');
$controller->registerTask('ninjaboardpreviewpost',		'ninjaboardPreview');
$controller->registerTask('ninjaboardsavetopic',		'ninjaboardSaveTopic');
$controller->registerTask('ninjaboardsavepost',			'ninjaboardSavePost');
$controller->registerTask('ninjaboardlogin',			'ninjaboardLogin');
$controller->registerTask('ninjaboardlogout',			'ninjaboardLogout');
$controller->registerTask('ninjaboardactivateprofile',	'ninjaboardActivateProfile');
$controller->registerTask('ninjaboardsaveprofile',		'ninjaboardSaveProfile');
$controller->registerTask('ninjaboardregisterprofile',	'ninjaboardRegisterProfile');
$controller->registerTask('ninjaboarddeletetopic',		'ninjaboardDeleteTopic');
$controller->registerTask('ninjaboarddeletepost',		'ninjaboardDeletePost');
$controller->registerTask('ninjaboardrequestlogin',		'ninjaboardRequestLogin');
$controller->registerTask('ninjaboardresetlogin',		'ninjaboardResetLogin');
$controller->registerTask('ninjaboardreportpost',		'ninjaboardReportPost');
$controller->registerTask('ninjaboardlocktopic',		'ninjaboardLockTopic');
$controller->registerTask('ninjaboardunlocktopic',		'ninjaboardUnlockTopic');
$controller->registerTask('ninjaboardmovetopic',		'ninjaboardMoveTopic');
$controller->registerTask('ninjaboardfeed',				'ninjaboardFeed');

$ninjaboardEngine =& NinjaboardEngine::getInstance();
$ninjaboardEngine->performSession();

// perform the request task
$controller->execute(JRequest::getCmd('task'));

// redirect if set by the controller
$controller->redirect();
