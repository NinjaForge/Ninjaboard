<?php
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
$ninja = JPATH_ADMINISTRATOR.'/components/com_ninja/ninja.php';
if(!file_exists($ninja)) {
	return JError::raiseWarning(0, JText::_('COM_NINJABOARD_THE_NINJA_FRAMEWORK_COMPONENT_COM_NINJA_IS_REQUIRED_FOR_THIS_COMPONENT_TO_RUN'));
}
require_once $ninja;

/** 
* If koowa is unavailable
* Abort the dispatcher
*/
if(!defined('KOOWA')) {
	return JError::raiseWarning(0, JText::_('COM_NINJABOARD_THIS_COMPONENT_CANNOT_RUN_WITHOUT_NOOKU_FRAMEWORK'));
}


// 0.5 legacy
if(KRequest::get('get.view', 'cmd') == 'board') 		KRequest::set('get.view', 'forums');
if(KRequest::get('get.layout', 'cmd') == 'ninjaboard')	unset($_GET['layout']);



$document = JFactory::getDocument();
$config   = JFactory::getConfig();
$debug    = $config->getValue('config.debug');

if($debug && false)
{
	try {
		//Init plug
		$this->getService('plg:koowa.debug');
	} catch(KFactoryAdapterException $e) {}
		
	$profile = microtime(true);
}

//@TODO this is pretty dirty, we need to find a better solution to set the offset in our views that currently use KRequest for it
if(KRequest::has('get.limitstart'))
{
	KRequest::set('get.offset', KRequest::get('get.limitstart', 'int'));
}

// Create the component dispatcher
echo KService::get('com://site/ninjaboard.dispatcher')->dispatch();

if($debug && false)
{
	try {
		//Init plug
		$queries = $this->getService('plg:koowa.debug')->queries;
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