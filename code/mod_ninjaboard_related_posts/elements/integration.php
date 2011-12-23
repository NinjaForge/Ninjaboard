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
class JElementIntegration extends JElement
{
   var   $_name = 'Integration';
   function fetchElement($name, $value, &$node, $control_name)
   {
		if (file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')){
			$options =array();
			$options[] = JHTML::_('select.option', 'nb', 'Ninjaboard', 'id', 'title');
			if (file_exists(JPATH_SITE.'/components/com_community/community.php')){
				$options[] = JHTML::_('select.option', 'js', 'JomSocial', 'id', 'title');
			}
			if (file_exists(JPATH_SITE.'/components/com_comprofiler/comprofiler.php')){
				$options[] = JHTML::_('select.option', 'cb', 'Community Builder', 'id', 'title');
			}		
			if (file_exists(JPATH_SITE.'/components/com_cbe/cbe.php')){
				$options[] = JHTML::_('select.option', 'cbe', 'Community Builder Enhanced', 'id', 'title');
			}
		  return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]',  '" class="inputbox"', 'id', 'title', $value, $control_name.$name);
	   } else {
		   return JText::_('Ninjaboard is not Installed!');
	   }
   }
}