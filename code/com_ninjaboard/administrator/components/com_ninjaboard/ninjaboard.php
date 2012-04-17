<?php
 /**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
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

//@TODO find out if it's really necessary to map this anymore
KService::setAlias('com://admin/ninjaboard.template.helper.default', 'ninja:template.helper.document');

// Uncomment for localhost ajax testing of request spinners and response messages
// As it'll delay the request making it closer to the end-user experience
//if(KRequest::type() == 'AJAX') sleep(3);

//Load the ninjaboard plugins
JPluginHelper::importPlugin('ninjaboard', null, true, KService::get('koowa:event.dispatcher'));

//Debug RTL
if(JFactory::getLanguage()->isRTL() && KRequest::has('get.debug.rtl')) {
	JFactory::getDocument()->addScriptDeclaration("
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
	
	JFactory::getDocument()->addStyleDeclaration("
		html.rtl {
			-webkit-transform: rotateY(180deg);
			direction: rtl !important; 
			unicode-bidi: bidi-override !important;
		}
	");
}

// Create the component dispatcher
echo KService::get('com://admin/ninjaboard.dispatcher')->dispatch();

// Add untranslated words to the current NB language file
KService::get('ninja:helper.language');