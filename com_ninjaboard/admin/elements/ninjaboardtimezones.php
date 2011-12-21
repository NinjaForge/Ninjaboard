<?php
/**
 * @version $Id: ninjaboardtimezones.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Time Zones Element
 *
 * @package Ninjaboard
 */
class JElementNinjaboardTimeZones extends JElement
{

	function fetchElement($name, $value, &$node, $control_name) {
		$db		= &JFactory::getDBO();

		$query = "SELECT z.*"
				. "\n FROM #__ninjaboard_timezones AS z"
				. "\n ORDER BY z.ordering"
				;
		$db->setQuery($query);
		$timezones = $db->loadObjectList( );

		return JHTML::_('select.genericlist',  $timezones, ''.$control_name.'['.$name.']', 'class="inputbox"', 'offset', 'name', $value, $control_name.$name);
	}

}