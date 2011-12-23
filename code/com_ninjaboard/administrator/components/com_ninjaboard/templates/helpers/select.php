<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Template Select Helper
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class ComNinjaboardHelperSelect extends KTemplateHelperSelect
{
	/**
	 * Generates just the option tags for an HTML select list
	 *
	 * @param	array	An array of objects
	 * @param	string	The name of the object variable for the option value
	 * @param	string	The name of the object variable for the option text
	 * @param	mixed	The key that is selected (accepts an array or a string)
	 * @returns	string	HTML for the select list
	 */
	public function options( $arr, $key = 'value', $text = 'text', $selected = null, $translate = false )
	{
		$html = '';

		foreach ($arr as $i => $option)
		{
			$element =& $arr[$i]; // since current doesn't return a reference, need to do this

			$isArray = is_array( $element );
			$extra	 = '';
			if ($isArray)
			{
				$k 		= $element[$key];
				$t	 	= $element[$text];
				if(isset($element['level'])) $t = self::indent($element['level']).$t;
				$id 	= ( isset( $element['id'] ) ? $element['id'] : null );
				if(isset($element['disable']) && $element['disable']) {
					$extra .= ' disabled="disabled"';
				}
				if(isset($element['attribs'])) {
					$extra .= ' '.$element['attribs'];
				}
			}
			else
			{
				$k 		= $element->$key;
				$t	 	= $element->$text;
				if(isset($element->level)) $t = self::indent($element->level).$t;
				$id 	= ( isset( $element->id ) ? $element->id : null );
				if(isset( $element->disable ) && $element->disable) {
					$extra .= ' disabled="disabled"';
				}
				if(isset($element->attribs)) {
					$extra .= ' '.$element->attribs;
				}
			}

			// This is real dirty, open to suggestions,
			// barring doing a propper object to handle it
			if ($k === '<OPTGROUP>') {
				$html .= '<optgroup label="' . $t . '">';
			} else if ($k === '</OPTGROUP>') {
				$html .= '</optgroup>';
			}
			else
			{
				//if no string after hypen - take hypen out
				$splitText = explode( ' - ', $t, 2 );
				$t = $splitText[0];
				if(isset($splitText[1])){ $t .= ' - '. $splitText[1]; }

				//$extra = '';
				//$extra .= $id ? ' id="' . $arr[$i]->id . '"' : '';
				if (is_array( $selected ))
				{
					foreach ($selected as $val)
					{
						$k2 = is_object( $val ) ? $val->$key : $val;
						if ($k == $k2)
						{
							$extra .= ' selected="selected"';
							break;
						}
					}
				} else {
					$extra .= ( $k == $selected ? ' selected="selected"' : '' );
				}

				//if flag translate text
				if ($translate) {
					$t = JText::_( $t );
				}

				// ensure ampersands are encoded
				$k = JFilterOutput::ampReplace($k);
				$t = JFilterOutput::ampReplace($t);

				$html .= '<option value="'. $k .'" '. $extra .'>' . $t . '</option>';
			}
		}

		return $html;
	}
	
	public function indent($indent = 0, $text = ' - ')
	{
		if($indent < 1) return false;
		$html = array();
		foreach(range(1, $indent) as $i)
		{
			$html[] = $text;
		}
		return implode($html);
	}

	/**
	 * Generates an HTML select list
	 *
	 * @param	array	An array of objects
	 * @param	string	The value of the HTML name attribute
	 * @param	string	Additional HTML attributes for the <select> tag
	 * @param	string	The name of the object variable for the option value
	 * @param	string	The name of the object variable for the option text
	 * @param	mixed	The key that is selected (accepts an array or a string)
	 * @returns	string	HTML for the select list
	 */
	public function usergroups( $arr, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false )
	{
		if ( is_array( $arr ) ) {
			reset( $arr );
		}

		if (is_array($attribs)) {
			$attribs = KHelperArray::toString($attribs);
		 }

		$id = $name;

		if ( $idtag ) {
			$id = $idtag;
		}

		$id		= str_replace('[','',$id);
		$id		= str_replace(']','',$id);

		$html	= '<select name="'. $name .'" id="'. $id .'" '. $attribs .'>';
		$html	.= self::options( $arr, $key, $text, $selected, $translate );
		$html	.= '</select>';

		return $html;
	}
	
	/**
	 * Generates an HTML checkbox list
	 *
	 * @param 	array 	An array of objects
	 * @param 	string 	The value of the HTML name attribute
	 * @param 	mixed	An array of values that need to be selected
	 * @param 	string 	Additional HTML attributes for the <select> tag
	 * @param 	mixed 	The key that is selected
	 * @param 	string 	The name of the object variable for the option value
	 * @param 	string 	The name of the object variable for the option text
	 * @return 	string 	HTML for the select list
	 */
	public function rules( $arr, $name, $selected = null, $attribs = null, $key = 'value', $text = 'text')
	{
		settype($selected, 'array');

		reset( $arr );
		$html = '<ul class="checklist ' . $name . '">';

		if (is_array($attribs)) {
			$attribs = KHelperArray::toString($attribs);
		 }
		
		for ($i=0, $n = count( $arr ); $i < $n; $i++ )
		{
			$k	= $arr[$i]->$key;
			$t	= $arr[$i]->$text;
			if(isset($arr[$i]->level)) $t = self::indent($arr[$i]->level, '<span class="gi">|&mdash;</span>').$t;
			$id	= ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra	= '';
			$extra	.= $id ? ' id="'.$arr[$i]->id.'"' : '';
			
			foreach ($selected as $val)
			{
				$k2 = is_object( $val ) ? $val->$key : $val;
				if ($k == $k2)
				{
					$extra .= ' checked="checked"';
					break;
				}
			}

			$html .= '<li><input type="checkbox" name="'.$name.'[]" id="'.$name.'_value_'.$k.'" value="'.$k.'" '.$extra.' '.$attribs.' />'
					.'<label for="'.$name.'_value_'.$k.'">'.$t.'</label></li>';
		}

		$html .= '<input type="hidden" name="'.$name.'[]" value="" />';
		$html .= '</ul>';
		return $html;
	}
	
	/**
	 * Generates an HTML select list
	 *
	 * @param	array	An array of objects
	 * @param	string	The value of the HTML name attribute
	 * @param	string	Additional HTML attributes for the <select> tag
	 * @param	string	The name of the object variable for the option value
	 * @param	string	The name of the object variable for the option text
	 * @param	mixed	The key that is selected (accepts an array or a string)
	 * @returns	string	HTML for the select list
	 */
	public function genericlist( $arr, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false )
	{
		if ( is_array( $arr ) ) {
			reset( $arr );
		}

		if (is_array($attribs)) {
			$attribs = KHelperArray::toString($attribs);
		 }

		$id = $name;

		if ( $idtag ) {
			$id = $idtag;
		}

		$id		= str_replace('[','',$id);
		$id		= str_replace(']','',$id);

		$html	= '<select name="'. $name .'" id="'. $id .'" '. $attribs .'>';
		$html	.= self::options( $arr, $key, $text, $selected, $translate );
		$html	.= '</select>';

		return $html;
	}
	
	public function languages($value, $name = 'locale', $client = 'site')
	    {
	        $user    = & JFactory::getUser();
	 
	        /*
	         * @TODO: change to acl_check method
	         */
	        if(!($user->get('gid') >= 23) && $client == 'administrator') {
	            return JText::_('No Access');
	        }
	  
	        jimport('joomla.language.helper');
	        $languages = JLanguageHelper::createLanguageList($value, constant('JPATH_'.strtoupper($client)), true);
	        array_unshift($languages, JHTML::_('select.option', '', '- '.JText::_('Select Language').' -'));
	 
	        return JHTML::_('select.genericlist',  $languages, $name, 'class="inputbox"', 'value', 'text', $value, $name );
	    }
	    
	    public function image($value = null, $name = 'image', $title = 'Image')
		{
	
			$doc 		=& JFactory::getDocument();
			
			$script = "\t".'function jInsertEditorText( image, e_name ) {
				document.getElementById(e_name).value = image;
				document.getElementById(e_name+\'preview\').innerHTML = image;
				if(!image.test(\'http\'))
				{
					var el	= $(e_name+\'preview\').getChildren().getLast();
					var src	= el.getProperty(\'src\');
					el.setProperty(\'src\', \''.JURI::root(true).'/\'+src);
					document.getElementById(e_name).value = document.getElementById(e_name+\'preview\').innerHTML;
				}
			}';
			if(!defined('JELEMENT_IMAGE'))
			{
				$doc->addScriptDeclaration($script);
				define('JELEMENT_IMAGE', true);
			}
			$media =& JComponentHelper::getParams('com_media');
			$ranks = array('publisher', 'editor', 'author', 'registered');
			$acl = & JFactory::getACL();
			for($i = 0; $i < $media->get('allowed_media_usergroup', 3); $i++)
			{
				$acl->addACL( 'com_media', 'popup', 'users', $ranks[$i] );
			}
			//Make sure the user is authorized to view this page
			$user = & JFactory::getUser();
			if (!$user->authorize( 'com_media', 'popup' )) {
				return JText::_('You\'re not authorized to access the media manager');
			}
	
			//Create the modal window link. &e_name let us have multiple instances of the modal window.
			$link = 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;e_name='.$name;
	
			JHTML::_('behavior.modal');
	
			return ' <input type="hidden" name="'.$name.'" id="'.$name.'" value="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'" /><div class="button2-left"><div class="image"><a class="modal" title="'.JText::_($title).'" href="'.$link.'"  rel="{handler: \'iframe\', size: {x: 570, y: 400}}">'.JText::_($title).'</a></div></div><br /><div id="'.$name.'preview" class="image-preview">'.$value.'</div>';
		}
}
