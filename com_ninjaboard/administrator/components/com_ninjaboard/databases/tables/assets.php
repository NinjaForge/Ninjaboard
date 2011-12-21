<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: assets.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	 	http://ninjaforge.com
 */

/**
 * Ninjaboard permissions table
 *
 * This is where 
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTableAssets extends KDatabaseTableDefault
{
	/**
	 * Table insert method
	 *
	 * @param  object	A KDatabaseRow object
	 * @return boolean  TRUE if successfull, otherwise false
	 */
	public function insert( KDatabaseRowAbstract $row )
	{
		$query = $this->_database->getQuery();

		$query->where('tbl.name', '=', $row->name, 'or');

		if($this->count($query))
		{
			$asset = $this->select($query, KDatabase::FETCH_ROW);
			$data  = array_merge($asset->getData(), array_filter($row->getData()));

			return parent::update($asset->setData($data));
		}

		return parent::insert($row);
	}
}