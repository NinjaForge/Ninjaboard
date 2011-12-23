<?php

/*

* @version		1.0.2

* @package		mod_ninjaboard_latest_posts

* @author 		NinjaForge

* @author email	support@ninjaforge.com

* @link			http://ninjaforge.com

* @license      http://www.gnu.org/copyleft/gpl.html GNU GPL

* @copyright	Copyright (C) 2010 NinjaForge - All rights reserved.

*/

defined( '_JEXEC' ) or die( 'Restricted access' );
defined( 'KOOWA' ) or die( 'Restricted access' );

//KTemplate::loadHelper('script', KRequest::root().'/media/com_ninja/js/jquery/jquery.min.js');

//KTemplate::loadHelper('script', KRequest::root().'/media/com_ninja/js/jquery/jquery.tools.min.js');

//$document->addStyleSheet( JURI::base(true).'/modules/mod_ninjaboard_latest_posts/css/mod_ninjaboard_latest_posts.css', 'text/css' );

//$document->addScript(JURI::base(true).'/media/com_ninja/js/jquery/jquery.min.js', 'text/javascript' );

//$document->addScript(JURI::base(true).'/media/com_ninja/js/jquery/jquery.tools.min.js', 'text/javascript' );

//Load module parameters
$layout = $params->get('layout', 'default');
$height = str_replace('px', '', $params->get('height', 200));
$width = str_replace('px', '', $params->get('width',200));
$num_cols = $params->get('num_cols', 1);
$num_posts = $params->get('num_posts', 1);

//safety measures for bad user input
//set $num_cols to at least one so we don't recieve div by zero error
if($num_cols <= 0){$num_cols = 1;}
//set $num_posts to at least one so we don't return empty results
if($num_posts <= 0){$num_posts = 1;}
//set $num_cols equal to $num_posts so we don't recieve results less than one
if($num_cols > $num_posts){$num_cols = $num_posts;}

KLoader::load('site::mod.ninjaboard_latest_posts.html');
KFactory::get('site::mod.ninjaboard_latest_posts.html', array(

	'params'  => $params,
	'module'  => $module,
	'attribs' => $attribs

))->display();

?>