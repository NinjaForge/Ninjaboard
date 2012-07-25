<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Joomla Usergroups maps table
 *
 * This table don't follow koowas naming conventions for the primary key.
 * Thus we hardcode it in.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTableJoomlausergroupmaps extends KDatabaseTableDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		$options->name		= 'ninjaboard_joomla_user_group_maps';
		$options->identity_column	= 'joomla_gid';
		
		parent::__construct($options);
	}

	/**
	 * Table insert method
	 *
	 * @param  object	A KDatabaseRow object
	 * @return boolean  TRUE if successfull, otherwise false
	 */
	public function insert( KDatabaseRowAbstract $row )
	{
		$query = $this->_database->getQuery();

		$query->where('tbl.joomla_gid', '=', $row->id);

		if($this->count($query))
		{
			$asset = $this->select($query, KDatabase::FETCH_ROW);
			$this->delete($asset);
//			$asset->ninjaboard_gid = $row->ninjaboard_gid;

			return parent::insert($row);
		}

		return parent::insert($row);
	}
}