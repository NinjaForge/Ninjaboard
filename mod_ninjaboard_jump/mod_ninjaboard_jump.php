<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninjaboard
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::load('site::mod.ninjaboard_jump.html');

KFactory::get('site::mod.ninjaboard_jump.html')->assign(array(
	'params'  => $params,
	'module'  => $module
))->setLayout($params->get('layout', 'default'))->display();