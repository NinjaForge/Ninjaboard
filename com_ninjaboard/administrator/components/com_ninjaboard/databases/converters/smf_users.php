<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: smf_users.php 2462 2011-10-11 22:55:40Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersPhpbb
 *
 * Sync SMF users with Ninjaboard
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersSmf_users extends ComNinjaboardDatabaseConvertersSmf
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
		$tables = array(
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
			'messages' => array(
			    'name' => 'messages',
				'options' => array(
				    'name' => 'ninjaboard_messages'
				),
				'columns' => array('created_by'),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()->select(array(
				    '*',
				    'ninjaboard_message_id AS id'
				))
			),
			'message_recipients' => array(
			    'name' => 'message_recipients',
				'options' => array(
				    'name' => 'ninjaboard_message_recipients'
				),
				'columns' => array('user_id'),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()->select(array(
				    '*',
				    "CONCAT(ninjaboard_message_id, '-', user_id) AS id"
				))
			),
			'people' => array(
			    'name' => 'people',
				'options' => array(
				    'name' => 'ninjaboard_people'
				),
				'columns' => array('temporary_id', 'id', 'ninjaboard_person_id'),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()->select(array(
				    '*',
				    'ninjaboard_person_id AS id'
				))
				
			),
		);

		//This returns false if the import is big enough to be done in steps.
		//So we need to stop the importing in this step, in order for it to initiate
		if($this->importData($tables, 'smf') === false) return $this;

		$ids = array();
		foreach($this->data as $table => $rows)
		{
			if(!isset($tables[$table])) continue;
			foreach($rows as $id => $row)
			{
				foreach($tables[$table]['columns'] as $column)
				{
				    if($table == 'people' && ($column == 'id' || $column == 'ninjaboard_person_id')) {
				        $this->data[$table][$id][$column] = $row[$column] = $row['temporary_id'];
				    } 
					if($row[$column]) $ids[$row[$column]] = $row[$column];
				}
			}
		}
		
		//Connect the koowa:database to the SMF database
		$this->setDatabaseConnection();
		

		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
															->select(array(
																'*',
																'ID_MEMBER AS id',
																'memberName AS username',
																'realName AS name',
																'FROM_UNIXTIME(dateRegistered) AS registerDate',
																'FROM_UNIXTIME(lastLogin) AS lastvisitDate',
																'passwd AS password',
																'emailAddress AS email'
															))
															->where('ID_MEMBER', 'in', $ids)
															->from('members');
		$users = $this->getService('koowa:database.adapter.mysqli')->select($query, KDatabase::FETCH_ARRAY_LIST, 'id');
		/*
		$users = $this->getService('com://admin/smf_converter.database.table.members', array(
			'name' => 'members',
			'identity_column' => 'id'
		))->select($query, KDatabase::FETCH_ROWSET);
		//*/
		
		//Reconnect the koowa:database to the Joomla! database
		$this->resetDatabaseConnection();
		
		$table		= $this->getService('com://admin/smf_converter.database.table.users', array(
			'name' => 'users',
			'identity_column' => 'id'
		));
		$columns    = $table->getColumns(true);
		$db			= $this->getService('koowa:database.adapter.mysqli');
		$user_query	= $this->getService('koowa:database.adapter.mysqli')->getQuery()->select('id')->from('users');
		$utc		= new DateTimeZone('UTC');
		$timezone	= new DateTimeZone(date('e'));
		
		foreach($users as $user)
		{
			$query = clone $user_query;
			$query
					->where('username', '=', $user['username'])
					->where('email', '=', $user['email']);
			$id = $db->select($query, KDatabase::FETCH_FIELD);
			
			//Insert user if not existing in the joomla table already
			if(!$id)
			{
				$registerDate						= new DateTime($user['registerDate'], $timezone);
				$registerDate->setTimezone($utc);
				$user['registerDate']				= $registerDate->format('Y-m-d H:i:s');

				$lastvisitDate						= new DateTime($user['lastvisitDate'], $timezone);
				$lastvisitDate->setTimezone($utc);
				$user['lastvisitDate']				= $lastvisitDate->format('Y-m-d H:i:s');
				
				if($user['ID_GROUP'] == 1)
				{
					$user['usertype'] = 'Super Administrator';
					$user['gid']	  = 25;
				}
				else
				{
					$user['usertype'] = 'Registered';
					$user['gid']      = 18;
				}
				$user['params'] = "editor=\ntimezone=".$user['timeOffset'];

				//Filter the data and remove unwanted columns
				$data = array_intersect_key($user, $columns);
				
				//We can't have a id on this column or we'll risk primary key duplicates
				unset($data['id']);

				$id = $db->insert('users', $data);
				
				//Update the Joomla core acl tables
				$core_acl_aro = array(
					'section_value'   =>       'users',
					'value'           =>        $id,
					'order_value'     =>       '0',
					'name'            =>        $user['username'],
					'hidden'          =>        '0'
			    );
			    try {
			    	$core_acl_aro_id = $db->insert('core_acl_aro', $core_acl_aro);
			    } catch(KDatabaseException $e) {
			    	continue;
			    }
			    
			    
			    $core_acl_groups_aro_map = array(
				'aro_id'          =>        $core_acl_aro_id,
				'group_id'        =>        $user['gid']

			    );
			    try {
			    	$db->insert('core_acl_groups_aro_map', $core_acl_groups_aro_map);
			    } catch(KDatabaseException $e) {
			    	continue;
			    }
			}
			
			$ids[$user['id']] = $id;

			
		}
		
		$identifier = new KServiceIdentifier('com://admin/ninjaboard.database.table.default');
		foreach($this->data as $table => $rows)
		{
		    //People table is a special case because we're changing a primary key, avoid it
		    //if($table == 'people') continue;
		
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
		
		
		return $this;
	}
}