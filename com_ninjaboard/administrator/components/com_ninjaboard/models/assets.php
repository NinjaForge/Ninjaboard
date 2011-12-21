<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: assets.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Assets model
 *
 * Model containing acl permissions
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelAssets extends ComNinjaModelTable
{

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		// Set the state
		// @TODO we need to look at what states we can purge, is getting messy here
		$this->_state;
	}
	
	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
	}
	
	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		parent::_buildQueryColumns($query);
	}
	
	protected function _buildQueryOrder($query)
	{
	
	}
	
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);
	} 
}