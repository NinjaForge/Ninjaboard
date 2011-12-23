<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
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
if( !KLoader::path('site::com.ninjaboard.dispatcher') ) return;

//Initialize the dispatcher just so models are mapped, and everything else Ninjaboard needs to run
KLoader::load('site::com.ninjaboard.dispatcher');
ComNinjaboardDispatcher::register();

/**
 * Renders latest posts with the same look and feel like the regular component list
 *
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */


echo KFactory::tmp('site::mod.ninjaboard_posts.html', array(
	'params'  => $params,
	'module'  => $module,
	'attribs' => $attribs
))->display();