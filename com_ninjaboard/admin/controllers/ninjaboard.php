<?php
/**
 * @version $Id: ninjaboard.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.html.pane');

require_once(JPATH_COMPONENT.DS.'views'.DS.'ninjaboard.php');

/**
 * Ninjaboard Controller
 *
 * @package Ninjaboard
 */
class ControllerNinjaboard extends JController
{
	/**
	 * shows the control panel
	 */
	function showControlPanel() {
		
		// initialize variables
		$db =& JFactory::getDBO();
		
		// get number of topics
		$query = "SELECT '". JText::_('NB_BOARDTOPICS') ."' AS description, SUM(topics) AS value" .
				 "\n FROM #__ninjaboard_forums"
				 ;
		$db->setQuery($query);	
		$rows = $db->loadObjectList();

		if (!is_array($rows)) {
			$object = new stdClass();
            $object->description    = JText::_('NB_BOARDTOPICS');
            $object->value          = JText::_('NB_NOTAVAILABLE');
			$rows[]                 = $object;
		}
						
		// get number of posts
		$query = "SELECT '". JText::_('NB_BOARDPOSTS') ."' AS description, SUM(posts) AS value" .
				 "\n FROM #__ninjaboard_forums"
				 ;
		$db->setQuery($query);
		$rusult = $db->loadObjectList();

		if (!is_array($rusult)) {
			$object = new stdClass();
            $object->description    = JText::_('NB_BOARDPOSTS');
            $object->value          = JText::_('NB_NOTAVAILABLE');
			$rusult[]               = $object;
		}
				
		$rows = array_merge($rows, $rusult);

		// get number of users
		$query = "SELECT  '". JText::_('NB_BOARDUSERS') ."' AS description , COUNT(*) AS value" .
				 "\n FROM #__users"
				 ;
		$db->setQuery($query);
		$rusult = $db->loadObjectList();

		if (!is_array($rusult)) {
			$object = new stdClass();
            $object->description    = JText::_('NB_BOARDUSERS');
            $object->value          = JText::_( 'NB_NOTAVAILABLE' );
			$rusult[]               = $object;
		}
					
		$rows = array_merge($rows, $rusult);
		
		// get forum size
		$query = "SHOW TABLE STATUS LIKE '%ninjaboard%'"
				 ;
		$db->setQuery($query);
		$tablerows = $db->loadObjectList();
		
		// get board size
		$size = 0;
		foreach($tablerows as $tablerow) {
			$size += $tablerow->Data_length;
		}
		
        if ( $size < 964 ) { 
			$size = round($size) ." Bytes"; 
		} else if ( $size < 1000000 ) { 
			$size = round( $size/1024,2 ) ." KB" ; 
		} else { 
			$size = round( $size/1048576,2 ) ." MB"; 
		}
		
		$object                 = new stdClass();
        $object->description    = JText::_('NB_BOARDSIZE');
        $object->value          = $size;
		$rows[]                 = $object;
					
		ViewNinjaboard::showControlPanel($rows);
	}

}
?>
