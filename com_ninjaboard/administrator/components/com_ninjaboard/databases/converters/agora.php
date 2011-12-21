<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: agora.php 1587 2011-02-18 22:47:48Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersAgora
 *
 * Imports data from Agora.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersAgora extends ComNinjaboardDatabaseConvertersAbstract
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
				'name' => 'forums',
				'options' => array(
					'name' => 'agora_categories',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'(id + (SELECT MAX(id) FROM #__agora_forums)) AS id',
								'cat_name AS title',
								'disp_position AS ordering',
								'enable AS enabled',
								"'/' AS path"
							))
			),
			array(
				'name' => 'forums',
				'options' => array(
					'name' => 'agora_forums',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'id',
								'enable AS enabled',
								'forum_name AS title',
								'forum_desc AS description',
								'num_topics AS topics',
								'num_posts AS posts',
								'last_post_id',
								'disp_position AS ordering',
								"CONCAT('/', (cat_id + (SELECT MAX(id) FROM #__agora_forums)), '/') AS path",
								'parent_forum_id AS parent_id'
							))
			),
			array(
				'name' => 'posts',
				'options' => array(
					'name' => 'agora_posts',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'tbl.*',
								'topic.subject AS subject',
								'user.jos_id AS created_by',
								'poster_ip AS user_ip',
								'poster_email AS guest_email',
								'message AS text',
								//posted have tbl prefix to prevent MySQL exception
								'FROM_UNIXTIME(tbl.posted) AS created_on',
								'FROM_UNIXTIME(edited) AS mofidied_on',
								'edited_by AS modified_by',
								'topic_id AS ninjaboard_topic_id'
							))
							->join('left', 'agora_topics AS topic', 'topic.id = tbl.topic_id')
							->join('left', 'agora_users AS user', 'user.id = tbl.poster_id')
			),
			array(
				'name' => 'topics',
				'options' => array(
					'name' => 'agora_topics',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'tbl.*',
								'num_views AS hits',
								'num_replies AS replies',
								'closed AS locked',
								'(SELECT id FROM #__agora_posts WHERE topic_id = tbl.id ORDER BY posted ASC LIMIT 1) AS first_post_id'
							))
			),
			array(
				'name' => 'people',
				'options' => array(
					'name' => 'agora_users',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'jos_id AS id',
								'id AS agora_user_id',
								'signature',
								'num_posts AS posts'
							))
			),
			array(
				'name' => 'watches',
				'options' => array(
					'name' => 'agora_subscriptions',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'CONCAT(user_id, topic_id, forum_id, category_id) AS id',
								'jos_id AS created_by',
								'NOW() AS created_on',
								'1 AS subscription_type',
								'(category_id + (SELECT MAX(id) FROM #__agora_forums)) AS subscription_type_id',
							))
							->join('left', 'agora_users', 'user_id = id')
							->where('category_id', '>', 0)
			),
			array(
				'name' => 'watches',
				'options' => array(
					'name' => 'agora_subscriptions',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'CONCAT(user_id, topic_id, forum_id, category_id) AS id',
								'jos_id AS created_by',
								'NOW() AS created_on',
								'1 AS subscription_type',
								'forum_id AS subscription_type_id',
							))
							->join('left', 'agora_users', 'user_id = id')
							->where('forum_id', '>', 0)
			),
			array(
				'name' => 'watches',
				'options' => array(
					'name' => 'agora_subscriptions',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'CONCAT(user_id, topic_id, forum_id, category_id) AS id',
								'jos_id AS created_by',
								'NOW() AS created_on',
								'3 AS subscription_type',
								'topic_id AS subscription_type_id',
							))
							->join('left', 'agora_users', 'user_id = id')
							->where('topic_id', '>', 0)
			)
		);

		//This returns false if the import is big enough to be done in steps.
		//So we need to stop the importing in this step, in order for it to initiate
		if($this->importData($tables, 'agora') === false) return $this;

		//Move over avatars
		if(isset($this->data['people']))
		{
			//Get the avatar path
			$query = KFactory::tmp('lib.koowa.database.query')
																->select('conf_value')
																->from('agora_config')
																->where('conf_name', '=', 'o_avatars_dir');
			$path  = KFactory::get('lib.koowa.database')->select($query, KDatabase::FETCH_FIELD);
			
			foreach($this->data['people'] as $id => $person)
			{
				$from	= JPATH_ROOT.'/'.$path.'/'.$person['agora_user_id'].'.';
				
				//Agora have 3 avatar types we need to check, gif, jpg and png
				if(JFile::exists($from.'gif')) {
					$from .= 'gif';
				} elseif(JFile::exists($from.'jpg')) {
					$from .= 'jpg';
				} elseif(JFile::exists($from.'png')) {
					$from .= 'png';
				} else {
					continue;
				}
				
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

		$this->updateForumPaths();

		return $this;
	}

	/**
	 * Checks if Agora can be converted
	 *
	 * @TODO Agora converter is disabled until it's completed
	 *
	 * @return boolean
	 */
	public function canConvert()
	{
		return JComponentHelper::getComponent( 'com_agora', true )->enabled;
	}
}