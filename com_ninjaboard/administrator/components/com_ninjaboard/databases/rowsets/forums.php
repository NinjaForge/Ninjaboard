<?php defined( 'KOOWA' ) or die( 'Restricted access' );
 /**
 * @version		$Id: forums.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
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