<?php
/**
 * @version		$Id: ninjacontent.php 443 2011-03-23 08:15:46Z Richie $
 * @category	Ninjaboard
 * @package		Plugins
 * @subpackage	Content
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
defined('KOOWA') or die("Koowa isn't available, or file is accessed directly");

jimport('joomla.event.plugin');

/**
 * Content Author Plugin Class - Displays the users Ninjaboard Avatar
 *
 * @author		Richie Mortimer <richie@ninjaforge.com>
 * @category	Ninjaboard
 * @package		Plugins
 * @subpackage	Content
 */
class plgContentNinjaboard_Author extends JPlugin 
{
	public function onPrepareContent(&$article, &$params, $limitstart)
	{
		$html	= '';
		$option	= JRequest::getCmd('option');
		$view	= JRequest::getCmd('view');

		// Only display if we are on a com_content article page
		if ($option == 'com_content' && $view == 'article')
		{
			// Get the ninjaboard Settings
			//$params = KFactory::get('admin::com.ninjaboard.model.settings')->getParams();
			$user	= JFactory::getUser($article->created_by);

			JFactory::getDocument()->addStyleDeclaration('
				#ninjaboard_author_avatar {
					background: white;
					border: 1px solid #E6E6E6;
				}
				#ninjaboard_author_avatar a.avatar {
					float:left;
					padding:10px;
					background-repeat: no-repeat;
					background-position:center
				}'
			);
			$profile = JRoute::_('&option=com_ninjaboard&view=person&id='.$user->id);

			$person		= KService::get('com://admin/lfnews.model.people')->id($user->id)->getItem();
			$avatar_on	= new DateTime($person->avatar_on);
			$cache		= (int)$avatar_on->format('U');
			$u =& JURI::getInstance( JURI::base() );
			$baseUrl = $u->toString();
    		$document =& JFactory::getDocument();

			$html .= '<div id="ninjaboard_author_avatar" class="clearfix">';
				$html .= '<div class="avatar">';
					$html .= KService::get('com://admin/ninjaboard.template.helper.avatar')->image(array('id'	=> $user->id));
				$html .= '</div>';
				$html .= '<h1><a href="'.$profile.'" itemprop="author">'.$user->name.'</a></h1>';
			$html .= '</div>';

		}

		$article->text .= $html;

		return;
	}
}