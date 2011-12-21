<?php
/**
 * @version		$Id: ninjaboard_posts.php 1668 2011-03-22 18:50:26Z Richie $
 * @package		JXtended.Finder
 * @subpackage	plgFinderKunena_Posts
 * @copyright	Copyright (C) 2007 - 2010 JXtended, LLC. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://jxtended.com
 */

defined('JPATH_BASE') or die;
defined( 'KOOWA' ) or die( 'Restricted access' );

// Load the base adapter.
//require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';
KLoader::load('admin::com.finder.helpers.indexer.adapter');

// Load the language files for the adapter.
$lang = JFactory::getLanguage();
$lang->load('plg_finder_ninjaboard_posts');

/**
 * Finder adapter for Labels Labels.
 *
 * @package		JXtended.Finder
 * @subpackage	plgFinderKunena_Posts
 */
class plgFinderNinjaboard_Posts extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $_context = 'Ninjaboard_Posts';

	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $_layout = 'kunena';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $_type_title = 'Forum Post';

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @throws	Exception on database error.
	 */
	protected function _index(FinderIndexerResult $item)
	{
		// Build the necessary route and path information.
		$item->url		= $this->_getURL($item->topic);
		$item->itemid	= '100065';
		$item->route	= $item->url.'&post='.$item->id.'&Itemid='.$item->itemid.'#p'.$item->id;
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'display_name');

		// Strip slashes!
		$item->title	= stripslashes($item->title);
		$item->summary	= stripslashes($item->summary);
		$item->display_name	= stripslashes($item->display_name);

		$item->text	= FinderIndexerHelper::prepareContent($item->summary);

		// Translate the access group to an access level.
		//$item->cat_access = $this->_getAccessLevel($item->cat_access);

		// Inherit state and access form the category.
		$item->state	= 1;
		$item->access	= 0;

		// Set the language.
		$item->language	= FinderIndexerHelper::getDefaultLanguage();

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Forum Post');

		// Add the author taxonomy data.
		if (!empty($item->author)) {
			$item->addTaxonomy('Forum User', $item->display_name);
		}
		
		// Index the item.
		FinderIndexer::index($item);
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return	boolean		True on success.
	 */
	protected function _setup()
	{
		
		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param	mixed		A JDatabaseQuery object or null.
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function _getListQuery($sql = null)
	{
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : new JDatabaseQuery();
		$sql->select('tbl.*, tbl.subject AS title, tbl.subject AS title_alt, tbl.text AS summary');
		$sql->select('usr.*');
		$sql->select('usr.username AS display_name');
		$sql->select('person.posts AS person_posts');
		$sql->select('tbl.ninjaboard_post_id AS id');
		$sql->select("IFNULL(person.avatar, '/media/com_ninjaboard/images/avatar.png') AS avatar");
		$sql->select('person.signature');
		$sql->select('(SELECT rank_file FROM #__ninjaboard_ranks WHERE person.posts >= min ORDER BY min DESC LIMIT 1) AS rank_icon');
		$sql->select('(SELECT title FROM #__ninjaboard_ranks WHERE person.posts >= min ORDER BY min DESC LIMIT 1) AS rank_title');
		$sql->select('forum.title AS forum');
		$sql->select('topic.ninjaboard_topic_id AS topic');
		$sql->select('topic.hits');
		$sql->from('#__ninjaboard_posts AS tbl');
		$sql->join('LEFT', '#__users AS usr ON usr.id = tbl.created_user_id');
		$sql->join('LEFT', '#__ninjaboard_people AS person ON person.ninjaboard_person_id = tbl.created_user_id');
		$sql->join('LEFT', '#__ninjaboard_topics AS topic ON topic.ninjaboard_topic_id = tbl.ninjaboard_topic_id');
		$sql->join('LEFT', '#__ninjaboard_forums AS forum ON forum.ninjaboard_forum_id = topic.forum_id');
		$sql->where('tbl.enabled=1');		

		return $sql;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param	mixed		The id of the item.
	 * @return	string		The URL of the item.
	 */
	protected function _getURL($id)
	{
		return 'index.php?option=com_ninjaboard&view=topic&id='.$id;
	}
}