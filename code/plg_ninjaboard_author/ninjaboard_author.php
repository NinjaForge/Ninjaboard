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
jimport('joomla.application.component.router');
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
	/**
	 * 2.5 method name change wrapper
	 */ 
	public function onContentAfterDisplay($context, &$article, &$params, $page = 0)
	{
		return $this->onAfterDisplayContent($article, $params, $page);
	}

	/**
	 * Method for rendering an article author box linked to ninjaboard
	 */
	public function onAfterDisplayContent(&$article, &$params, $limitstart, $context = null)
	{
		$html			= '';
		$option			= JRequest::getCmd('option');
		$view			= JRequest::getCmd('view');
		$itemid			= null;

		// a rather annoying issue with plugins and routing, we end up with links like /catid/2 (2.5) if we dont use a itemid
		$component    	= JComponentHelper::getComponent('com_ninjaboard');
		$menu         	= JFactory::getApplication()->getMenu();
		$items        	= $menu->getItems(version_compare(JVERSION,'1.6.0','ge') ? 'component_id' : 'componentid', $component->id);
    	if (is_array($items))
    	{
    		foreach ($items as $item)
    		{
    		    if(isset($item->query['view']) && $item->query['view'] == 'forums')
    		    {
    		       	$itemid = $item->id;
    		        break;
    		    }
    		}
    	}

		if ($option == 'com_content' && $view == 'article') $context = 'com_content.article';



		// Only display if we are on a com_content article page
		if ($context == 'com_content.article') {
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
					display: block;
				}'
			);
			$profile = JRoute::_('index.php?option=com_ninjaboard&view=person&id='.$user->id.'&Itemid='.$itemid);

			$person		= KService::get('com://admin/ninjaboard.model.people')->id($user->id)->getItem();
			$avatar_on	= new DateTime($person->avatar_on);
			$cache		= (int)$avatar_on->format('U');
			$u =& JURI::getInstance( JURI::base() );
			$baseUrl = $u->toString();
    		$document =& JFactory::getDocument();

			$html .= '<div id="ninjaboard_author_avatar" class="clearfix">';
				$html .= '<a class="avatar" href="'.JRoute::_('index.php?option=com_ninjaboard&view=person&id='.$user->id.'&Itemid='.$itemid).'" style="background-image: url('.JRoute::_('index.php?option=com_ninjaboard&view=avatar&id='.$user->id.'&thumbnail=large&Itemid='.$itemid).'); height: 100px; width: 100px;"></a>';
				//$html .= KService::get('com://site/ninjaboard.template.helper.avatar')->image(array('id'	=> $user->id));
				$html .= '<h1><a href="'.$profile.'" itemprop="author">'.$user->name.'</a></h1>';
			$html .= '</div>';

		}

		//$article->text .= $html;

		return $html;
	}
}