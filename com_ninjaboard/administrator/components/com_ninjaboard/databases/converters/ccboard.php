<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: ccboard.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersCcboard
 *
 * Imports data from ccBoard.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersCcboard extends ComNinjaboardDatabaseConvertersAbstract
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
					'name' => 'ccb_attachments',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'tbl.id',
								'tbl.post_id AS post',
								'tbl.real_name AS name',
								'post.post_user AS joomla_user_id',
								'tbl.ccb_name AS file'
							))
							->join('left', 'ccb_posts AS post', 'tbl.post_id = post.id')
			),
			array(
				'name' => 'forums',
				'options' => array(
					'name' => 'ccb_category',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'(id + (SELECT MAX(id) FROM #__ccb_forums)) AS id',
								'cat_name AS title',
								"'/' AS path"
							))
			),
			array(
				'name' => 'forums',
				'options' => array(
					'name' => 'ccb_forums',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'id',
								'forum_name AS title',
								'forum_desc AS description',
								'topic_count AS topics',
								'post_count AS posts',
								'last_post_id',
								'published AS enabled',
								'locked',
								"CONCAT('/', (cat_id + (SELECT MAX(id) FROM #__ccb_forums)), '/') AS path"
							))
			),
			array(
				'name' => 'posts',
				'options' => array(
					'name' => 'ccb_posts',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'*',
								'topic_id AS ninjaboard_topic_id',
								'post_subject AS subject',
								'post_text AS text',
								'post_user AS created_by',
								'FROM_UNIXTIME(post_time) AS created_on',
								'ip AS user_ip',
								'modified_by',
								'FROM_UNIXTIME(modified_time) AS mofidied_on',
								'modified_reason AS edit_reason'
							))
			),
			array(
				'name' => 'topics',
				'options' => array(
					'name' => 'ccb_topics',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'*',
								'reply_count AS replies',
								'topic_type AS topic_type_id',
								'start_post_id AS first_post_id',
							))
			),
			array(
				'name' => 'people',
				'options' => array(
					'name' => 'ccb_users',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'user_id AS id',
								'signature',
								'post_count AS posts',
								'avatar'
							))
			)
		);

		//This returns false if the import is big enough to be done in steps.
		//So we need to stop the importing in this step, in order for it to initiate
		if($this->importData($tables, 'ccboard') === false) return $this;

		//Move over file attachments
		if(isset($this->data['attachments']))
		{
			foreach($this->data['attachments'] as $id => $attachment)
			{
				$from	= JPATH_ROOT.'/components/com_ccboard/assets/uploads/'.$attachment['file'];
				$file	= JPATH_ROOT.'/media/com_ninjaboard/attachments/'.$attachment['file'];
				
				//Don't do anything if avatar don't exist
				if(!JFile::exists($from)) continue;
				
				JFile::copy($from, $file);
			}
		}

		//Move over avatars
		if(isset($this->data['people']))
		{
			foreach($this->data['people'] as $id => $person)
			{
				if(!$person['avatar']) continue;

				$from	= JPATH_ROOT.'/components/com_ccboard/assets/avatar/'.$person['avatar'];
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
	 * Checks if ccBoard can be converted
	 *
	 * It also checks just the tables to allow converts without anything other than the ccBoard database tables on site
	 *
	 * @return boolean
	 */
	public function canConvert()
	{
		if(JComponentHelper::getComponent( 'com_ccboard', true )->enabled) {
			return true;
		} else {
			//Tests if the component files does exists, but is disabled in jos_components
			try {
				$model = KFactory::get('admin::com.ccboard.model.forums');
			} catch(KFactoryAdapterException $e) {
				return true;
			}
			
			//Tries to count rows in the ccBoard forums table, which will throw an exception if table don't exist
			try {
				$model->getTotal();
			} catch(KDatabaseTableException $e) {
				return false;
			}
			
			return true;
		}
	}

	/**
	 * Gets the ccBoard converter label
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return 'ccBoard';
	}
}