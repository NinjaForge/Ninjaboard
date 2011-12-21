<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: ranks.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Ranks model
 *
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelRanks extends KModelTable
{
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		//lets get our states
	   	$this->_state->insert('enabled', 'int', KFactory::get('lib.joomla.application')->isSite());
	}

    /**
     * Query WHERE clause
     */
    protected function _buildQueryWhere(KDatabaseQuery $query)
    {
    	parent::_buildQueryWhere($query);
    
    	if($search = $this->_state->search)
    	{
    		$query->where('tbl.title', 'LIKE', '%'.$search.'%');
    	}
    	
    	if($this->_state->enabled !== false && $this->_state->enabled !== '')
    	{
    		$query->where('tbl.enabled', '=', $this->_state->enabled);
    	}
    }
}