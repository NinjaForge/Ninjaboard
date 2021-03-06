<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard forums table
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseTableForums extends KDatabaseTableDefault
{
	/**
	 * The lenght of which all path parts should be applied padding
	 *
	 * @var integer
	 */
	protected $_pad;

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$config->name    = 'ninjaboard_forums';
		
		$nestable			  = 'ninja:database.behavior.nestable';
		$orderable			  = 'com://admin/ninjaboard.database.behavior.orderable';
		//$configurable		  = $this->getService('com://admin/ninjaboard.behavior.configurable');
		$config->behaviors = array($orderable, $nestable/*, $configurable*/);
		$config->filters =  array(
			'params' => 'json'
		);
		
		parent::__construct($config);
		
		//@TODO change the default value to '/' in the table schema later.
		$fields = $this->getColumns();
		$fields['path']->default = '/';
	}
	
	public function  _initialize(KConfig $config)
	{
        $config->append(array(
            'behaviors' => array(
            	'ninja:database.behavior.nestable', 'com://admin/ninjaboard.database.behavior.orderable', 'lockable', 'orderable', 'sluggable'
            ),
            'column_map' => array(
                'locked_on'        => 'checked_out_time',
                'locked_by'        => 'checked_out',
            )
        ));

        parent::_initialize($config);
    }
	
	/**
	 * Table insert method
	 *
	 * @param  object	A KDatabaseRow object
	 * @return boolean  TRUE if successfull, otherwise false
	 */
	public function insert( KDatabaseRowInterface $row )
	{
		$this->_setLevel($row);

		//This is because we need behavior.nestable to set the right path, before we set the path_sort
		//row->getModified() returns a blank array after parent::update have run
		$modified = $row->getModified();

		//Make sure that the row is appended, not prepended to the list
		if(!in_array('ordering', $modified))
		{
			$row->ordering = $this->count(array()) + 1;
			$modified[] = 'ordering';
		}

		$result = parent::insert($row);

		$this->_setSort($row, $modified);

		return $result;
	}

	/**
	 * Table update method
	 *
	 * @param  object	A KDatabaseRow object
	 * @return boolean  TRUE if successfull, otherwise false
	 */
	public function update( KDatabaseRowInterface $row)
	{
		$this->_setLevel($row);

		//This is because we need behavior.nestable to set the right path, before we set the path_sort
		//row->getModified() returns a blank array after parent::update have run
		$modified = $row->getModified();

		$result = parent::update($row);
		
		$this->_setSort($row, $modified);

		return $result;
	}
	
	/**
	 * Table maintenance method
	 *
	 * Runs a maintenance procedure
	 */
	public function maintenance()
	{
	    // On some systems, and especially if we're upgrading from something older than Ninjaboard 1.0.7 this routine may time out
	    @set_time_limit(300);
	
		foreach($this->select(array('level' => 0)) as $forum)
		{
			$this->_setLevel($forum);
			$forum->save();
		}

		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
															->select('*')
															->select("CONCAT(path, ninjaboard_forum_id, '/') AS path_temp")
															->where('path_sort', '=', '', 'or')
															->where('path_sort_title', '=', '', 'or')
															->where('path_sort_ordering', '=', '', 'or')
															->order('path_temp', 'asc');
		foreach($this->select($query, KDatabase::FETCH_ROWSET) as $forum)
		{
			$this->_setSort($forum, array('path', 'ordering'));
		}
		
		$total = $this->getService('com://admin/ninjaboard.model.forums')->getTotal();

		
		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()->select('COUNT(DISTINCT ordering)');
		$count = $this->select($query, KDatabase::FETCH_FIELD);
		//If the distinct count on ordering is different from the total, then we need to adjust the ordering
		if($count != $total)
		{
			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
																->select('*')
																->select("CONCAT(path, ninjaboard_forum_id, '/') AS path_temp")
																->order('path_temp', 'asc');
			
			//Reset the ordering column before setting the order paths
			$forum = $this->select($query, KDatabase::FETCH_ROW);
			if($forum->isOrderable()) $forum->reorder();

			foreach($this->select($query, KDatabase::FETCH_ROWSET) as $forum)
			{
				$this->_setSort($forum, array('path', 'ordering'));
			}
		}
		
		foreach($this->select(array('alias' => '')) as $forum)
		{
			$forum->alias = $this->getService('com://admin/ninjaboard.filter.slug')->sanitize($forum->title);
			$forum->save();
		}
		
		//If there are more than 10 forums, there's a high risk we need to pad the path integers
		if($total >= 10)
		{
			//If there are no subforums, then there's no need to pad paths
			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()->where('path', '!=', '/');
			
			if($this->count($query))
			{
				//Get the first parent, so we can check if it needs padding
				$query	= $this->getService('koowa:database.adapter.mysqli')->getQuery()
														->select('path_sort')
														->order('ninjaboard_forum_id', 'asc');
				$last	= trim($this->select($query, KDatabase::FETCH_FIELD), '/');
				$pad	= strlen($last);
				
				if($pad < $this->_getPad())
				{
					$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
																		->select('*')
																		->select("CONCAT(path, ninjaboard_forum_id, '/') AS path_temp")
																		->order('path_temp', 'asc');
					foreach($this->select($query, KDatabase::FETCH_ROWSET) as $forum)
					{
						$this->_setSort($forum, array('path', 'ordering'));
					}
				}
			}
		}

		return true;
	}
	
	/**
	 * Sets row level based on path
	 *
	 * @param  object	A KDatabaseRow object
	 * @return KDatabaseRowset
	 */
	private function _setLevel(KDatabaseRowInterface $row)
	{
		$row->level = substr_count($row->path, '/');
	
		return $row;
	}
	
	/**
	 * Sets the sort columns
	 *
	 * @param  object	A KDatabaseRow object
	 * @param  array	Array over modified columns
	 * @return KDatabaseRowset
	 */
	private function _setSort(KDatabaseRowInterface $row, array $modified = array())
	{
		//Only run if title, path or ordering is changed
		if(
			!in_array('title', $modified) &&
			!in_array('path', $modified) &&
			!in_array('ordering', $modified
		)) {
			return;
		}
		//Prevents eternal recursing
		if(
			in_array('path_sort', $modified) ||
			in_array('path_sort_title', $modified) ||
			in_array('path_sort_ordering', $modified
		)) {
			return;
		}
		
		$parents	= array_filter(explode('/', $row->path));
		$rows		= array($row);
		$ids		= array('/'.$row->id.'/');
		$titles		= array('/'.$row->title.'/');
		
		$query = $this->getDatabase()->getQuery()
													->select('*')
													->select("CONCAT(path, ninjaboard_forum_id, '/') AS path_temp")
													->where('path', 'LIKE', '%/'.$row->id.'/%', 'or')
													->order('path_temp', 'asc');

		if($parents) $query->where('ninjaboard_forum_id', 'in', $parents, 'or');
		foreach($this->select($query, KDatabase::FETCH_ROWSET) as $child)
		{
			$rows[]			= $child;
			$ids[]			= '/'.$child->id.'/';
			$titles[]		= '/'.$child->title.'/';
		}

		foreach($rows as $item)
		{
			//Set default values before padding
			$item->path_sort			= $item->path.$item->id.'/';
			$item->path_sort_title		= str_replace($ids, $titles, $item->path_sort);
			
			//Pad the new values since they sort by text
			$this->_pad($item, 'path_sort');
			
			$item->save();
		}
		
		//If ordering changed, then we need to update all forums
		if(in_array('ordering', $modified, true) || in_array('path', $modified, true))
		{
			$rows		= array();
			$ids		= array();
			$orderings	= array();

			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
														->select('*')
														->select('ordering AS tmp_ordering')
														->select("CONCAT(path, ninjaboard_forum_id, '/') AS path_temp")
														->order('path_temp', 'desc');

			foreach($this->select($query, KDatabase::FETCH_ROWSET) as $child)
			{
				$rows[]					= $child;
				$searches[$child->id]	= $child->tmp_ordering;
			}

			foreach($rows as $item)
			{
				//Set default values before padding
				$parts = array_filter(explode('/', $item->path_temp));
				foreach($parts as $key => $id)
				{
					if(isset($searches[$id])) $parts[$key] = $searches[$id];
				}
				$item->path_sort_ordering	= '/'.implode('/', $parts).'/';

				//Pad the new values since they sort by text
				$this->_pad($item, 'path_sort_ordering', 'ordering');

				$item->save();
			}
		}
	}

	/**
	 * Convenience method for padding integers so their sortable by text
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  KDatabaseRowInterface
	 * @param  string					Name of the column you want to pad
	 * @param  string					Name of column to check pad length	Defaults to ninjaboard_forum_id
	 * @return return type
	 */
	protected function _pad(KDatabaseRowInterface $row, $column, $pad = 'ninjaboard_forum_id')
	{
		//Pad the values
		$path	= '/';
		$parts	= array_filter(explode('/', $row->$column));
		foreach($parts as $part)
		{
			$path .= str_pad((int) $part, $this->_getPad($pad), 0, STR_PAD_LEFT).'/';
		}
		$row->$column = $path;
		
		return $this;
	}

	/**
	 * Get the path padding
	 *
	 * @param  string					Name of column to check pad length	Defaults to ninjaboard_forum_id
	 * @return integer
	 */
	protected function _getPad($pad = 'ninjaboard_forum_id')
	{
		$cache = '_pad_'.$pad;
		if(!isset($this->$cache))
		{
			//Get the last row id, so we know how much we need to pad
			$query	= $this->getService('koowa:database.adapter.mysqli')->getQuery()
													->select($pad)
													->order($pad, 'desc');
			$this->$cache = strlen($this->select($query, KDatabase::FETCH_FIELD));
		}
	
		return $this->$cache;
	}
}