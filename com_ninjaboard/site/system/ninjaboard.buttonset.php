<?php
/**
 * @version $Id: ninjaboard.buttonset.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Button Set
 *
 * @package Ninjaboard
 */
class NinjaboardButtonSet
{
	/**
	 * The manifest XML object
	 * @var object
	 */
	var $_xml = null;
	
	var $name;
	
	var $xmlFile;
	
	var $buttons;
	
	var $buttonByFunction;
	
	function NinjaboardButtonSet($xmlFile) {
		
		// initialize variables
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		
		$this->name = basename($xmlFile, ".xml");
		$this->xmlFile = $xmlFile;
		$this->buttons = array();
		
		if (file_exists(NB_BUTTONS.DS.$this->name.DS.$xmlFile)) {
			$this->_xml =& JFactory::getXMLParser('Simple');
			$this->_xml->loadFile(NB_BUTTONS.DS.basename($xmlFile, ".xml").DS.$xmlFile);
	
			$root = & $this->_xml->document;
			$buttonSet = $root->getElementByPath('buttonset');
			
			if ($buttonSet) {
				
				// load language file
				if ($buttonSet->attributes('translateable')) {
					$this->loadLanguage($xmlFile);
				}

				$elements = $buttonSet->children();
				
				if ($elements) {
					
				}
				
				$script = '';

				$spritefile = $buttonSet->attributes('sprite');
				$locales    = NinjaboardHelper::getLocale();

				foreach ($elements as $element) {
					//TODO cleanup this foreach
					$button = new StdClass();
					//TODO change this function later
					//$button->fileName = NB_BUTTONS_LIVE.'/'.$this->name.'/'.NinjaboardHelper::getLocale().'/'.$element->attributes('filename');
					$button->function = $element->attributes('function');
					$style .= 'a.'.$element->attributes('function').' span {'
				    	    . 'background:url('.NB_BUTTONS_LIVE.'/'.$this->name.'/'.$locales.'/'.$spritefile.') no-repeat -'.JText::_($element->attributes('x')).'px -'.JText::_($element->attributes('y')).'px}'
					        . '.'.$element->attributes('function').' {'
				    	    . 'height:'.JText::_($element->attributes('height')).'px;'
				    	    . 'width:'.JText::_($element->attributes('width')).'px;'
				    	    . '}'
					        . 'button.'.$element->attributes('function').' {'
				    	    . 'background:url('.NB_BUTTONS_LIVE.'/'.$this->name.'/'.$locales.'/'.$spritefile.') no-repeat -'.JText::_($element->attributes('x')).'px -'.JText::_($element->attributes('y')).'px}'
					        . '.'.$element->attributes('function').' {'
				    	    . 'height:'.JText::_($element->attributes('height')).'px;'
				    	    . 'width:'.JText::_($element->attributes('width')).'px;'
				    	    . '}'
				    	    . '.'.$element->attributes('function').':hover {'
				    	    . 'background-position:-'.JText::_($element->attributes('x')).'px -'.JText::_($element->attributes('hover')).'px;'
				    	    . '}'
				    	    . '.'.$element->attributes('function').':active {'
				    	    . 'background-position:-'.JText::_($element->attributes('x')).'px -'.JText::_($element->attributes('active')).'px;'
				    	    . '}'; 
					$button->title = JText::_($element->attributes('title'));
					$this->buttons[] = $button;
					$this->buttonByFunction[$button->function] = $button;
				}
				//Add style to stylesheet
				//$doc =& JFactory::getDocument();
				//$doc->addStyleDeclaration( $style );  very dirty this, commented out to stop NB posting the mess above to the head.
				//                                      needs to be done properly 
				
			} else {
				$messageQueue->addMessage(JText::sprintf('NB_MSGXMLPARSINGERROR', JText::_('NB_BUTTONSET'), NB_BUTTONS.DS.$this->name.DS.$xmlFile));
			}
		} else {
			$messageQueue->addMessage(JText::sprintf('NB_MSGRESSOURCENOTFOUND', JText::_('NB_BUTTONSET'), NB_BUTTONS.DS.$this->name.DS.$xmlFile));
		}
	}
	
	/**
	 * get instance
	 *
	 * @access	public
	 * @return	object
	 */
	function &getInstance() {
	
		static $ninjaboardButtonSet;

		if (!is_object($ninjaboardButtonSet)) {
			$ninjaboardConfig	=& NinjaboardConfig::getInstance();
			$ninjaboardButtonSet = new NinjaboardButtonSet($ninjaboardConfig->getButtonSetFile());
		}

		return $ninjaboardButtonSet;
	}
	
	/**
	 * load language
	 *
	 * @access 	public
	 */

	function loadLanguage($site = 'site') {
	
		// initialize variables
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
	
		// load language file for buttons
		$langFile = NB_BUTTONS.DS.$this->name.DS.NinjaboardHelper::getLocale($site).DS.$this->name.'.ini';
		
		if (!is_file($langFile)) {
			$langFile = NB_BUTTONS.DS.$this->name.DS.'en-GB'.DS.$this->name.'.ini';	
		}	
				
		if (is_file($langFile)) {
			$lang =& JFactory::getLanguage();
			$lang->_load($langFile); // sorry for calling private function, but there is no other solution at the moment!
		} else {
			$messageQueue->addMessage(JText::sprintf('NB_MSGNOLANGUAGEFILE', JText::_('NB_BUTTONSET')));
		}
	}

}
?>
