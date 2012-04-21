<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Avatar Helper
 *
 * @package Ninjaboard
 */
class ComNinjaboardTemplateHelperAvatar extends KTemplateHelperAbstract
{
	/**
	 * Gets the allowed image extensions and makes it more readable
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return string
	 */
	public function extensions($config = array())
	{
		$extensions	= JComponentHelper::getParams('com_media')->get('image_extensions');
		$extensions = preg_split('/[\s,]+/', $extensions);
		$extensions = array_unique($extensions);
		sort($extensions);

		if(($key = array_search('bmp', $extensions)) !== false)
		    unset($extensions[$key]);
		
		return implode(', ', $extensions).'.';
	}
	
	/**
	 * The maximum upload size limit, but in a readable format
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return boolean|string	Returns false if there's no limit
	 */
	public function upload_size_limit($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'params'	=> $this->getService('com://admin/ninjaboard.model.settings')->getParams()
		));

		$upload_size_limit	= $config->params['avatar_settings']['upload_size_limit'];
		
		if($upload_size_limit == 0) return false;

		return sprintf(
			JText::_('COM_NINJABOARD_MAXIMUM_SIZE_OF'),
			$this->getService('ninja:template.helper.convert')->bytes(array('bytes' => $upload_size_limit))
		);
	}

	/**
	 * Renders the input element, with the accept attribute set when needed
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return string
	 */
	public function input($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'attributes'	=> array(
				'id'		=> 'avatar',
				'name'		=> 'avatar',
				'size'		=> 15,
				'type'		=> 'file',
				'class'		=> 'value',
				'accept'	=> 'image/*'
			)
		));

		$params = JComponentHelper::getParams('com_media');
		if(!$params->get('check_mime', false))
		{
			$config->attributes->append(array(
				//'accept'	=> htmlspecialchars($params->get('upload_mime'))
				//'accept'	=> 'image/*'
			));
		}

		return '<input '.KHelperArray::toString($config->attributes->toArray()).'/>';
	}

	/**
	 * Renders the html markup for the user avatar
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return string
	 */
	public function image($config = array())
	{
		$params		= $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$prepend	= JFactory::getApplication()->isAdmin() ? KRequest::root().'/' : '';

		$config = new KConfig($config);
		$config->append(array(
			'id'		=> $this->getService('com://admin/ninjaboard.model.people')->getMe()->id,
			'thumbnail'	=> 'large',
			'class'		=> 'avatar',
			'link'		=> 'person',
			'type'		=> 'css'	//optionally tag (<img>)
		));
		
		$person		= $this->getService('com://admin/ninjaboard.model.people')->id($config->id)->getItem();
		$avatar_on	= new DateTime($person->avatar_on);
		$cache		= (int)$avatar_on->format('U');

		$config->append(array(
			'avatarurl'		=> $prepend.JRoute::_('&option=com_ninjaboard&view=avatar&id='.$config->id.'&thumbnail='.$config->thumbnail.'&cache='.$cache),
			'profileurl'	=> $prepend.JRoute::_('&option=com_ninjaboard&view=person&id='.$config->id)
		));
		
		$attribs = array(
			'class'	=> $config->class,
			'href'	=> $config->profileurl
		);
		$height = $params['avatar_settings'][$config->thumbnail.'_thumbnail_height'];
		$width  = $params['avatar_settings'][$config->thumbnail.'_thumbnail_width'];
		
		///* @TODO Following is the <img /> version, likely going to be deprecated
		if($config->type == 'tag')
		{
			
			$attribs['src']		= $config->avatarurl;
			$attribs['height']	= $height;
			$attribs['width']	= $width;
			unset($attribs['href']);

			$html  = '<img '.KHelperArray::toString($attribs).' />';
		}
		//*/
		else
		{
			$style  = 'background-image: url('.$config->avatarurl.'); ';
			$style .= 'height: '.$height.'px; ';
			$style .= 'width: '.$width.'px;';
			$attribs['style'] = $style;
	
			$html = '<a '.KHelperArray::toString($attribs).'></a>';
		}
		
		return $html;
	}

	/**
	 * Gets an avatar from gravatar - Based on:
	 *
	 * From Gravatar Help:
	 *        "A gravatar is a dynamic image resource that is requested from our server. The request
	 *        URL is presented here, broken into its segments."
	 * Source:
	 *    http://site.gravatar.com/site/implement
	 *
	 *
	 *	Class Page: http://www.phpclasses.org/browse/package/4227.html
	 *
	 * @author Lucas Araújo <araujo.lucas@gmail.com>
	 * @version 1.0
	 * @package Gravatar
	 */
	public function gravatar($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'email'			=> '',
			'size'			=> '80',
			'gravatar_id'	=> null,
			'default'		=> 404,
			'extra'			=> '',
			'html'			=> false
		));

		if ($this->getService('koowa:filter.email')->validate($config->email)) {
            $config->gravatar_id = md5(strtolower($config->email));
        } 

        if (!$config->gravatar_id) return;

        $url = 'http://www.gravatar.com/avatar/'.$config->gravatar_id.'?default='.$config->default.'&size='.$config->size;

        return !$config->html ? $url : '<img src="'.$url.'" width="'.$config->size.'" height="'.$config->size.'"'.$config->extra.' />';    
	}
}