<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Usergroups model
 *
 * @TODO look into if we can just delete this file.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelUsergroups extends ComDefaultModelDefault
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
		$this->_state
			->insert('visible', 'int', JFactory::getApplication()->isSite());
	}

	/**
     * Get a list over permissions for a group
     *
     * @param  int $id			The primary key
     * @return KDatabaseRowset
     */
    public function getPermissions($id)
    {
    	$table = $this->getService('com://admin/ninjaboard.database.table.assets');
    	$query = $table->getDatabase()->getQuery();
    	$prefx = $table->getDatabase()->getTablePrefix();

		$query->where('tbl.name', 'LIKE', 'com_ninjaboard.usergroup.'.$id.'.%');
		
		$permissions = array();
		foreach ($table->select($query, KDatabase::FETCH_ROWSET) as $permission)
		{
			$column = explode('.', $permission->name);
			$permission->column	= end($column);
			$permission->title	= KInflector::humanize($permission->column);
			$permissions[$permission->column] = $permission;
		}

    	return $permissions;
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

		if($this->_state->visible)
		{
			$query->where('tbl.visible', '=', 1);
		}
    }
}