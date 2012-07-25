<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

jimport('joomla.filesystem.file');

/**
 * Ninjaboard settings table
 *
 * Extends ninja table to inherit its ninja apis, Woooooocha!
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTableSettings extends NinjaDatabaseTableSettings
{
	/**
	 * Get the default values from the xml document
	 *
	 * @author	Stian Didriksen <stian@ninjaforge.com>
	 * @return  array 	key/value data from the doc
	 */
	protected function _getDefaultsFromXML($row)
	{
		$theme = $row->theme ? $row->theme : 'chameleon';
		$path = JPATH_ROOT.'/components/com_ninjaboard/themes/'.$theme.'/'.$theme.'.xml';
		
		//Don't load a file that don't exist
		if(!JFile::exists($path)) return parent::_getDefaultsFromXML($row);
		
		$xml		= simplexml_load_file($path);
		$defaults	= array();

		foreach($xml->form->children() as $i => $group)
		{
			$value = array();
			foreach($group->children() as $i => $element)
			{				
				if(!$element['default']) continue;
				$value[(string)$element['name']] = (string)$element['default'];
			}
			if(count($value) < 1) continue;
			$defaults[(string)(isset($group['name']) ? $group['name'] : $group['group'])] = $value;
		}
					
		
		return array_merge($defaults, parent::_getDefaultsFromXML($row));
	}
}