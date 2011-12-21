<?php
/**
 * @version $Id: ninjaboard.engine.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Engine
 *
 * @package Ninjaboard
 */
class NinjaboardEngine
{
	/**
	 * authentification data
	 *
	 * @var array
	 */
	var $_ninjaboardEmoticonSet;

	function NinjaboardEngine() {
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
	
		// emoticon set
		$this->_ninjaboardEmoticonSet =& NinjaboardEmoticonSet::getInstance($ninjaboardConfig->getEmoticonSetFile());	
	}
	
	/**
	 * get instance
	 *
	 * @access 	public
	 * @return object
	 */
	function &getInstance() {
	
		static $ninjaboardEngine;

		if (!is_object($ninjaboardEngine)) {
			$ninjaboardEngine = new NinjaboardEngine();
		}

		return $ninjaboardEngine;
	}
	
	function performSession() {
	
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardSession	=& JTable::getInstance('ninjaboardsession');
		$ninjaboardSession->purge(60 * $ninjaboardConfig->getBoardSettings('session_time'));
		
		$session =& JFactory::getSession();
		
		if ($ninjaboardSession->load($session->getId())) {
			$ninjaboardSession->update();
		} else if (!$ninjaboardSession->insert($session->getId())) {
			die($ninjaboardSession->getError());
		}
	}
	
	function convertToHtml(&$post) {
	
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		
		// replace bb codes
		if ($ninjaboardConfig->getBoardSettings('enable_bbcode') && $post->enable_bbcode) {
			$post->text = $this->convertBBToHtml($post->text);
		}
		
		// replace text only for non preformatted HTML
		# Has been prepared that at the callback function.
		$post->text = str_replace("\n",   '<br />', $post->text);
		$post->text = str_replace('{LF}', "\n",     $post->text);
				
		// look up emoticons in post
		if ($ninjaboardConfig->getBoardSettings('enable_emoticons') && $post->enable_emoticons) {
			foreach ($this->_ninjaboardEmoticonSet->codesList as $emoticonCode) {
				$post->text = str_replace($emoticonCode[0], '<img src="'. $emoticonCode[1]->fileName .'" title="'. JText::_($emoticonCode[1]->emoticon) 
															.'" alt="'. JText::_($emoticonCode[1]->emoticon) .'" class="ninjaboardEmoticon" />', $post->text);
			}
		}
	}
	
	function convertBBToHtml($string) {
		$old_string = '';
		while($old_string != $string) {
			$old_string = $string;
			$string = preg_replace_callback('{\[(\w+)((=)(.+)|())\]((.|\n)*)\[/\1\]}U', array($this, 'convertBBToHtmlCallback'), $string);
		}

		return $string;
	}

	function convertBBToHtmlCallback($matches) {
		$tag = trim($matches[1]);
		$innerString = $matches[6];
		$argument = $matches[4];

		switch($tag) {
			case 'b':
				$replacement = '<strong>'.$innerString.'</strong>';
				break;
			case 'i':
			case 'u':
				$replacement = '<'.$tag.'>'.$innerString.'</'.$tag.'>';
				break;
	
			case 'code':
				$replacement = '<span class="codetext">Code:</span><pre class="code">'.str_replace("\n", '{LF}', $innerString).'</pre>';
				break;
	
			case 'color':
				$color = preg_match("[^[0-9a-fA-F]{3,6}$]", $argument) ? '#' . $argument : $argument;
				$replacement =  '<span style="color:' . $color . '">' . $innerString . '</span>';
				break;
	
			case 'email':
				$address = $argument ? $argument : $innerString;
				$replacement = '<a href="mailto:' . $address . '">' . $innerString . '</a>';
				break;
	
			case 'img':
				$replacement = '<img src="' . $innerString . '" />';
				break;
				
			case 'list':
				$tag = ($argument == 'l') ? 'ol' : 'ul';
				$innerString = preg_replace('/\[\*\](.*?)\\n/si', '<li>$1</li>', $innerString);
				$replacement = '<'.$tag.' class="list">' . $innerString . '</'.$tag.'>';
				break;
									
			case 'size':
				if (is_numeric($argument) && $argument > 5 && $argument < 64) {
					$replacement =  '<span style="font-size:' . $argument . 'px;">' . $innerString . '</span>';
				}
				break;
	
			case 'quote':
				$replacement = '<span class="quotetext">Quote:</span><span class="quote">' . $innerString . '</span>';
				break;
	
			case 'url':
				$url = $argument ? $argument : $innerString;
				$replacement = '<a href="' . $url . '" target="_blank">' . $innerString . '</a>';
				break;
	
			default:    // unknown tag => reconstruct and return original expression
				$replacement = '[' . $tag . ']' . $innerString . '[/' . $tag .']';
				break;
		}
	
		return $replacement;
	}						
}

?>
