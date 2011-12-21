<?php
/* @version		1.0.0
 * @package		mod_ninjaboard_menu
 * @author 		Stephanie Scmidt
 * @author mail	admin@dwtutorials.com
 * @link		http://www.dwtutorials.com
 * @copyright	Copyright (C) 2009 Stephanie Scmidt - All rights reserved.
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// Check if Koowa is active
if(!defined('KOOWA')) {
	JError::raiseWarning(0, JText::_("Koowa wasn't found. Please install the Koowa plugin and enable it."));
	return;
}	
//KTemplate::loadHelper('script', KRequest::root().'/media/com_ninja/js/jquery/jquery.pack.js');

JHTML::stylesheet( @$params->get('layout').'.css', 'modules/mod_nbmenu/css/');
JHTML::script(  @$params->get('layout').'.js', 'modules/mod_nbmenu/js/' );


KFactory::get('admin::mod.nbmenu.html', array(
	'params'  => $params,
	'module'  => $module,
	'attribs' => $attribs
))->display();
?>