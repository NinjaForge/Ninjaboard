<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: attachment.php 1543 2011-02-15 22:18:07Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Attachment Helper
 *
 * @package Ninjaboard
 */
class ComNinjaboardTemplateHelperAttachment extends KTemplateHelperAbstract
{
	/**
	 * Gets the allowed file extensions and makes it more readable
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return string
	 */
	public function extensions($config = array())
	{
		$extensions	= JComponentHelper::getParams('com_media')->get('upload_extensions');
		$extensions = preg_split('/[\s,]+/', strtolower($extensions));
		$extensions = array_unique($extensions);
		sort($extensions);
		$extensions = implode(', ', $extensions).'.';
		
		$show = json_encode(JText::_('Show allowed file types'));
		$hide = json_encode(JText::_('Hide allowed file types'));
		KFactory::get('admin::com.ninja.helper.default')->js("
			jQuery(function($){
				$('.allowed-file-extensions-toggle').click(function(event){
					event.preventDefault();

					var prev = $(this).prev();
					$(this).text(prev.is(':visible') ? $show : $hide);
					prev.slideToggle();
				});
			});
		");
		
		return '<p class="allowed-file-extensions">'.$extensions.'</p><a href="#" class="allowed-file-extensions-toggle">'.JText::_('Show allowed file types').'</a>';
	}
	
	/**
	 * The maximum upload size limit, but in a readable format
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return boolean|string	Returns false if there's no limit
	 */
	public function upload_size_limit()
	{
		$params = JComponentHelper::getParams('com_media');

		$upload_size_limit	= $params->get('upload_maxsize');
		
		if($upload_size_limit == 0) return false;

		return sprintf(
			JText::_('Maximum size of %s per file.'),
			KFactory::get('admin::com.ninja.helper.convert')->bytes(array('bytes' => $upload_size_limit))
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
				'name'		=> 'attachments[]',
				'type'		=> 'file'
			)
		));

		$params = JComponentHelper::getParams('com_media');
		if(!$params->get('check_mime', false))
		{
			$config->attributes->append(array(
				'accept'	=> htmlspecialchars($params->get('upload_mime'))
			));
		}

		return '<input '.KHelperArray::toString($config->attributes->toArray()).'/>';
	}
}