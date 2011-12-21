<?php defined('KOOWA') or die( 'Restricted access' );
/**
 * @version		$Id: html.php 2278 2011-07-24 23:11:56Z captainhook $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Gets the posts view from the component
 *   
 * @author   	CaptainHook
 * @category	Ninjaboard
 */
class ModNinjaboard_postsHtml extends ModDefaultHtml
{
	
	public function display()
	{
		KFactory::tmp('lib.joomla.document')->addStyleSheet(JRoute::_('media/com_ninjaboard/css/latest_posts.css'));
		
		$controller = KFactory::tmp('site::com.ninjaboard.controller.post');
		$controller->getModel()->reset(); // @TODO - alpha 2 workaround - update/remove for alpha 3
		
		//Module Params for list layout
		$controller->getView()->assign('collapse_content',	 $this->params->get('collapse_content', '1'));
		$controller->getView()->assign('display_avatar',	 $this->params->get('display_avatar', '1'));
		$controller->getView()->assign('module_id',			 '-'.$this->module->id);
		$controller->getView()->assign('latest_style',			 0);
		
		//Ninjaboard Params
		$params = KFactory::get('admin::com.ninjaboard.model.settings')->getParams();
		//Module Params
		$params['moduleclass_sfx'] = $this->params->get('moduleclass_sfx', '');
		$params['posts_num'] = intval($this->params->get('posts_num', 10));
		
		
		return	'<div id="mod-ninjaboard-latest-posts-'.$this->module->id.'" class="mod-ninjaboard-latest-posts '.$params['moduleclass_sfx'].' '.$params['style']['type'].' '.$params['style']['border'].' '.$params['style']['separators'].'">'.
					
					   $controller->direction('desc')
						          ->sort('created_time')
			                      ->limit($params['posts_num'])
			                      ->layout('list')
			                      ->display().
				'</div>';
				
	}
}