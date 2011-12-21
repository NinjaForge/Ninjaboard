<?php
/**
 * @version $Id: ninjaboard.emoticonset.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Emoticon Set
 *
 * @package Ninjaboard
 */
class NinjaboardEmoticonSet
{
	/**
	 * The manifest XML object
	 * @var object
	 */
	var $_xml = null;
	var $name;
	var $xmlFile;
	var $emoticons;
	var $codesList;

	function NinjaboardEmoticonSet($xmlFile) {
		
		// initialize variables
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		
		$this->name = basename($xmlFile, ".xml");
		$this->xmlFile = $xmlFile;
		$this->emoticons = array();
		$this->codesList = array();
		
		if (file_exists(NB_EMOTICONS.DS.$this->name.DS.$xmlFile)) {
			$this->_xml =& JFactory::getXMLParser('Simple');
			$this->_xml->loadFile(NB_EMOTICONS.DS.$this->name.DS.$xmlFile);
			
			$root = & $this->_xml->document;
			$emoticonSet = $root->getElementByPath('emoticonset');
			
			if ($emoticonSet) {
				
				// load language file
				if ($emoticonSet->attributes('translateable')) {
					$this->loadLanguage($xmlFile);
				}

				$elements = $emoticonSet->children();
		
				foreach ($elements as $element) {
					$emoticon = new StdClass();
					$emoticon->fileName = NB_EMOTICONS_LIVE.DL.$this->name.DL.$element->attributes('filename');
					$emoticon->emoticon = $element->attributes('emoticon');
					$emoticon->codes = explode(',', $element->attributes('codes'));
					$emoticon->hidden = $element->attributes('hidden');
					$this->emoticons[] = $emoticon;
					foreach ($emoticon->codes as $emoticonCode) {
						$this->codesList[] = array($emoticonCode, $emoticon);
					}
				}
				
				// sort the code list, so the engine can interpret them
				$this->bubbleSortDescending($this->codesList);
			} else {
				$messageQueue->addMessage(JText::sprintf('NB_MSGXMLPARSINGERROR', JText::_('NB_EMOTICONSET'), NB_EMOTICONS.DS.$this->name.DS.$xmlFile));
			}
		} else {
			$messageQueue->addMessage(JText::sprintf('NB_MSGRESSOURCENOTFOUND', JText::_('NB_EMOTICONSET'), NB_EMOTICONS.DS.$this->name.DS.$xmlFile));
		}

	}

	/**
	 * get instance
	 *
	 * @access 	public
	 * @return object
	 */
	function &getInstance($xmlFile) {
	
		static $ninjaboardEmoticonSet;

		if (!is_object($ninjaboardEmoticonSet)) {
			$ninjaboardEmoticonSet = new NinjaboardEmoticonSet($xmlFile);
		}

		return $ninjaboardEmoticonSet;
	}
	
	/**
	 * load language
	 *
	 * @access 	public
	 */
	function loadLanguage($site = 'site') {
	
		// initialize variables
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		
		// load language file for emoticons
		$langFile = NB_EMOTICONS.DS.$this->name.DS.NinjaboardHelper::getLocale($site).DS.$this->name.'.ini';
		
		if (!is_file($langFile)) {
			$langFile = NB_EMOTICONS.DS.$this->name.DS.'en-GB'.DS.$this->name.'.ini';	
		}
			
		if (is_file($langFile)) {
			$lang =& JFactory::getLanguage();
			$lang->_load($langFile); // sorry for calling private function, but there is no other solution at the moment!	
		} else {
			$messageQueue->addMessage(JText::sprintf('NB_MSGNOLANGUAGEFILE', JText::_('NB_EMOTICONSET')));
		}
	}
	
	/**
	 * bubble sort descending
	 *
	 * @access 	public
	 */
	function bubbleSortDescending(&$a) {
		$length = count($a);

		for ($i=1; $i < $length; $i++) {
			$flag = 0;
			for ($j=0; $j < $length-$i; $j++) {
				if (strlen($a[$j][0]) < strlen($a[$j+1][0])) {
					$h = $a[$j];
					$a[$j] = $a[$j+1];
					$a[$j+1] = $h;
				} 
			} 
		}
	}
					
}
?>
