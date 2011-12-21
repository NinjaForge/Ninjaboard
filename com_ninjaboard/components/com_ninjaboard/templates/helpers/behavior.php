<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: behavior.php 1660 2011-03-21 22:52:52Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Behavior Helper
 *
 * @package Ninjaboard
 */
class ComNinjaboardTemplateHelperBehavior extends KTemplateHelperAbstract
{
	/**
	 * Email Updates button
	 *
	 * @author Stian Didriksen
	 */
	public function watch($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'view'		=> KRequest::get('get.view', 'cmd'),
			'id'		=> false,
			'active'	=> 'watching',
			'hover'		=> 'unwatch',
			'class'		=> 'watch',
			'lang'		=> array(
				'subscribe'		=> JText::_('Subscribe'),
				'subscribed'	=> JText::_('Subscribed'),
				'unsubscribe'	=> JText::_('Unsubscribe'),
			),
			
		));
		
		$table = KFactory::get('admin::com.ninjaboard.database.table.watches');

		$config->append(array(
			'type'		=> $table->getTypeIdFromName($config->view),
			'type_id'	=> $config->id
		));
		
		$url = '?option=com_ninjaboard&view=watches&format=json';
		$selector = KFactory::get('admin::com.ninja.helper.default')->formid('watch');
		
		
		static $loaded;
		if(!$loaded) $loaded = array();

		if(!isset($loaded[$selector]))
		{
			$loaded[$selector] = true;
			KFactory::get('admin::com.ninja.helper.default')->js('/watch.js');
			KFactory::get('admin::com.ninja.helper.default')->js('
				jQuery(function($){
					$(\'.'.$selector.'\').ninjaboardWatch('.json_encode(array(
						'active'	=> $config->active,
						'hover'		=> $config->hover,
						'lang'		=> $config->lang->toArray(),
						'watch'		=> array(
							'url'	=> JRoute::_($url, false),
							'data'	=> array(
								'_token'		=> JUtility::getToken(),
								'subscription_type'		=> $config->type,
								'subscription_type_id'	=> $config->type_id,
								'action' => 'add'
							)
						),
						'unwatch'	=> array(
							'url'	=> $url.'&type='.$config->type.'&type_id='.$config->type_id,
							'data'	=> array(
								'_token' => JUtility::getToken(),
								'action' => 'delete'
							)
						)
					)).');
				});');
		}



		$me			= KFactory::get('admin::com.ninjaboard.model.people')->getMe();
		$params		= KFactory::get('admin::com.ninjaboard.model.settings')->getParams();
		$watching	= (bool)KFactory::tmp('admin::com.ninjaboard.model.watches')
																		->by($me->id)
																		->type($config->type)
																		->type_id($config->type_id)
																		->getTotal();

		//@TODO make this a model method that fetches just the id
		$id			= KFactory::tmp('admin::com.ninjaboard.model.watches')
																		->by($me->id)
																		->type($config->type)
																		->type_id($config->type_id)
																		->limit(0)
																		->getList()
																		->getData();
																		
		if(!$id) $id = false;
		if(is_array($id)) $id = key($id);
		
		$attr   = KHelperArray::toString(array(
			'class'			=> $config->class.' '.$selector.($watching ? ' '.$config->active : ''),
			'data-watching'	=> (int)$watching,
			'data-id'		=> $id
		));
		$html[] = '<div '.$attr.'>';
		$html[] = '<a>'.($watching ? $config->lang->subscribed : $config->lang->subscribe).'</a>';
		$html[] = '</div>';
		
		return implode($html);
	}
}