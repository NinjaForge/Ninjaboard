<?php
/**
 * @package		Ninjaboard
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */ 

/**
 * Specializes the core orderable class in koowa to support hierarchies
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @package     Ninjaboard
 * @subpackage 	Behaviors
 */
class ComNinjaboardDatabaseBehaviorOrderable extends KDatabaseBehaviorOrderable
{
	/**
	 * Resets the order of all rows
	 *
	 * @return	KDatabaseTableAbstract
	 */
	public function reorder()
	{
		$table	= $this->getTable();
		$db 	= $table->getDatabase();
		$query 	= $db->getQuery();
		
		//Build the where query
		$this->_buildQueryWhere($query);

        if(array_key_exists('path', $table->getColumns())) {
            $order_by = 'path, ordering';
        } else {
            $order_by = 'ordering';
        }
		

		$db->execute("SET @order = 0");
		$db->execute(
			 'UPDATE #__'.$table->getBase().' '
			.'SET ordering = (@order := @order + 1) '
			.(string) $query.' '
			.'ORDER BY '.$order_by.' ASC'
		);

		return $this;
	}
}