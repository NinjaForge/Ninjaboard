<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninjaboard
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::load('admin::mod.ninjaboard_stats.html');

KFactory::get('admin::mod.ninjaboard_stats.html')->assign(array(
	'params'  => $params,
	'module'  => $module,
	'attribs' => $attribs
))->setLayout($params->get('layout', 'popular_topics'))->display();