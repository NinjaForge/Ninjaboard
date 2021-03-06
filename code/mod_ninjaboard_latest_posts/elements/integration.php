<?php
/**
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_latest_posts
 * @copyright	Copyright (C) 2010 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JElementIntegration Class - for displaying a list of available user profiles
 */
class JElementIntegration extends JElement
{
   var   $_name = 'Integration';

    /**
    * Method for building the element
    */
   function fetchElement($name, $value, &$node, $control_name)
   {
   		$options = array();

		if (file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')){
			$options[] = JHTML::_('select.option', 'nb', 'Ninjaboard', 'id', 'title');
			if (file_exists(JPATH_SITE.'/components/com_community/community.php'))
				$options[] = JHTML::_('select.option', 'js', 'JomSocial', 'id', 'title');

			if (file_exists(JPATH_SITE.'/components/com_comprofiler/comprofiler.php'))
				$options[] = JHTML::_('select.option', 'cb', 'Community Builder', 'id', 'title');

			if (file_exists(JPATH_SITE.'/components/com_cbe/cbe.php'))
				$options[] = JHTML::_('select.option', 'cbe', 'Community Builder Enhanced', 'id', 'title');

		  return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]',  '" class="inputbox"', 'id', 'title', $value, $control_name.$name);
	   } else {
		   return JText::_('MOD_NINJABOARD_LATEST_POSTS_NINJABOARD_NOT_INSTALLED');
	   }
   }
}