<?php
/**
 * @version $Id: ninjaboard.feed.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) .'/../../../libraries/bitfolge/feedcreator.php');

/**
 * Ninjaboard Feed
 *
 * @package Ninjaboard
 */
class NinjaboardFeed
{

	/**
	 * get a ninjaboard authentification object
	 *
	 * @access public
	 * @return object of NinjaboardAuth
	 */
	function &getInstance() {
	
		static $ninjaboardFeed;

		if (!is_object($ninjaboardFeed)) {
			$ninjaboardFeed = new NinjaboardFeed();
		}

		return $ninjaboardFeed;
	}

	function createFeed() {
		$ninjaboardConfig =& NinjaboardConfig::getInstance();
		$feedRootURL = JURI::root();
		
		$feed = new UniversalFeedCreator();
		$feed->useCached();
		$feed->title = $ninjaboardConfig->getBoardSettings('board_name');
		$feed->description = $ninjaboardConfig->getBoardSettings('description');

		$feed->descriptionTruncSize = $ninjaboardConfig->getFeedSettings('feed_desc_trunk_size');
		$feed->descriptionHtmlSyndicated = $ninjaboardConfig->getFeedSettings('feed_desc_html_syndicate');
		//$feed->xslStyleSheet = "http://feedster.com/rss20.xsl";

		$feed->link = $feedRootURL;
		$feed->feedURL = $feedRootURL.$PHP_SELF;
		
		// create the feed image
		$feedImage = new FeedImage();
		$feedImage->title = $ninjaboardConfig->getFeedSettings('feed_image_title');
		$feedImage->url = $ninjaboardConfig->getFeedSettings('feed_image_url');
		$feedImage->link = $ninjaboardConfig->getFeedSettings('feed_image_link');
		$feedImage->description = $ninjaboardConfig->getFeedSettings('feed_image_description');
		$feedImage->descriptionTruncSize = $ninjaboardConfig->getFeedSettings('feed_image_desc_trunk_size');
		$feedImage->descriptionHtmlSyndicated = $ninjaboardConfig->getFeedSettings('feed_image_desc_html_syndicate');

		$feed->image = $feedImage;
		
		// get items
		$descriptionTruncSize = $ninjaboardConfig->getFeedSettings('feed_desc_trunk_size');
		$descriptionHtmlSyndicated = $ninjaboardConfig->getFeedSettings('feed_desc_html_syndicate');
		$items = $this->getFeedItems();
		foreach ($items as $item) {
			$feedItem = new FeedItem();
			$feedItem->title = $item->subject;
			 
			$feedItem->link = JRoute::_($feedRootURL.'index.php?option=com_ninjaboard&view=topic&topic='.$item->id_topic.'&Itemid='.$this->Itemid.'#p'.$item->id);
			$feedItem->description = $item->text;

			$feedItem->descriptionTruncSize = $descriptionTruncSize;
			$feedItem->descriptionHtmlSyndicated = $descriptionHtmlSyndicated;
		
			$feedItem->date = strtotime($item->date_post);
			$feedItem->source = $feedRootURL;
			$feedItem->author = $item->author;
		
			$feed->addItem($feedItem);
		}

		// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1, MBOX, OPML, ATOM0.3, HTML, JS
		return $feed->saveFeed("RSS2.0", JPATH_SITE.DS.'boardfeed.xml');
		//return $feed->createFeed();
	}
	
	/**
	 * get feed items
	 * 
	 * @access public
	 * @return array
	 */
	function getFeedItems() {
	
		// initialize variables
		$db				=& JFactory::getDBO();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		
		// get the count of items to show
		$limit = (int)$ninjaboardConfig->getFeedSettings('feed_items_count');
		
		// items to show. topics or posts?
		switch ((int)$ninjaboardConfig->getFeedSettings('feed_items_type')) {
			case 0:
				$innerJoin = "\n INNER JOIN #__ninjaboard_topics AS t ON t.id_first_post = p.id";
				break;
			case 1:
				$innerJoin = "\n INNER JOIN #__ninjaboard_topics AS t ON t.id = p.id_topic";
				break;
			default:
				break;
		}
						
		$query = "SELECT p.*, t.id_first_post, t.status, ". ($ninjaboardConfig->getViewSettings('show_user_as') == 0 ? "u.name" : "u.username") . " AS author, pg.guest_name AS guest_author, "
				. "\n u.id AS id_user, u.registerDate, ju.posts, ju.avatar_file, ju.show_online_state, f.name AS forum_name, c.name AS category_name"
				. "\n FROM #__ninjaboard_posts AS p"
				. $innerJoin
				. "\n INNER JOIN #__ninjaboard_forums AS f ON f.id = p.id_forum"
				. "\n INNER JOIN #__ninjaboard_categories AS c ON c.id = f.id_cat"
				. "\n LEFT JOIN #__users AS u ON p.id_user = u.id"
				. "\n LEFT JOIN #__ninjaboard_users AS ju ON ju.id = u.id"
				. "\n LEFT JOIN #__ninjaboard_posts_guests AS pg ON p.id = pg.id_post"
				. "\n ORDER BY p.date_post DESC LIMIT 0, $limit"
				;
		$db->setQuery($query);

		return $db->loadObjectList();
	}

}
?>