<?php defined( 'KOOWA' ) or die( 'Restricted access' );
 /**
 * @version		$Id: forum.php 1787 2011-04-12 23:38:17Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardDatabaseRowForum extends ComNinjaboardDatabaseRowParam
{
	/**
	 * Permissions cache
	 *
	 * @var array
	 */
	private $_permissions = array();

	/**
	 * Recounts stats like posts, topics and latest post
	 *
	 * @return boolean  TRUE if successfull, otherwise false
	 */
	public function recount()
	{
		//Getting all subforum ids, used in following queries
		//@TODO this needs to be optimized
		$database = KFactory::get('lib.koowa.database.adapter.mysqli');
		$table = $this->getTable();
		$query = KFactory::tmp('lib.koowa.database.query')
					->select('ninjaboard_forum_id')
					->where('path', 'like', '%/'.$this->id.'/')
					->from('ninjaboard_forums');
		
		$ids = array_merge(array($this->id), $database->select($query, KDatabase::FETCH_FIELD_LIST));

		$query = KFactory::tmp('lib.koowa.database.query')
					->select('ninjaboard_topic_id')
					->where('forum_id', 'in', $ids)
					->from('ninjaboard_topics');
		$topics = $database->select($query, KDatabase::FETCH_FIELD_LIST);
		$this->topics = count($topics);
		
		//If not topics left, don't do any more queries
		if(!$this->topics) {
			$this->posts = 0;
			$this->last_post_id = 0;
			
			return $this->save();
		}
		
		$query = KFactory::tmp('lib.koowa.database.query')
					->select('ninjaboard_post_id')
					->where('ninjaboard_topic_id', 'in', $topics)
					->from('ninjaboard_posts');
		$posts = $database->select($query, KDatabase::FETCH_FIELD_LIST);
		$this->posts = count($posts);
		
		$query = KFactory::tmp('lib.koowa.database.query')
					->select('ninjaboard_post_id')
					->where('ninjaboard_topic_id', 'in', $topics)
					->from('ninjaboard_posts')
					->order('created_time', 'desc');
		$this->last_post_id = $database->select($query, KDatabase::FETCH_FIELD);
		
		return $this->save();
	}
	
	/**
     * Retrieve row field value
     *
     * @param  	string 	The column name.
     * @return 	string 	The corresponding column value.
     */
    public function __get($column)
    {
    	if(strpos($column, '_permissions') === false) return parent::__get($column);

    	$object = str_replace('_permissions', '', $column);
    	if(!isset($this->_permissions[$object]))
		{
			$table	= KFactory::get('admin::com.ninjaboard.database.table.assets');
			$query	= KFactory::tmp('lib.koowa.database.query');
			$me		= KFactory::get('admin::com.ninjaboard.model.people')->getMe();
			$gids	= explode('|', $me->ninjaboard_usergroup_id);

			//If super admin, then the permission level is always 3
			if($me->gid == 25) return $this->_permissions[$object] = 3;
			
			$query
					->select('IFNULL(tbl.level, '.(int)$me->{$object.'_permissions'}.') AS level')
					->from('ninjaboard_assets AS tbl')
					->order('tbl.level', 'DESC')
					->limit(1);
			
			$where = array();
			foreach($gids as $gid)
			{
				$where[] = 'com_ninjaboard.forum.'.(int)$this->id.'.'.$gid.'.'.$object;
			}
			if($where) $query->where('tbl.name', 'in', $where);

			$result = $table->getDatabase()->select($query, KDatabase::FETCH_FIELD);
			$this->_permissions[$object] = is_numeric($result) ? $result : $me->{$object.'_permissions'};
		}
    	
    	return $this->_permissions[$object];
    }
}