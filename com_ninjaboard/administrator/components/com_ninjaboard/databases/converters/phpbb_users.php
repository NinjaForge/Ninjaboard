<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: phpbb_users.php 2462 2011-10-11 22:55:40Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersPhpbb
 *
 * Sync phpBB3 users with Ninjaboard
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersPhpbb_users extends ComNinjaboardDatabaseConvertersPhpbb
{
	/**
	 * Overload ComNinjaboardDatabaseConvertersPhpbb::$_layout
	 *
	 * @var string|boolean
	 */
	protected $_layout = false;

	/**
	 * Boolean deciding wether the converter needs an UI button or not
	 *
	 * @var boolean
	 */
	public $button = false;

	/**
	 * Import the sample content
	 *
	 * @return $this
	 */
	public function convert()
	{
		//*/
        // jos_ninjaboard_people, jos_ninjaboard_posts, jos_ninjaboard_usergroup_maps, 
		$tables = array(
			/*array(
			    'name' => 'people',
				'options' => array(),
				'columns' => array('ninjaboard_person_id'),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()->select(array(
				    '*',
				    'ninjaboard_person_id AS id'
				))
			),*/
			'attachments' => array(
			    'name' => 'attachments',
				'options' => array(
				    'name' => 'ninjaboard_attachments'
				),
				'columns' => array('joomla_user_id'),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()->select(array(
				    '*',
				    'ninjaboard_attachment_id AS id'
				))
			),
			'posts' => array(
			    'name' => 'posts',
				'options' => array(
				    'name' => 'ninjaboard_posts'
				),
				'columns' => array('created_by', 'modified_by'),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()->select(array(
				    '*',
				    'ninjaboard_post_id AS id',
				    'created_user_id AS created_by',
				    'modified_user_id AS modified_by'
				))
			),
		);

		//This returns false if the import is big enough to be done in steps.
		//So we need to stop the importing in this step, in order for it to initiate
		if($this->importData($tables, 'phpbb') === false) return $this;

		$ids = array();
		foreach($this->data as $table => $rows)
		{
			if(!isset($tables[$table])) continue;
			foreach($rows as $row)
			{
				foreach($tables[$table]['columns'] as $column)
				{
					if($row[$column]) $ids[$row[$column]] = $row[$column];
				}
			}
		}

		//Connect the koowa:database to the phpBB3 database
		$this->setDatabaseConnection();
		
		$users = $this->getService('com://admin/phpbb.database.table.users', array(
			'name' => 'users',
			'identity_column' => 'user_id'
		))->select(array($ids));

		//Get the avatars gallery path
		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
															->select('config_value')
															->from('config')
															->where('config_name', '=', 'avatar_gallery_path');
		$gallery  = $this->getService('koowa:database.adapter.mysqli')->select($query, KDatabase::FETCH_FIELD);
		
		//Get the avatars uploads salt
		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
															->select('config_value')
															->from('config')
															->where('config_name', '=', 'avatar_salt');
		$prefix  = $this->getService('koowa:database.adapter.mysqli')->select($query, KDatabase::FETCH_FIELD);
		
		//Get the avatars uploads path
		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
															->select('config_value')
															->from('config')
															->where('config_name', '=', 'avatar_path');
		$upload  = $this->getService('koowa:database.adapter.mysqli')->select($query, KDatabase::FETCH_FIELD);
		$upload .= '/'.$prefix.'_';
		
		//Reconnect the koowa:database to the Joomla! database
		$this->resetDatabaseConnection();
		
		$db = $this->getService('koowa:database.adapter.mysqli');
		
		foreach($users as $user)
		{
			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()->select('id')->from('users');
			$ids[$user->id] = $db->select($query->where('username', '=', $user->username_clean)->where('email', '=', $user->user_email), KDatabase::FETCH_FIELD);

			//We only know how to deal with 3 avatar types
			if(!in_array($user->user_avatar_type, array(1, 2, 3))) continue;

			//Remote avatars
			if($user->user_avatar_type == 2)
			{
				$user->avatar = $user->user_avatar;
				continue;
			}

			//Uploaded avatars
			if($user->user_avatar_type == 1)
			{
				$parts	= explode('_', $user->user_avatar);
				$from 	= $upload.$parts[0].'.'.JFile::getExt($user->user_avatar);
			}

			//Gallery avatars
			if($user->user_avatar_type == 3)
			{
				$from = $gallery.'/'.$user->user_avatar;
			}
			
			$from	= JPATH_ROOT.'/'.$this->getPath().'/'.$from;
			$file	= basename($from);
			$avatar	= '/media/com_ninjaboard/images/avatars/'.$ids[$user->id].'/'.$file;

			//Don't do anything if avatar don't exist
			if(!JFile::exists($from)) continue;
			
			JFile::copy($from, JPATH_ROOT.$avatar);
			
			$user->avatar = $avatar;
		}
		
		$identifier = new KServiceIdentifier('com://admin/ninjaboard.database.table.default');
		foreach($this->data as $table => $rows)
		{
			if(!isset($tables[$table])) continue;
			foreach($rows as $i => $row)
			{
				foreach($tables[$table]['columns'] as $column)
				{
					if($row[$column]) $rows[$i][$column] = $ids[$row[$column]];
				}
			}
			$identifier->name = $table;
			$table = $this->getService($identifier);

			$this->update($rows, $table);
		}
		
		$table = $this->getService('com://admin/ninjaboard.database.table.people', array('column_map' => array(
			'user_posts' => 'posts',
			'user_sig' => 'signature'
		)));
		if(KRequest::get('post.offset', 'int', 0) < 1) $this->_truncateTable($table);
					
		$columns   = $table->getColumns(true);
		foreach($users as $user)
		{	
			/*	
			$this->row = $this->getService($identifier)
						->getItem()
						->setData($this->row)
						->save(); 
			//*/
			$user->set('id', $ids[$user->id]);

			//Filter out any extra columns.
			$data = array_intersect_key($user->getData(), $columns);
			
			//Get the data and apply the column mappings
			$data = $table->mapColumns($data);
			
			//@TODO find a better way to encode KConfig to json to avoid fatal errors
			/*foreach($data as &$val)
			{
				if(is_a($val, 'KConfig')) $val = $val->toArray();
				if(is_array($val) || is_object($val)) $val = json_encode($val);
				
			}*/
			try {
				$table->getDatabase()->insert($table->getBase(), $data);
			} catch(KDatabaseException $e) {
				//Do nothing, just mute the exception
			}
		}

		return $this;
	}
}