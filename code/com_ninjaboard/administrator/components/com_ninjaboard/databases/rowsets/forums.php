<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardDatabaseRowsetForums extends KDatabaseRowsetDefault
{
	/**
	 * Recounts all the rows in the rowset
	 *
	 * @return boolean  TRUE if successfull, otherwise false
	 */
	public function recount()
	{
		foreach ($this as $i => $row) {
		    $row->recount();
		}
		
		return true;
	}
}