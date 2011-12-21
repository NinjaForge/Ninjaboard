<?php
/**
 * @version $Id: ninjaboard.iconset.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Icon Set
 *
 * @package Ninjaboard
 */
class NinjaboardIconSet
{
	/**
	 * The manifest XML object
	 * @var object
	 */
	var $_xml = null;
	
	var $name;
	
	var $xmlFile;
	
	var $icons;
	
	var $iconByFunction;
	
	function NinjaboardIconSet($xmlFile) {
		
		// initialize variables
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		
		$this->name = basename($xmlFile, ".xml");
		$this->xmlFile = $xmlFile;
		$this->icons = array();
		
		if (file_exists(NB_ICONS.DS.$this->name.DS.$xmlFile)) {
			$this->_xml =& JFactory::getXMLParser('Simple');
			$this->_xml->loadFile(NB_ICONS.DS.$this->name.DS.$xmlFile);
	
			$root = & $this->_xml->document;
			$iconSet = $root->getElementByPath('iconset');

			if ($iconSet) {
				
				// load language file
				if ($iconSet->attributes('translateable')) {
					$this->loadLanguage($xmlFile);
				}

				$elements = $iconSet->children();
	
				foreach ($elements as $element) {
					$icon = new StdClass();
					$icon->fileName = NB_ICONS_LIVE.DL.$this->name.DL.$element->attributes('filename');
					$icon->group = $element->attributes('group');
					$icon->function = $element->attributes('function');
					$icon->title = JText::_($element->attributes('title'));
					$this->icons[] = $icon;
					$this->iconByFunction[$icon->function] = $icon;
				}
			} else {
				$messageQueue->addMessage(JText::sprintf('NB_MSGXMLPARSINGERROR', JText::_('NB_ICONSET'), NB_ICONS.DS.$this->name.DS.$xmlFile));
			}
		} else {
			$messageQueue->addMessage(JText::sprintf('NB_MSGRESSOURCENOTFOUND', JText::_('NB_ICONSET'), NB_ICONS.DS.$this->name.DS.$xmlFile));
		}		
	}
	
	/**
	 * get instance
	 *
	 * @access 	public
	 * @return object
	 */
	function &getInstance() {
	
		static $ninjaboardIconSet;

		if (!is_object($ninjaboardIconSet)) {
			$ninjaboardConfig	=& NinjaboardConfig::getInstance();
			$ninjaboardIconSet = new NinjaboardIconSet($ninjaboardConfig->getIconSetFile());
		}

		return $ninjaboardIconSet;
	}
	
	/**
	 * get icons by group
	 *
	 * @access 	public
	 * @return array
	 */
	function getIconsByGroup($iconGroup) {

		$icons = array();
		foreach ($this->icons as $icon) {
			if ($icon->group == $iconGroup) {
				$icons[] = $icon;
			}
		}
		return $icons;
	}
	
	/**
	 * load language
	 *
	 * @access 	public
	 */		
	function loadLanguage($site) {
	
		// initialize variables
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		
		// load language file for icon set
		$langFile = NB_ICONS.DS.$this->name.DS.NinjaboardHelper::getLocale($site).DS.$this->name.'.ini';
		
		if (!is_file($langFile)) {
			$langFile = NB_ICONS.DS.$this->name.DS.'en-GB'.DS.$this->name.'.ini';	
		}	
			
		if (is_file($langFile)) {		
			$lang =& JFactory::getLanguage();
			$lang->_load($langFile); // sorry for calling private function, but there is no other solution at the moment!
		} else {
			$messageQueue->addMessage(JText::sprintf('NB_MSGNOLANGUAGEFILE', JText::_('NB_ICONSET')));
		}
	}

}
?>
