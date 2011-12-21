<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: joomlausergroupmaps.php 1611 2011-02-27 01:07:31Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Joomla Usergroup maps model
 *
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelJoomlausergroupmaps extends ComDefaultModelDefault
{
	/**
	 * Get guest item
	 */
	public function getGuest()
	{
		// Get the data if it doesn't already exist
        if (!isset($this->_default))
        {
        
        	$table = $this->getTable();
        	$query = $table->getDatabase()->getQuery();

			$query->select('tbl.ninjaboard_gid AS gid')->where('joomla_gid', '=', 0);
	      	
        	$this->_default = $table->select($query, KDatabase::FETCH_ROW);
        }

        return $this->_default;
	}
}