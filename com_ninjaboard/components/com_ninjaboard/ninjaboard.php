<?php
 /**
 * @version		$Id: ninjaboard.php 1412 2011-01-13 13:08:25Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
//@TODO add proper error handling when com_ninja don't exist
$ninja = JPATH_ADMINISTRATOR.'/components/com_ninja/ninja.php';
if(file_exists($ninja)) require_once $ninja;
else					return;

/** 
* If either of the frameworks is unavailable
* Abort the dispatcher
*/
if( !defined('KOOWA') ) return false;


// 0.5 legacy
if(KRequest::get('get.view', 'cmd') == 'board') 		KRequest::set('get.view', 'forums');
if(KRequest::get('get.layout', 'cmd') == 'ninjaboard')	unset($_GET['layout']);


// We like code reuse, so we map the frontend models to the backend models
foreach(array('avatars', 'forums', 'settings', 'users', 'usergroups', 'people', 'profiles', 'topics', 'posts', 'attachments', 'watches') as $model)
{
	KFactory::map('site::com.ninjaboard.model.'.$model, 'admin::com.ninjaboard.model.'.$model);
}

foreach(array('forum') as $row)
{
	KFactory::map('site::com.ninjaboard.database.row.'.$row, 'admin::com.ninjaboard.database.row.'.$row);
}

KFactory::map('site::com.ninjaboard.database.table.topics', 	'admin::com.ninjaboard.database.table.topics');
KFactory::map('site::com.ninjaboard.database.table.posts', 	'admin::com.ninjaboard.database.table.posts');
KFactory::map('site::com.ninjaboard.database.table.attachments', 	'admin::com.ninjaboard.database.table.attachments');
KFactory::map('site::com.ninjaboard.database.table.users', 	'admin::com.ninjaboard.database.table.users');
KFactory::map('site::com.ninjaboard.database.table.people', 	'admin::com.ninjaboard.database.table.people');
KFactory::map('site::com.ninjaboard.database.table.settings', 	'admin::com.ninjaboard.database.table.settings');
KFactory::map('site::com.ninjaboard.database.table.watches', 	'admin::com.ninjaboard.database.table.watches');

//@TODO temporary mappings
KFactory::map('site::com.ninjaboard.model.rules', 	'admin::com.ninjaboard.model.profile_fields');
KFactory::map('site::com.ninjaboard.model.helps', 	'admin::com.ninjaboard.model.profile_fields');

//Set napi to load jquery scripts instead of mootools
KFactory::get('admin::com.ninja.helper.default')->framework('jquery');

$document = KFactory::get('lib.joomla.document');
$config   = KFactory::get('lib.joomla.config');
$debug    = $config->getValue('config.debug');

//The following makes sure MooTools always loads first when needed and only loads jQuery if it isn't already
if(KFactory::get('lib.joomla.application')->getTemplate() != 'morph' && !JFactory::getApplication()->get('jquery')) {
	KFactory::get('admin::com.ninja.helper.default')->js('/jquery.min.js');
	
	//Set jQuery as loaded, used in template frameworks like Warp5
	JFactory::getApplication()->set('jquery', true);
}

//Load the ninjaboard plugins
JPluginHelper::importPlugin('ninjaboard', null, true, KFactory::get('lib.koowa.event.dispatcher'));

if($debug && false)
{
	try {
		//Init plug
		KFactory::get('plg.koowa.debug');
	} catch(KFactoryAdapterException $e) {}
		
	$profile = microtime(true);
}

//@TODO this is pretty dirty, we need to find a better solution to set the offset in our views that currently use KRequest for it
if(KRequest::has('get.limitstart'))
{
	KRequest::set('get.offset', KRequest::get('get.limitstart', 'int'));
}

// Create the component dispatcher
echo KFactory::get('site::com.ninjaboard.dispatcher')->dispatch(KRequest::get('get.view', 'cmd', 'forums'));

if($debug && false)
{
	try {
		//Init plug
		$queries = KFactory::get('plg.koowa.debug')->queries;
		$document->addScriptDeclaration('if(console) { console.group("Ninjaboard SQL queries ('.count($queries).')");'.PHP_EOL);
		foreach($queries AS $query)
		{
			$document->addScriptDeclaration('console.log('.json_encode((string)$query).');'.PHP_EOL);
		}
		$document->addScriptDeclaration('console.groupEnd(); }'.PHP_EOL);
	} catch(KFactoryAdapterException $e) {}
	
	$time = number_format(microtime(true) - $profile, 4);
	$document->addScriptDeclaration('if(console) console.log("Ninjaboard took '.$time.' seconds to render.");'.PHP_EOL);
}

// Add untranslated words to the current NB language file
KFactory::get('admin::com.ninja.helper.language');