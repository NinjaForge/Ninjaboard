<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Usergroup maps model
 *
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelUsergroupmaps extends ComDefaultModelDefault
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
		$this->_state->insert('id', 'int');
	}
	
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);

		if($this->_state->id) $query->where('tbl.joomla_user_id', 'in', $this->_state->id);
	}

	/**
	 * Get an array over group ids
	 */
	public function getGroups()
	{
		// Get the data if it doesn't already exist
        if (!isset($this->_groups))
        {
        	$table = $this->getTable();
        	$query = $table->getDatabase()->getQuery();
        	
        	$query->select('ninjaboard_user_group_id');
        	
        	$this->_buildQueryFrom($query);
        	$this->_buildQueryJoins($query);
        	$this->_buildQueryWhere($query);
        	$this->_buildQueryGroup($query);
        	$this->_buildQueryHaving($query);
        	$this->_buildQueryOrder($query);
        	$this->_buildQueryLimit($query);
        	
        	//Create query object
			if(is_numeric($query) || is_array($query))
	       	{
	        	$key    = $this->mapColumns($this->getIdentityColumn());
	           	$values = (array) $query;
	
	       		$query = $this->_database->getQuery()
	        				->where($key, 'IN', $values);
	     	}
	
        	if(!count($query->from)) {
        		$query->from($this->getName().' AS tbl');
			}
	      	
        	$this->_groups = $table->getDatabase()->select($query, KDatabase::FETCH_FIELD_LIST);	
        }

        return $this->_groups;
	}
}