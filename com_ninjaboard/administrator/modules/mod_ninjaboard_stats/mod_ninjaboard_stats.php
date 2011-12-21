<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: mod_ninjaboard_stats.php 2469 2011-11-01 14:09:17Z stian $
 * @package		Ninjaboard
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::loadIdentifier('admin::mod.ninjaboard_stats.html');

echo $this->getService('admin::mod.ninjaboard_stats.html')->assign(array(
	'params'  => $params,
	'module'  => $module,
	'attribs' => $attribs
))->setLayout($params->get('layout', 'popular_topics'))->display();