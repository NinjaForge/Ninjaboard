<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: legacy.php 2461 2011-10-11 22:32:21Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Imports data from Ninjaboard 0.5.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersLegacy extends ComNinjaboardDatabaseConvertersAbstract
{
	/**
	 * This converter is able to run in steps
	 *
	 * @var boolean
	 */
	public $splittable = true;

	/**
	 * Import the sample content
	 *
	 * @return $this
	 */
	public function convert()
	{
		$tables = array(
			array(
				'name' => 'attachments',
				'options' => array(
					'name' => 'ninjaboard_attachments_backups',
					'identity_column' => 'id'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'id',
								'id_user AS joomla_user_id',
								'id_post AS post',
								'file_name AS name',
								'file_name AS file'
							))
			),
			array(
				'name' => 'forums',
				'options' => array(
					'name' => 'ninjaboard_categories_backups',
					'identity_column' => 'id'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'(id + (SELECT MAX(id) FROM #__ninjaboard_forums_backups)) AS id',
								'(SELECT SUM(posts) FROM #__ninjaboard_forums_backups) AS posts',
								'(SELECT SUM(topics) FROM #__ninjaboard_forums_backups) AS topics',
								'(SELECT MAX(id_last_post) FROM #__ninjaboard_forums_backups) AS last_post_id',
								'name AS title',
								'published AS enabled',
								'ordering',
								"'/' AS path"
							))
			),
			array(
				'name' => 'forums',
				'options' => array(
					'name' => 'ninjaboard_forums_backups',
					'identity_column' => 'id'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'id',
								'description',
								'locked',
								'ordering',
								'topics',
								'posts',
								'id_last_post AS last_post_id',
								//'id AS ninjaboard_forum_id',
								'name AS title',
								'state AS enabled',
								"CONCAT('/', (id_cat + (SELECT MAX(id) FROM #__ninjaboard_forums_backups)), '/') AS path"
							))
			),
			array(
				'name' => 'usergroups',
				'options' => array(
					'name' => 'ninjaboard_groups_backups',
					'identity_column' => 'id'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'id',
								'name AS title'
							))
			),
			
			//Permissions for admins
			$this->buildAclQueryColumn('FORUM', 3, 4),
			//Permissions for admins and mods
			$this->buildAclQueryColumn('TOPIC', 3, 2, '>'),
			$this->buildAclQueryColumn('POST', 3, 2, '>'),
			$this->buildAclQueryColumn('ATTACHMENT', 3, 2, '>'),
			//Permissions for regular users
			$this->buildAclQueryColumn('TOPIC', 1, 3, '<'),
			$this->buildAclQueryColumn('POST', 1, 3, '<'),
			$this->buildAclQueryColumn('ATTACHMENT', 1, 3, '<'),
			//Only admins have permissions beyond Has Access on forums
			$this->buildAclQueryColumn('FORUM', 1, 4, '<'),
			
			array(
				'name' => 'posts',
				'options' => array(
					'name' => 'ninjaboard_posts_backups',
					'identity_column' => 'id'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'*',
								'date_post AS created_on',
								'date_last_edit AS modified_on',
								'id_edit_by AS modified_by',
								'ip_poster AS user_ip',
								'id_topic AS ninjaboard_topic_id',
								'id_user AS created_by',
								'IFNULL(guest.guest_name, tbl.guest_name) AS guest_name'
							))
							->join('left', 'ninjaboard_posts_guests_backups AS guest', 'guest.id_post = tbl.id')
			),
			array(
				'name' => 'topics',
				'options' => array(
					'name' => 'ninjaboard_topics_backups',
					'identity_column' => 'id'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'*',
								'id_forum AS forum_id',
								'views AS hits',
								'type AS topic_type_id',
								'id_first_post AS first_post_id',
								'id_last_post AS last_post_id',
							))
			),
			array(
				'name' => 'people',
				'options' => array(
					'name' => 'ninjaboard_users_backups',
					'identity_column' => 'id'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'*',
								'avatar_file AS avatar',
							))
			),
		);

		//This returns false if the import is big enough to be done in steps.
		//So we need to stop the importing in this step, in order for it to initiate
		if($this->importData($tables, 'ninjaboard_legacy') === false) return $this;

		//Move over file attachments
		if(isset($this->data['attachments']))
		{
			foreach($this->data['attachments'] as $id => $attachment)
			{
				$from	= JPATH_ROOT.'/components/com_ninjaboard/attachments/'.$attachment['file'];
				$file	= JPATH_ROOT.'/media/com_ninjaboard/attachments/'.$attachment['file'];
				
				//Don't do anything if avatar don't exist
				if(!JFile::exists($from)) continue;
				
				JFile::copy($from, $file);
			}
		}

		//Move over avatars
		if(isset($this->data['people']))
		{
			$path				= 'media/ninjaboard/avatars';
			$query				= $this->getService('koowa:database.adapter.mysqli')->getQuery()
																->select('avatar_settings')
																->from('ninjaboard_configs_backups')
																->order('default_config');
			$avatar_settings	= $this->getService('koowa:database.adapter.mysqli')->select($query, KDatabase::FETCH_FIELD);
			
			
			if($avatar_settings)
			{
				foreach(explode("\n", $avatar_settings) as $avatar_setting)
				{
					$parts = explode('=', $avatar_setting);
					if($parts[0] == 'avatar_path') $path = $parts[1];
				}
			}
			
			foreach($this->data['people'] as $id => $person)
			{
				$from	= JPATH_ROOT.'/'.$path.'/'.$person['avatar'];
				$file	= basename($from);
				$avatar	= '/media/com_ninjaboard/images/avatars/'.$person['id'].'/'.$file;

				//Don't do anything if avatar don't exist
				if(!JFile::exists($from)) continue;
				
				JFile::copy($from, JPATH_ROOT.$avatar);
				
				$this->data['people'][$id]['avatar'] = $avatar;
			}
		}
		
		//Clear cache folder so that avatars and attachments cache are cleared
		//@TODO this should only run once
		$cache = JPATH_ROOT.'/cache/com_ninjaboard/';
		if(JFolder::exists($cache)) JFolder::delete($cache);

		parent::convert();

		return $this;
	}

	/**
	 * Creates an acl select statement
	 *
	 * @param  string $name		The name of the asset
	 * @param  int    $level	The asset level
	 * @param  int    $role		The 0.5 role level
	 * @param  string $operator	The where statemenet operator
	 * @return array	Returns select statements
	 */
	private function buildAclQueryColumn($name, $level, $role, $operator = '=')
	{
		return array(
			'name' => 'assets',
			'options' => array(
				'name' => 'ninjaboard_groups_backups',
				'identity_column' => 'id'
			),
			'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
						->select(array(
							"(".rand()." + id) AS id",
							"CONCAT_WS('.', LOWER('COM_NINJABOARD'), LOWER('USERGROUP'), id, LOWER('$name')) AS name",
							"'$level' AS level"
						))
						->where('role', $operator, $role)
		);
	}

	/**
	 * Checks if the converter can convert
	 *
	 * This check will look for the backup tables that are generated when Ninjaboard 1.0 installs over 0.5
	 * If they are, then we can import from it.
	 *
	 * @return boolean
	 */
	public function canConvert()
	{
		try {
			return $this->getService('com://admin/ninjaboard.model.forums_backups')->getTotal();
		} catch(KDatabaseTableException $e) {
			return false;
		}
	}

	/**
	 * Sets another label than the default "Legacy"
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return 'Ninjaboard 0.5';
	}
}