<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: yahoo.php 2461 2011-10-11 22:32:21Z stian $
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
class ComNinjaboardDatabaseConvertersYahoo extends ComNinjaboardDatabaseConvertersAbstract
{
	/**
	 * This converter is able to run in steps
	 *
	 * @var boolean
	 */
	public $splittable = true;

	/**
	 * Boolean deciding wether the converter needs an UI button or not
	 *
	 * Used here to hide this converter
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
			array(
				'name' => 'topics',
				'options' => array(
					'name' => 'yahoo_messages',
					'identity_column' => 'ninjaboard_topic_id'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'tbl.post_id AS ninjaboard_topic_id',
								'tbl.post_id AS first_post_id',
								'(SELECT last_post.post_id FROM #__yahoo_messages AS last_post WHERE last_post.topic_id = tbl.topic_id ORDER BY post_id DESC LIMIT 1) AS last_post_id',
								'(SELECT COUNT(*) FROM #__yahoo_messages AS count_replies WHERE count_replies.topic_id = tbl.topic_id) AS replies',
								'(SELECT forum.ninjaboard_forum_id FROM #__ninjaboard_forums AS forum LIMIT 1) AS forum_id'
							))
							->where('tbl.post_subject = tbl.topic_id')
			),
			array(
				'name' => 'posts',
				'options' => array(
					'name' => 'yahoo_messages',
					'identity_column' => 'ninjaboard_post_id'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'post_id AS ninjaboard_post_id',
								'post_subject AS subject',
								'post_text AS text',
								'post_time AS created_on',
								'post_username AS guest_name',
								'(SELECT topic.post_id FROM #__yahoo_messages AS topic WHERE topic.post_subject = tbl.topic_id LIMIT 1) AS ninjaboard_topic_id'
							))
			)
		);

		//This returns false if the import is big enough to be done in steps.
		//So we need to stop the importing in this step, in order for it to initiate
		if($this->importData($tables, 'yahoo') === false) return $this;
		
		//Convert the html to bbcode before it's inserted to ninjaboard tables
		if(isset($this->data['posts']))
		{
			foreach($this->data['posts'] as $id => $post)
			{
				$this->data['posts'][$id]['text'] = html2bbcode($post['text']);
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
	 * Gets the ccBoard converter label
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return 'Yahoo Groups';
	}
}