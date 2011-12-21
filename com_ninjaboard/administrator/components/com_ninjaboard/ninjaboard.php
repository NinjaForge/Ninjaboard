<?php
 /**
 * @version		$Id: ninjaboard.php 1357 2011-01-10 18:45:58Z stian $
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

/* @TODO testing upgrade script
require_once JPATH_COMPONENT . '/install/upgrade.php';
//*/

/** 
* If koowa is unavailable
* Abort the dispatcher
*/
if( !defined('KOOWA') ) return;

KFactory::map('admin::com.ninjaboard.template.helper.default', 'admin::com.ninja.helper.default');

// Uncomment for localhost ajax testing of request spinners and response messages
// As it'll delay the request making it closer to the end-user experience
//if(KRequest::type() == 'AJAX') sleep(3);

//Load the ninjaboard plugins
JPluginHelper::importPlugin('ninjaboard', null, true, KFactory::get('lib.koowa.event.dispatcher'));

//Debug RTL
if(KFactory::get('lib.joomla.language')->isRTL() && KRequest::has('get.debug.rtl')) {
	KFactory::get('lib.joomla.document')->addScriptDeclaration("
		String.prototype.reverse = function(){
			return this.split('').reverse().join('');
		};
		
		window.addEventListener('blur', function(){
			document.html.removeClass('rtl');
		});
		
		window.addEventListener('focus', function(){
			document.html.addClass('rtl');
		});
	");	
	
	KFactory::get('lib.joomla.document')->addStyleDeclaration("
		html.rtl {
			-webkit-transform: rotateY(180deg);
			direction: rtl !important; 
			unicode-bidi: bidi-override !important;
		}
	");
}

// Create the component dispatcher
echo KFactory::get('admin::com.ninjaboard.dispatcher')->dispatch(KRequest::get('get.view', 'cmd', 'dashboard'));

// Add untranslated words to the current NB language file
KFactory::get('admin::com.ninja.helper.language');