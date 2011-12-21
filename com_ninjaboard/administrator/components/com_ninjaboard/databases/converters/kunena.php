<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: kunena.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersKunena
 *
 * Imports data from Kunena.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersKunena extends ComNinjaboardDatabaseConvertersAbstract
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
					'name' => 'kunena_attachments',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'id',
								'mesid AS post',
								'filename AS name',
								'userid AS joomla_user_id',
								'folder',
								'hash'
							))
			),
			array(
				'name' => 'forums',
				'options' => array(
					'name' => 'kunena_categories',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'tbl.*',
								"'/' AS path",
								'name AS title',
								'parent AS parent_id',
								'published AS enabled',
								'numTopics AS topics',
								'numPosts AS posts',
								'id_last_msg AS last_post_id'
							))
			),
			//Ghosts or "shadows"
			array(
				'name' => 'topic_symlinks',
				'options' => array(
					'name' => 'kunena_messages',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'id',
								'message AS ninjaboard_topic_id',
								'catid AS ninjaboard_forum_id'
							))
							->join('left', 'kunena_messages_text', 'mesid = id')
							->where('parent', '=', 0)
							->where('moved', '=', 1)
			),
			array(
				'name' => 'topics',
				'options' => array(
					'name' => 'kunena_messages',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'tbl.*',
								'catid AS forum_id',
								'topic_emoticon AS status',
								'id AS first_post_id',
								'(SELECT SUM(hits) FROM #__kunena_messages WHERE parent = tbl.id OR id = tbl.id) AS hits',
								'(SELECT COUNT(*) FROM #__kunena_messages WHERE parent = tbl.id) AS replies',
								'(SELECT MAX(id) FROM #__kunena_messages WHERE parent = tbl.id OR id = tbl.id) AS last_post_id',
							))
							->where('parent', '=', 0)
							->where('moved', '=', 0)
			),
			array(
				'name' => 'posts',
				'options' => array(
					'name' => 'kunena_messages',
					'identity_column' => 'id'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							//->set('columns', array('!`hold` AS `enabled`'))
							->select(array(
								'tbl.*',
								'!hold AS enabled',
								'thread AS ninjaboard_topic_id',
								'topic_emoticon AS status',
								'id AS first_post_id',
								'name AS guest_name',
								'email AS guest_email',
								'userid AS created_by',
								'modified_by AS modified_by',
								'FROM_UNIXTIME(time) AS created_on',
								'ip AS user_ip',
								'FROM_UNIXTIME(modified_time) AS mofidied_on',
								'modified_reason AS edit_reason',
								'message AS text',
							))
							->join('left', 'kunena_messages_text', 'mesid = id')
							->where('moved', '=', 0)
			),
			array(
				'name' => 'people',
				'options' => array(
					'name' => 'kunena_users',
					'identity_column' => 'userid'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'*'
							))
			)
		);

		//This returns false if the import is big enough to be done in steps.
		//So we need to stop the importing in this step, in order for it to initiate
		if($this->importData($tables, 'kunena') === false) return $this;

		//Move over file attachments
		if(isset($this->data['attachments']))
		{
			foreach($this->data['attachments'] as $id => $attachment)
			{
				$from	= JPATH_ROOT.'/'.$attachment['folder'].'/'.$attachment['name'];
				$file	= JPATH_ROOT.'/media/com_ninjaboard/attachments/'.$attachment['hash'].'.'.JFile::getExt($attachment['name']);
				
				//Don't do anything if avatar don't exist
				if(!JFile::exists($from)) continue;
				
				JFile::copy($from, $file);
				
				$this->data['attachments'][$id]['file'] = basename($file);
			}
		}

		//Kunena stores the new location in the text body
		if(isset($this->data['topic_symlinks']))
		{
			foreach($this->data['topic_symlinks'] as $id => $topic_symlink)
			{
				if(!$topic_symlink['ninjaboard_topic_id']) continue;

				$symlink = explode('=', $topic_symlink['ninjaboard_topic_id']);
				
				//Something is wrong, there is no id
				if(!isset($symlink[1])) continue;
				
				$this->data['topic_symlinks'][$id]['ninjaboard_topic_id'] = $symlink[1];
			}
		}

		//Move over avatars
		if(isset($this->data['people']))
		{
			foreach($this->data['people'] as $id => $person)
			{
				if(!$person['avatar']) continue;

				$from	= JPATH_ROOT.'/media/kunena/avatars/'.$person['avatar'];
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
	 * Checks if Kunena can be converted
	 *
	 * @return boolean
	 */
	public function canConvert()
	{
		if(!JComponentHelper::getComponent( 'com_kunena', true )->enabled) return false;

		$kunena = JPATH_BASE.'/components/com_kunena/kunena.xml';
		if(!file_exists($kunena)) {
			JError::raiseWarning(0, JText::_('You need to upgrade to Kunena 1.6 or newer in order to use the Kunena converter.'));
			return false;
		}

		return true;
	}
}