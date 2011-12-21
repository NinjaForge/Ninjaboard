<?php
/*
* @version		1.0.7
* @package		mod_ninjaboard_related_posts
* @author 		NinjaForge
* @author email	support@ninjaforge.com
* @link			http://ninjaforge.com
* @license      http://www.gnu.org/copyleft/gpl.html GNU GPL
* @copyright	Copyright (C) 2010 NinjaForge - All rights reserved.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
// Check if Nooku Framework (codename= Koowa) is active
/*if(!defined('KOOWA')){
	JError::raiseWarning(0, JText::_("Koowa wasn't found. Please install the Koowa plugnin and enable it."));
	return;
}*/
//KTemplate::loadHelper('script', KRequest::root().'/media/com_ninja/js/jquery/jquery.min.js');
//KTemplate::loadHelper('script', KRequest::root().'/media/com_ninja/js/jquery/jquery.tools.min.js');
require_once( dirname(__FILE__).'/helper.php' );
$document =& JFactory::getDocument();
$document->addStyleSheet( JURI::base(true).'/modules/mod_ninjaboard_related_posts/css/mod_ninjaboard_related_posts.css', 'text/css' );
$document->addScript(JURI::base(true).'/media/com_ninja/js/jquery/jquery.min.js', 'text/javascript' );
$document->addScript(JURI::base(true).'/media/com_ninja/js/jquery/jquery.tools.min.js', 'text/javascript' );
//Load module parameters
$layout = $params->get('layout', 'default');
$height = $params->get('height', 200);
$width = $params->get('width',200);
$num_cols = $params->get('num_cols', 1);
$num_posts = $params->get('num_posts', 1);
//safety measures for bad user input
//set $num_cols to at least one so we don't recieve div by zero error
if($num_cols <= 0){$num_cols = 1;}
//set $num_posts to at least one so we don't return empty results
if($num_posts <= 0){$num_posts = 1;}
//set $num_cols equal to $num_posts so we don't recieve results less than one
if($num_cols > $num_posts){$num_cols = $num_posts;}
$module_id = $module->id;
//get list items
$items = modNinjaBoardRelatedHelper::getNinjaBoardPosts($params);
//if no results return
//if (!count($items)) return;
//get template
require( JModuleHelper::getLayoutPath( 'mod_ninjaboard_related_posts', $layout ) );
?>
