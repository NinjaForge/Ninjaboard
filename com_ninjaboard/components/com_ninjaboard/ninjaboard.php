<?php
 /**
 * @version		$Id: ninjaboard.php 1666 2011-03-22 02:06:32Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
$ninja = JPATH_ADMINISTRATOR.'/components/com_ninja/ninja.php';
if(!file_exists($ninja)) {
	return JError::raiseWarning(0, JText::_('The Ninja Framework component (com_ninja) is required for this component to run.'));
}
require_once $ninja;

/** 
* If koowa is unavailable
* Abort the dispatcher
*/
if(!defined('KOOWA')) {
	return JError::raiseWarning(0, JText::_('This component cannot run without Nooku Framework.'));
}


// 0.5 legacy
if(KRequest::get('get.view', 'cmd') == 'board') 		KRequest::set('get.view', 'forums');
if(KRequest::get('get.layout', 'cmd') == 'ninjaboard')	unset($_GET['layout']);



$document = KFactory::get('lib.joomla.document');
$config   = KFactory::get('lib.joomla.config');
$debug    = $config->getValue('config.debug');

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