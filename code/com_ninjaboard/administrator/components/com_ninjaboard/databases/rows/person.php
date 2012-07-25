<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseRowPerson
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseRowPerson extends KDatabaseRowDefault
{
	/**
	 * assigned usergroup ids cache
	 *
	 * @var string
	 */
	private $_ninjaboard_usergroup_id;
	
	/**
	 * Permissions cache
	 *
	 * @var array
	 */
	private $_permissions = array();

	/**
     * Retrieve row field value
     *
     * @param  	string 	The column name.
     * @return 	string 	The corresponding column value.
     */
    public function __get($column)
    {
    	if($column == 'usergroups')
		{
			if(!$this->_usergroups)
			{
				$ids = explode('|', $this->ninjaboard_usergroup_id);
				$this->_usergroups = $this->getService('com://admin/ninjaboard.model.usergroups')->limit(0)->id($ids)->getList();
			}

			return $this->_usergroups;
		}
    
		if($column == 'ninjaboard_usergroup_id')
		{
			if(!isset($this->_ninjaboard_usergroup_id))
			{
				$original = parent::__get($column);
				$table	= $this->getTable();
				$query	= $table->getDatabase()->getQuery();

				$gid = (version_compare(JVERSION,'1.6.0','ge') && !$this->gid) ? 1 : $this->gid;

				$query->select("joomla_map.ninjaboard_gid AS ninjaboard_usergroup_id")
					 	->from('ninjaboard_joomla_user_group_maps AS joomla_map')
					  	->where('joomla_map.joomla_gid', '=', (int)$gid)
					  	->where('joomla_map.ninjaboard_gid', '!=', 0)
					  	->limit(1);

				$ninjaboard_usergroup_id = $table->getDatabase()->select($query, KDatabase::FETCH_FIELD, 'ninjaboard_usergroup_id');

				if($this->id)
				{
					$query	= $table->getDatabase()->getQuery();
					$query->select("ninjaboard_map.ninjaboard_user_group_id AS ninjaboard_usergroup_id")
							->from('ninjaboard_user_group_maps AS ninjaboard_map')
							->where('ninjaboard_map.joomla_user_id', '=', $this->id);
				
					$groups = $table->getDatabase()->select($query, KDatabase::FETCH_FIELD_LIST);
					$ninjaboard_usergroup_id = $groups ? implode('|', $groups) : $ninjaboard_usergroup_id;
				}
				
				$this->_ninjaboard_usergroup_id = $ninjaboard_usergroup_id;
			}
			
			return $this->_ninjaboard_usergroup_id;
		}
		
		if(strpos($column, '_permissions') !== false)
		{
			$object = str_replace('_permissions', '', $column);
	    	if(!isset($this->_permissions[$object]))
			{
				$table	= $this->getService('com://admin/ninjaboard.database.table.assets');
				$query	= $this->getService('koowa:database.adapter.mysqli')->getQuery();
				$gids	= explode('|', $this->ninjaboard_usergroup_id);

				//If super admin, then the permission level is always 3
				if($this->gid == 25 || $this->gid == 8) return $this->_permissions[$object] = 3;
				
				$query
						->select('tbl.level')
						->from('ninjaboard_assets AS tbl')
						->order('tbl.level', 'DESC')
						->limit(1);

				$where = array();
				foreach($gids AS $gid)
				{
					$where[] = 'com_ninjaboard.usergroup.'.$gid.'.'.$object;
				}
				if($where) $query->where('tbl.name', 'in', $where);

				$this->_permissions[$object] = $table->getDatabase()->select($query, KDatabase::FETCH_FIELD);
			}

	    	return $this->_permissions[$object];
		}
		
		return parent::__get($column);
    }
}