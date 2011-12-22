<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardDatabaseRowsetJoomlausergroupmaps extends KDatabaseRowsetDefault
{
	/**
	 * Add a row in the rowset
	 * 
	 * Since joomla user group maps at the time being uses 0 as the value of unregistered users,
	 * we override this method as Koowa does not allow 0 keys to be inserted
	 *
	 * @param  object   A KDatabaseRow object to be inserted
	 * @return KDatabaseRowsetAbstract
	 */
	public function insert(KDatabaseRowInterface $row)
	{
	    if(isset($this->_identity_column)) {
	        $handle = $row->{$this->_identity_column};
	    } else {
	        $handle = $row->getHandle();
	    }
	    
        $this->_object_set->offsetSet($handle, $row);
        
        //Add the columns, only if they don't exist yet
        $columns = array_keys($row->toArray());
        foreach($columns as $column)
        {
            if(!in_array($column, $this->_columns)) {
                $this->_columns[] = $column;
            }
        }
	    
	    return $this;
	}
}