<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: forum.php 1762 2011-04-11 18:59:09Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Forum Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerForum extends ComNinjaboardControllerAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		foreach(array(
			'enabled'	=> true,
			'levels'	=> 3,
			'limit'		=> 0,
			'offset'	=> 0,
			'sort'		=> 'path_sort_ordering'
		) as $key => $val)
		{
			$this->_request->$key = $val;
		}
		
		//Set model recurse
		//if(KRequest::method() == 'GET') KRequest::set('get.recurse', true);
		//Set other model states
		//KRequest::set('get.enabled', true);
		
		$this->registerCallback(array('before.read', 'beforebrowse'), array($this, 'setOrdering'));
		
		$cache = JPATH_ROOT.'/cache/com_'.$this->getIdentifier()->package . '/maintenance.forums.txt';
		
		if(!JFile::exists($cache))
		{
			KFactory::get('admin::com.ninjaboard.controller.maintenance')->forums();
			JFile::write($cache, date('r'));
		}
	}

	/**
	 * Set forums order
	 *
	 */
	public function setOrdering()
	{
		$params = KFactory::get('admin::com.ninjaboard.model.settings')->getParams();

		if(isset($params->sort)) $this->_request->sort = $params->sort;
		else return true;
		
		KFactory::get($this->getModel())->set($this->getRequest());
	}

	/**
	 * Generic save action
	 *
	 * @param   KCommandContext	A command context object
	 * @return 	KDatabaseRow 	A row object containing the saved data
	 */
	protected function _actionSave(KCommandContext $context)
	{
		//Saving not supported on the frontend
	}

	/**
	 * Generic apply action
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRow 	A row object containing the saved data
	 */
	protected function _actionApply(KCommandContext $context)
	{
		//Apply not supported on the frontend
	}

	/**
	 * Generic edit action, saves over an existing item
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRowset A rowset object containing the updated rows
	 */
	protected function _actionEdit(KCommandContext $context)
	{
		//Edit not supported on the frontend
	}

	/**
	 * Generic add action, saves a new item
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRow 	A row object containing the new data
	 */
	protected function _actionAdd(KCommandContext $context)
	{
		//Add not supported on the frontend
	}

	/**
	 * Generic delete function
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	KDatabaseRowset	A rowset object containing the deleted rows
	 */
	protected function _actionDelete(KCommandContext $context)
	{
		//Delete not supported on the frontend
	}
}