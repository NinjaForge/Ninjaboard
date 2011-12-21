<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: dispatcher.php 1393 2011-01-11 21:58:55Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Dispatcher, obviously
 *
 * @package Ninjaboard
 */
class ComNinjaboardDispatcher extends ComDefaultDispatcher
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$view = $this->getController()->getView();

		if(!is_a($view, 'ComNinjaboardViewHtml')) return;

		//Add the "Forums" to the pathway if the current view or the last pathway item isn't "Forums"
		$pathway = KFactory::get('lib.koowa.application')->getPathWay()->getPathway();
		$last	 = end($pathway);
		
		//Parse the query in the pathway url
		$query = array('Itemid' => false);
		if($last) parse_str(str_replace('index.php?',  '', $last->link), $query);

		// We need to find out if the menu item link has a view param
		$menuquery = array('view' => '');
		$menu = JSite::getMenu()->getItem($query['Itemid']);
		parse_str(str_replace('index.php?',  '',$menu->link), $menuquery); // remove "index.php?" and parse
		if($view->getName() != 'forums' && (!$last || KInflector::pluralize($menuquery['view']) != 'forums'))
		{
			$forum = KFactory::tmp('site::com.ninjaboard.controller.forum', array('request' => array('view' => 'forums')))->getView();
			$this->registerCallback('after.render', array($forum, 'setBreadcrumbs'));
		}

		//If the view is forums, don't append the crumb if there's something already that links to it
		if($view->getName() == 'forums' && $menuquery['view'] == 'forums') return;

		$this->registerCallback('after.render', array($view, 'setDocumentTitle'));
		$this->registerCallback('after.render', array($view, 'setBreadcrumbs'));
	}

    protected function _initialize(KConfig $config)
    {
        $config->append(array(
                'controller_default' => 'forum'
        ));

        parent::_initialize($config);
    }
}