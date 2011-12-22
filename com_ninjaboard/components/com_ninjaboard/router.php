<?php defined('_JEXEC') or die('Restricted access');
 /**
 * NinjaForge Ninjaboard
 *
 * @package		Ninjaboard
 * @copyright	Copyright (C) 2007-2010 Ninja Media Group. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardRouter
{
    /**
     * cache object
     *
     * @var array
     */
    protected static $_cache;

    /**
     * Cache over extension views, which can't conflict with slugs
     *
     * @var array
     */
    static private $_views;
    
    /**
     * The Itemid so that when it's not in the url we don't do an expensive lookup every single time
     *
     * @var array
     */
    static private $_Itemid;
    
    /**
     * Object cache of urls, to reduce lookup on the cache layer, as well as helping load when caching is off
     *
     * @var array
     */
    static private $_urls;

    /**
     * Builds the url
     *
     */
    public static function write(&$query)
    {
        ///* Find the correct menu item if it does not exist
        if(!array_key_exists('Itemid', $query))
        {
            if(!isset(self::$_Itemid))
            {
            	static $items;
            	if (!$items) {
            		$component    = JComponentHelper::getComponent('com_ninjaboard');
            		$menu         = JSite::getMenu();
            		$items        = $menu->getItems('componentid', $component->id);
            	}
            	if (is_array($items))
            	{
            		foreach ($items as $item)
            		{
            		    if(isset($item->query['view']) && $item->query['view'] == 'forums')
            		    {
            		        self::$_Itemid = $item->id;
            		        break;
            		    }
            		}
            	}
            }
            
            if(isset(self::$_Itemid)) {
                $query['Itemid'] = self::$_Itemid;
            }
        }
        //*/
    
        $data      = false;
        $cache     = self::getCache();
        $cache_key = http_build_query($query);
        
        if(isset(self::$_urls[$cache_key])) {
            $data = self::$_urls[$cache_key];
        } elseif($data = $cache->get($cache_key)) {
            $data                    = unserialize($data);
            self::$_urls[$cache_key] = $data;
        }
        
        if(is_array($data))
        {
            //Remove the stuff we don't want from our url
            foreach($query as $key => $value)
            {
            	//If no value at all, don't remove it as it'll cause parser issues
            	if($value === '') continue;
            
            	//Can't use SEF suffixes for formats as it fails on .json
            	if($key != 'option' && $key != 'Itemid'/* && $key != 'format'*/)
            	{
            		//The following is primarily to fix issues in some 3rd party SEF extensions
            		if($key == 'format' && ($value == 'html' || $value == 'raw')) continue;
            	
            		unset($query[$key]);
            	}
            }
        
            return $data;
        }
        
        $segments = array();
    	if(array_key_exists('view', $query))
    	{
    		$segments[0] = $query['view'];
    
    		if(array_key_exists('id', $query))
    		{
    			$name  = KInflector::pluralize($segments[0]);
    			$model = KService::get('com://admin/ninjaboard.model.'.$name, array(
    				'acl' => false
    			));
    			$item  = KInflector::pluralize($segments[0]) != 'avatars' ? $model->id($query['id'])->getItem() : new stdClass;

                if(isset($item->alias) && $query['view'] == 'forum' && !in_array($item->alias, self::getViews()))
                {
                    $segments[1] = $item->alias;
                }
                elseif($query['view'] == 'topic' && $item->id && $item->alias)
                {
                    $forum_model = KService::get('com://admin/ninjaboard.model.forums', array(
                    	'acl' => false
                    ));
                    $forum = $forum_model->id($item->forum_id)->getItem();
                    $slug  = self::getTopicSlug($item->subject, $item->id);
                    if($forum->id && $forum->alias && $slug && !in_array($forum->alias, self::getViews()))
                    {
                        $segments[0] = $forum->alias;
                        $segments[1] = $slug;
                    }
                    else {
                        $segments[1] = $query['id'].':'.KService::get('com://admin/ninjaboard.filter.slug')->sanitize($item->alias);
                    }
                }
    			elseif(isset($item->alias) && $query['view'] != 'person' && $query['view'] != 'avatar')
    			{
    				$segments[1] = $query['id'].':'.KService::get('com://admin/ninjaboard.filter.slug')->sanitize($item->alias);
    			}
    			else
    			{
    				$segments[1] = $query['id'];
    			}
    
    			if($query['view'] == 'forum') unset($segments[0]);
    			
    			if(array_key_exists('post', $query) && $query['view'] == 'topic'){
    				$segments[] = $query['post'];
    				unset($query['post']);
    			}
    
    			unset($query['id']);
    		}
    		unset($query['view']);
    		
    		
    		// everything else are filters
    		foreach($query as $key => $value)
    		{
    			//If no value at all, don't add it as it'll cause parser issues
    			if($value === '') continue;
    		
    			//Can't use SEF suffixes for formats as it fails on .json
    			if($key != 'option' && $key != 'Itemid'/* && $key != 'format'*/)
    			{
    				//The following is primarily to fix issues in some 3rd party SEF extensions
    				if($key == 'format' && ($value == 'html' || $value == 'raw')) continue;
    			
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

        self::$_urls[$cache_key] = $segments;
        $cache->store(serialize($segments), $cache_key);
    
    	return $segments;
    }
    
    /**
     * Parse the url
     *
     */
    public static function read($segments)
    {
        $cache     = self::getCache();
        $cache_key = http_build_query($segments);
        
        if($data = $cache->get($cache_key))
        {
            if($data = unserialize($data)) return $data;
        }
    
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
    		$first = array_shift($segments);
    		$id    = current(explode(':', $first));
    		//If the first two parts are strings, then it's a topic
    		$second = isset($segments[0]) && !KService::get('koowa:filter.alnum')->validate($segments[0]) ? $segments[0] : false;

            if($second && !in_array($first, self::getViews()))
            {
                $vars['view'] = 'topic';
                $parts        = explode(':', array_shift($segments));
                $part         = array_shift($parts);
                $slug         = $parts ? $part.'-'.implode(':', $parts): $part;
                $table        = KService::get('com://admin/ninjaboard.database.table.topic_slugs');
                $item         = $table->select(array('ninjaboard_topic_slug' => $slug), KDatabase::FETCH_ROW); 
                $vars['id']   = $item->ninjaboard_topic_id;
                
                //For SEO purposes, redirect to the right url if the forum slug is incorrect
                if(!is_numeric($id))
                {
                    $topic = KService::get('com://admin/ninjaboard.model.topics')->id($item->ninjaboard_topic_id)->getItem();
                    $forum = KService::get('com://admin/ninjaboard.model.forums')->id($topic->forum_id)->getItem();

                    $parts        = explode(':', $first);
                    array_shift($parts);
                    $alias        = $parts ? $id.'-'.implode(':', $parts): $id;
                    
                    if($forum->id && $forum->alias != $alias && !in_array($forum->alias, self::getViews()))
                    {
                        $search  = $alias.'/'.$slug;
                        $replace = $forum->alias.'/'.$slug;
                        $redirect = str_replace($search, $replace, KRequest::url());
                        
                        //Perform 301 permament redirect so that the search engine listings are corrected
                        JFactory::getApplication()->redirect($redirect, '', '', true);
                    }
                }
            }
    		elseif(is_numeric($id))
    		{
    			$vars['view'] = 'forum';
    			$vars['id']	  = $id;
    		}
    		elseif(!in_array($first, self::getViews()))
    		{
    		    $vars['view'] = 'forum';
    		    $parts        = explode(':', $first);
    		    array_shift($parts);
                $alias        = $parts ? $id.'-'.implode(':', $parts): $id;
    		    $query = KService::get('koowa:database.adapter.mysqli')->getQuery()
    		                 ->select('ninjaboard_forum_id')
    		                 ->where('alias', '=', $alias);
    		    $vars['id']	  = KService::get('com://admin/ninjaboard.database.table.forums')->select($query, KDatabase::FETCH_FIELD);
    		}
    		else
    		{
    			$vars['view'] = $first;
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
    	
    	$cache->store(serialize($vars), $cache_key);
    
    	return $vars;
    }
    
    /**
     * Gets a slug for a topic, creates an entry in the #__ninjaboard_topic_slugs if necessary
     *
     * @param  string $subject   The topic subject that are to be turned into a slug
     * @param  int    $id        The topic id
     * @return string | boolean  Returns the created/fetched slug, false if a slug couldn't be created
     */
    private static function getTopicSlug($subject, $id)
    {
        $slug  = KService::get('com://admin/ninjaboard.filter.slug')->sanitize($subject);
        $table = KService::get('com://admin/ninjaboard.database.table.topic_slugs');
        $item  = $table->select(array('ninjaboard_topic_id' => $id), KDatabase::FETCH_ROW);

        if($item->ninjaboard_topic_slug) return $item->ninjaboard_topic_slug;

        $is_alnum = KService::get('koowa:filter.alnum')->validate($slug);
        if($is_alnum || $table->count(array('ninjaboard_topic_slug' => $slug))) return self::getTopicSlugRecurse($slug, $id);
        
        $table->getRow()
              ->setData(array('ninjaboard_topic_slug' => $slug, 'ninjaboard_topic_id' => $id))
              ->save();

        return $slug;
    }
    
    /**
     * Deals with cases where the slug already exists, but are used for another topic id
     *
     * @param  string $slug      The topic slug that already exists
     * @param  int    $id        The topic id
     * @param  int    $increment Increments each time the recurse is done
     * @return string            Returns the created/fetched slug
     */
    private static function getTopicSlugRecurse($slug, $id, $increment = 1)
    {
        $table = KService::get('com://admin/ninjaboard.database.table.topic_slugs');
        $new   = $slug.'-'.$increment;
        //Critical to make sure the new slug isn't longer than 100 characters
        $count = strlen($new);
        if($count > 100)
        {
            $diff = $count - 100;
            $slug = substr($slug, 0, strlen($slug) - $diff);
            $new  = $slug.'-'.$increment; 
        }
        
        if($table->count(array('ninjaboard_topic_slug' => $new))) return self::getTopicSlugRecurse($slug, $id, ++$increment);
        
        $table->getRow()
              ->setData(array('ninjaboard_topic_slug' => $new, 'ninjaboard_topic_id' => $id))
              ->save();        

        return $new;
    }
    
    /**
     * Gets all the views, so we know what slugs a forum cannot have
     *
     * @return array
     */
    private static function getViews()
    {
        if(!isset(self::$_views))
        {
            if(!class_exists('JFolder')) jimport('joomla.filesystem.folder');
            self::$_views = JFolder::folders(JPATH_ROOT.'/components/com_ninjaboard/views');
        }
        
        return self::$_views;
    }

    /**
     * Gets the cache object with urls
     *
     * @return JCache instance
     */
    public static function getCache()
    {
        if(!isset(self::$_cache)) self::$_cache = JFactory::getCache('com.ninjaboard.router', 'output');
        
        return self::$_cache;
    }
}

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
	return ComNinjaboardRouter::write($query);
}

function NinjaboardParseRoute($segments)
{
	return ComNinjaboardRouter::read($segments);
}