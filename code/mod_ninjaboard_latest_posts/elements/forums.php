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

class JElementForums extends JElement



{



   var   $_name = 'Forums';



   function fetchElement($name, $value, &$node, $control_name)



   {



		if (file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')){



			$size = ( $node->attributes('size') ? $node->attributes('size') : 5 );

			

			$db =& JFactory::getDBO();

			

			$query = 'SELECT ninjaboard_forum_id AS id, title FROM #__ninjaboard_forums WHERE enabled = 1 ORDER BY title';

			

			$db->setQuery($query);

			

			$options = $db->loadObjectList();

			

			return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]',  ' multiple="multiple" size="' . $size .'" class="inputbox"', 'id', 'title', $value, $control_name.$name);



	   } else {



		   return JText::_('Ninjaboard is not Installed!');



	   }



   }



}



