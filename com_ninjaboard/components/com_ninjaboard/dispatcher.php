<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: dispatcher.php 2483 2011-11-02 03:44:19Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Dispatcher, obviously
 *
 * @IMPORTANT Don't ever use $this->getService on the dispatcher, always use $this->getService
 *
 * @package Ninjaboard
 */
class ComNinjaboardDispatcher extends NinjaDispatcher
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{	
		parent::__construct($config);

        ///*
        // Bogus debug post
        // @TODO will be moved to dummy data plugin
        if(JFactory::getUser()->id == 62 && KRequest::get('get.generate_dummy_posts', 'boolean', false))
        {
            @set_time_limit(300);
            $forums = array(2, 3, 4, 5, 6, 7, 9, 10, 11, 12);
            $posts = array('Lorem ipsum dolor sit amet', 'This is a dummy post', 'Please help me ANYONE!', 'I rock!', 'You guys are awesome :)','lorem',
            'ipsum',
            'dolor',
            'sit',
            'amet',
            'consectetur',
            'adipiscing',
            'elit',
            'curabitur',
            'vel',
            'hendrerit',
            'libero',
            'eleifend',
            'blandit',
            'nunc',
            'ornare',
            'odio',
            'ut',
            'orci',
            'gravida',
            'imperdiet',
            'nullam',
            'purus',
            'lacinia',
            'a',
            'pretium',
            'quis',
            'congue',
            'praesent',
            'sagittis',
            'laoreet',
            'auctor',
            'mauris',
            'non',
            'velit',
            'eros',
            'dictum',
            'proin',
            'accumsan',
            'sapien',
            'nec',
            'massa',
            'volutpat',
            'venenatis',
            'sed',
            'eu',
            'molestie',
            'lacus',
            'quisque',
            'porttitor',
            'ligula',
            'dui',
            'mollis',
            'tempus',
            'at',
            'magna',
            'vestibulum',
            'turpis',
            'ac',
            'diam',
            'tincidunt',
            'id',
            'condimentum',
            'enim',
            'sodales',
            'in',
            'hac',
            'habitasse',
            'platea',
            'dictumst',
            'aenean',
            'neque',
            'fusce',
            'augue',
            'leo',
            'eget',
            'semper',
            'mattis',
            'tortor',
            'scelerisque',
            'nulla',
            'interdum',
            'tellus',
            'malesuada',
            'rhoncus',
            'porta',
            'sem',
            'aliquet',
            'et',
            'nam',
            'suspendisse',
            'potenti',
            'vivamus',
            'luctus',
            'fringilla',
            'erat',
            'donec',
            'justo',
            'vehicula',
            'ultricies',
            'varius',
            'ante',
            'primis',
            'faucibus',
            'ultrices',
            'posuere',
            'cubilia',
            'curae',
            'etiam',
            'cursus',
            'aliquam',
            'quam',
            'dapibus',
            'nisl',
            'feugiat',
            'egestas',
            'class',
            'aptent',
            'taciti',
            'sociosqu',
            'ad',
            'litora',
            'torquent',
            'per',
            'conubia',
            'nostra',
            'inceptos',
            'himenaeos',
            'phasellus',
            'nibh',
            'pulvinar',
            'vitae',
            'urna',
            'iaculis',
            'lobortis',
            'nisi',
            'viverra',
            'arcu',
            'morbi',
            'pellentesque',
            'metus',
            'commodo',
            'ut',
            'facilisis',
            'felis',
            'tristique',
            'ullamcorper',
            'placerat',
            'aenean',
            'convallis',
            'sollicitudin',
            'integer',
            'rutrum',
            'duis',
            'est',
            'etiam',
            'bibendum',
            'donec',
            'pharetra',
            'vulputate',
            'maecenas',
            'mi',
            'fermentum',
            'consequat',
            'suscipit',
            'aliquam',
            'habitant',
            'senectus',
            'netus',
            'fames',
            'quisque',
            'euismod',
            'curabitur',
            'lectus',
            'elementum',
            'tempor',
            'risus',
            'cras');
            foreach(range(0, 499) as $i)
            {
                shuffle($forums);
                reset($forums);
                shuffle($posts);
                reset($posts);
                $data = array('forum_id' => current($forums), 'subject' => current($posts), 'text' => end($posts));
                $this->getService('com://site/ninjaboard.controller.post', array(
                    'notify' => false
                ))
                    ->setModel($this->getService('com://site/ninjaboard.model.posts'))
                    ->add($data);
            }
        }
        //*/
        
        

		$view = $this->getController()->getView();

		if(!is_a($view, 'ComNinjaboardViewHtml')) return;

		//Add the "Forums" to the pathway if the current view or the last pathway item isn't "Forums"
		$pathway = JFactory::getApplication()->getPathWay()->getPathway();
		$last	 = end($pathway);
		
		//If no ItemID, we need to check if there exist a menu item entry for Ninjaboard
		if(KRequest::get('get.Itemid', 'int') === NULL)
		{
		    $component    = JComponentHelper::getComponent('com_ninjaboard');
		    $menu         = JSite::getMenu();
		    $items        = $menu->getItems('componentid', $component->id);
		    
		    // If any menu links to Ninjaboard, find out if any root ones exists
		    if(is_array($items))
		    {
		    	foreach ($items as $item)
		    	{
		    	    if(isset($item->query['view']) && $item->query['view'] == 'forums')
		    	    {
		    	        // Perform a 301 redirect to the right menu item to eliminate duplicate entrypoints
		    	        return JFactory::getApplication()
		    	                   ->redirect(JRoute::_('&Itemid='.$item->id, false), '', '', true);
		    	    }
		    	}
		    }
		}
		
		//Parse the query in the pathway url
		$query = array('Itemid' => KRequest::get('get.Itemid', 'int'));
		if(is_object($last) && isset($last->link)) parse_str(str_replace('index.php?',  '', $last->link), $query);

		// We need to find out if the menu item link has a view param
		$menuquery	= array('view' => '');
		$menu		= JSite::getMenu();
		$item		= $menu->getItem($query['Itemid']);
		
		//Menu item id is invalid, so lets get the active menu item
		if(!$item)	$item = $menu->getActive();
		
		//There is no active menu item, so we must be on the default page (without Itemid)
		if(!$item)	$item = $menu->getDefault();

		//Menu is still false, so abort the operation to prevent warnings
		if(!$item) return;

		parse_str(str_replace('index.php?',  '',$item->link), $menuquery); // remove "index.php?" and parse
		if(!isset($menuquery['view'])) $menuquery['view'] = '';
		
		if($view->getName() != 'forums' && (!$last || KInflector::pluralize($menuquery['view']) != 'forums'))
		{
			$forum = $this->getService('com://site/ninjaboard.controller.forum', array('request' => array('view' => 'forums')))->getView();
			$this->registerCallback('after.render', array($forum, 'setBreadcrumbs'));
		}

		//If the view is forums, don't append the crumb if there's something already that links to it
		if($view->getName() == 'forums' && $menuquery['view'] == 'forums') return;

		$this->registerCallback('after.render', array($view, 'setDocumentTitle'));
		$this->registerCallback('after.render', array($view, 'setBreadcrumbs'));
	}

	/**
	 * Initialize method
	 *
	 * Used to customize options for the default controller, and the entities to map
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'controller' => 'forum',
			'maps'               => array('attachments', 'avatars', 'forums', 'messages', 'people', 'posts', 'profiles', 'settings', 'topics', 'usergroups', 'users', 'watches')
		));

		parent::_initialize($config);
	}

	/**
	 * Register mapped objects to the factory, and other things necessary for Ninjaboard to operate properly
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return boolean	true means it registered, false means it have already registered
	 * @deprecated
	 */
	public static function register()
	{
        JError::raiseWarning(500, __CLASS__."::register is a deprecated method, please use $this->getService to initiate the dispatcher singleton instead.");
	}
}