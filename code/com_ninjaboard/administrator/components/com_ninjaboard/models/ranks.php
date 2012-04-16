<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
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
class ComNinjaboardModelRanks extends ComDefaultModelDefault
{
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		//lets get our states
	   	$this->_state->insert('enabled', 'int', JFactory::getApplication()->isSite() ? 1 : null);
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
    	
    	if(is_numeric($this->_state->enabled))
    	{
    		$query->where('tbl.enabled', '=', $this->_state->enabled);
    	}
    }
}