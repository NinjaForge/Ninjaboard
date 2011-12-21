<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: phpbb_users.php 1357 2011-01-10 18:45:58Z stian $
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
			/*'people' => array(
				'options' => array(),
				'columns' => array('ninjaboard_person_id'),
				'query' => KFactory::tmp('lib.koowa.database.query')
			),*/
			'attachments' => array(
				'options' => array(),
				'columns' => array('joomla_user_id'),
				'query' => KFactory::tmp('lib.koowa.database.query')
			),
			'posts' => array(
				'options' => array(),
				'columns' => array('created_by', 'modified_by'),
				'query' => KFactory::tmp('lib.koowa.database.query')
			),
		);

		$offset = KRequest::get('post.offset', 'int', false);
		foreach ( $tables as $name => $table )
		{
			if($offset === false)
			{
				$this->data[$name] = KFactory::get('admin::com.ninjaboard.database.table.'.$name)->count(clone $table['query']);

				continue;
			}
			elseif ($offset !== false)
			{
				$query = KFactory::tmp('lib.koowa.database.query')
					//->select($table['columns'])
					->limit(KRequest::get('post.limit', 'int', 1000), $offset);
			}
			
			$this->data[$name] = KFactory::get('admin::com.ninjaboard.database.table.'.$name)->select($query);
			
		}

		if($offset === false)
		{
			$total = array_reduce($this->data, 'max');
			$steps = floor($total / KRequest::get('post.limit', 'int', 1000));
			if($steps > 0) 
			{
				echo json_encode(array('splittable' => true, 'total' => $total, 'steps' => $steps));
				return $this;
			}
			else
			{
				echo json_encode(array('splittable' => false));
				foreach ( $tables as $name => $table )
				{
					$this->data[$name] = KFactory::get('admin::com.ninjaboard.database.table.'.$name)->select(clone $table['query']);
				}
			}
		}
		
		$ids = array();
		foreach($this->data as $table => $rows)
		{
			if(!isset($tables[$table])) continue;
			foreach($rows as $row)
			{
				foreach($tables[$table]['columns'] as $column)
				{
					if($row->$column) $ids[$row->$column] = $row->$column;
				}
			}
		}
		
		//Connect the lib.koowa.database to the phpBB3 database
		$this->setDatabaseConnection();
		
		$users = KFactory::get('admin::com.phpbb.database.table.users', array(
			'name' => 'users',
			'identity_column' => 'user_id'
		))->select(array($ids));
		
		//Get the avatars gallery path
		$query = KFactory::tmp('lib.koowa.database.query')
															->select('config_value')
															->from('config')
															->where('config_name', '=', 'avatar_gallery_path');
		$gallery  = KFactory::get('lib.koowa.database')->select($query, KDatabase::FETCH_FIELD);
		
		//Get the avatars uploads salt
		$query = KFactory::tmp('lib.koowa.database.query')
															->select('config_value')
															->from('config')
															->where('config_name', '=', 'avatar_salt');
		$prefix  = KFactory::get('lib.koowa.database')->select($query, KDatabase::FETCH_FIELD);
		
		//Get the avatars uploads path
		$query = KFactory::tmp('lib.koowa.database.query')
															->select('config_value')
															->from('config')
															->where('config_name', '=', 'avatar_path');
		$upload  = KFactory::get('lib.koowa.database')->select($query, KDatabase::FETCH_FIELD);
		$upload .= '/'.$prefix.'_';
		
		//Reconnect the lib.koowa.database to the Joomla! database
		$this->resetDatabaseConnection();
		
		$db = KFactory::get('lib.koowa.database');
		
		foreach($users as $user)
		{
			$query = KFactory::tmp('lib.koowa.database.query')->select('id')->from('users');
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
		
		foreach($this->data as $table => $rows)
		{
			if(!isset($tables[$table])) continue;
			KFactory::get($rows->getTable())->getCommandChain()->disable();
			foreach($rows as $row)
			{
				foreach($tables[$table]['columns'] as $column)
				{
					if($row->$column) $row->$column = $ids[$row->$column];
				}
				$row->save();
			}
			KFactory::get($rows->getTable())->getCommandChain()->enable();
		}
		
		$table = KFactory::get('admin::com.ninjaboard.database.table.people', array('column_map' => array(
			'user_posts' => 'posts',
			'user_sig' => 'signature'
		)));
		if(KRequest::get('post.offset', 'int', 0) < 1) $this->_truncateTable($table);
					
		foreach($users as $user)
		{	
			/*	
			$this->row = KFactory::tmp($identifier)
						->getItem()
						->setData($this->row)
						->save(); 
			//*/
			$user->set('id', $ids[$user->id]);

			//Filter the data and remove unwanted columns
			$data = $table->filter($user->getData(), true);
			
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
		
		//die('<pre>'.print_r($ids, true).'</pre>');
		//die('<pre>'.print_r($users, true).'</pre>');
		
		return $this;
	}
}