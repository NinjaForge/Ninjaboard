<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version		$Id: mod_ninjaboard_quickpanel.php 2232 2011-07-18 09:57:55Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/** 
* If koowa is unavailable
* Abort the dispatcher
*/
if( !defined('KOOWA') ) return;

/** 
* If Ninjaboard is unavailable
* Abort the dispatcher
*/
if(!KLoader::path('site::com.ninjaboard.dispatcher') || !method_exists('ComNinjaboardDispatcher', 'register'))
{
    echo JText::_('Ninjaboard 1.1 or later required.');
    return;
}

//User needs to be logged in
if(KFactory::get('lib.joomla.user')->guest) return;

//Initialize the dispatcher just so models are mapped, and everything else Ninjaboard needs to run
ComNinjaboardDispatcher::register();

/**
 * Renders latest posts with the same look and feel like the regular component list
 *
 * @version		$Id: mod_ninjaboard_quickpanel.php 2232 2011-07-18 09:57:55Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

echo KFactory::tmp('site::mod.ninjaboard_quickpanel.html', array(
	'params'  => $params,
	'module'  => $module,
	'attribs' => $attribs
))->display();