<?php defined('_JEXEC') or die('Restricted access');
 /**
 * NinjaForge Ninjaboard
 *
 * @version		$Id: router.php 1349 2011-01-07 13:25:11Z stian $
 * @package		Ninjaboard
 * @copyright	Copyright (C) 2007-2010 Ninja Media Group. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Code borrowed from com_profiles by Nooku.org
 * List views are people/, offices/, departments/
 * Item views are people/id-firstname_lastname, offices/id-officealias, departments/id-departmentalias
 *
 * @TODO	the pluralization and singularization is commeted out as it's likely causing issues with the posting
 * 			of topics when sef is on.
 */
function NinjaboardBuildRoute(&$query)
{
	$segments = array();
	if(array_key_exists('view', $query))
	{
		$segments[0] = $query['view'];

		if(array_key_exists('id', $query))
		{
			$model = KFactory::tmp('admin::com.ninjaboard.model.'.KInflector::pluralize($segments[0]));
			$item  = KInflector::pluralize($segments[0]) != 'avatars' ? $model->id($query['id'])->getItem() : new stdClass;

			if(isset($item->alias) && $query['view'] != 'person' && $query['view'] != 'avatar')
			{
				$segments[1] = $query['id'].':'.KFactory::tmp('lib.koowa.filter.slug')->sanitize($item->alias);
			}
			else
			{
				$segments[1] = $query['id'];
			}

			if($query['view'] == 'forum') unset($segments[0]);
			
			if(array_key_exists('post', $query) && $query['view'] == 'topic'){
				$segments[2] = $query['post'];
				unset($query['post']);
			}

			unset($query['id']);
		}
		
		/*//Find the correct menu item if it does not exist
		if(!array_key_exists('Itemid',$query)){
			static $items;
			if (!$items) {
				$component    = &JComponentHelper::getComponent('com_ninjaboard');
				$menu        = &JSite::getMenu();
				$items        = $menu->getItems('componentid', $component->id);
			}
			if (is_array($items))
			{
				foreach ($items as $item)
				{
					// Check if this menu item links to this view.
					if (isset($item->query['view']) && isset($query['view']) && ($item->query['view'] == $query['view'] ) &&
							isset($item->query['id']) && isset($query['id']) && ($item->query['id'] == $query['id'] )){
							$query['Itemid'] = $item->id;
							//no need to add view or alias if it's defined in the menu item
							unset($segments[0]);
							unset($segments[1]);
					}
					//less specific, at least find a forum item if the menu link doesnt exist
					else if (!isset($query['Itemid']) && isset($item->query['view']) && ($item->query['view'] == $query['view'] )){
							$query['Itemid'] = $item->id;
							//no need for the view parameter already included in the menu item
							unset($segments[0]);
					}
				}
			}
			$segments = array_values($segments);
		}
		//*/
		unset($query['view']);
		
		
		// everything else are filters
		foreach($query as $key => $value)
		{
			//Can't use SEF suffixes for formats as it fails on .json
			if($key != 'option' && $key != 'Itemid'/* && $key != 'format'*/)
			{
				$segments[] = $key;
				$segments[] = $value;
				unset($query[$key]);
			}
		}
		
		//Reset keys to avoid notices in the core
		$parts = $segments;
		$segments = array();
		foreach($parts as $segment)
		{
			$segments[] = $segment;
		}
	}

	return $segments;
}

function NinjaboardParseRoute($segments)
{
	if(isset($segments[0]))
	{
		/*
		$menu = JSite::getMenu();
		$active = $menu->getActive();
		foreach($active->query as $key => $value)
		{
			$vars[$key] = $value;
		}
		//*/

		//If the first segment is an integer then it's a forum
		$id = current(explode(':', $segments[0]));
		if(is_numeric($id))
		{
			$vars['view'] = 'forum';
			$vars['id']	  = $id;
			array_shift($segments);
		}
		else
		{
			$vars['view'] = array_shift($segments);
		}
		
		if(isset($segments[0]) && !isset($vars['id']))
		{
			$id = current(explode(':', $segments[0]));
			if(is_numeric($id))
			{
				$vars['id'] = $id;
				array_shift($segments);
			}
		}

		if(isset($segments[0]) && $vars['view'] == 'topic' && is_numeric($segments[0])) {
			$vars['post'] = array_shift($segments);
		}

		// anything else are filters: name/value/name/value
		while(count($segments)) {
			$vars[array_shift($segments)] = array_shift($segments);
		}
	}

	return $vars;
}