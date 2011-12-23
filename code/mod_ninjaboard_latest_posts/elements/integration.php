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
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementIntegration extends JElement

{

   var   $_name = 'Integration';

   function fetchElement($name, $value, &$node, $control_name)

   {

		if (JFolder::exists(JPATH_SITE.'/components/com_ninjaboard/')){

			$options =array();

			$options[] = JHTML::_('select.option', 'nb', 'Ninjaboard', 'id', 'title');

			if (JFolder::exists(JPATH_SITE.'/components/com_community/')){

				$options[] = JHTML::_('select.option', 'js', 'JomSocial', 'id', 'title');

			}

			if (JFolder::exists(JPATH_SITE.'/components/com_comprofiler/')){

				$options[] = JHTML::_('select.option', 'cb', 'Community Builder', 'id', 'title');

			}		

			if (JFolder::exists(JPATH_SITE.'/components/com_cbe/')){

				$options[] = JHTML::_('select.option', 'cbe', 'Community Builder Enhanced', 'id', 'title');

			}

		  return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]',  '" class="inputbox"', 'id', 'title', $value, $control_name.$name);

	   } else {

		   return JText::_('Ninjaboard is not Installed!');

	   }

   }

}