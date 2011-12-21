<?php
/*
* @version		1.0.7
* @package		mod_ninjaboard_popular_posts
* @author 		NinjaForge
* @author email	support@ninjaforge.com
* @link			http://ninjaforge.com
* @license      http://www.gnu.org/copyleft/gpl.html GNU GPL
* @copyright	Copyright (C) 2010 NinjaForge - All rights reserved.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
/*if(!defined('KOOWA')){
	JError::raiseWarning(0, JText::_("Koowa wasn't found. Please install the Koowa plugnin and enable it."));
	return;
}*/
//class helper
class modNinjaBoardPopularHelper
{
    //main function to get posts posts
    function getNinjaBoardPosts($params)
    {
		if (!file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')){ return;}
        //connect to database
        $db = &JFactory::getDBO();
        //get current user
        $user = &JFactory::getUser();
        //load module parameters & set defaults
        $num_posts = $params->get('num_posts', 1);
		//if $num_posts isn't set or set to zero, then set it to one
		if($num_posts <= 0){ $num_posts = 1; }
/*        $show_public = $params->get('show_public', 1);
        $show_regualar = $params->get('show_regular', 0);
        $show_special = $params->get('show_special', 0);
*/
        $only_forums = $params->get('only_forums', 0);
        $not_forums = $params->get('not_forums', 0);
        $child_forums = $params->get('child_forums', 1);
        $which_posts = $params->get('which_posts', 0);
        $order_by = $params->get('order_by', 0);
        $show_recent = $params->get('show_recent', '720');
        $show_unread = $params->get('show_unread', 1);
        $unread_image = $params->get('unread_image', 1);
        $subject_max = $params->get('subject_max', 50);
        $message_max = $params->get('message_max', 200);
        $name_link = $params->get('name_link', 1);
        $item_format = $params->get('item_format', '%R %S<br /><small>%N %D</small>');
        $tooltip_format = $params->get('tooltip_format', '%F > %L');
        $date_format = $params->get('date_format', 'j.n.Y G:i');
        $avatar_w_h = $params->get('avatar_w_h', 'height');
        $avatar_size = $params->get('avatar_size', '50');
        $name_link = $params->get('name_link', 'nb');
		$url_view = JRequest::getVar('view');
		$url_option = JRequest::getVar('option');
		//get current forum id
		if ($url_option == 'com_ninjaboard'){
			$url_id = JRequest::getInt('id', '');
		}
        //get menu id
        $itemId = modNinjaBoardPopularHelper::getItemId();
		//get com menu id
		$comItemid = modNinjaBoardPopularHelper::getComItemid($name_link);
        //create new post indicator
        //if user is logged in
        if ($user->id != 0)
        {
            //get last session from sessions table by user id
            $db->setQuery("SELECT time FROM #__session WHERE userid = {$user->id} ORDER BY time ASC LIMIT 1");
            $session = $db->loadResult();
            //if user has visited in last year
            if ($session > (time() - 1314000))
            {
                $lasttime = $session;
                //limit to 1 year, if user hasn't visited in over a year
            }
            else
            {
                $lasttime = time() - 1314000;
            }
            //if user isn't logged or is guest
        }
        else
        {
            $lasttime = 0;
        }
        if ($lasttime > 0)
        { //logged in user
            $sql_new = ", (unix_timestamp(p.created_time) > " . $lasttime .
                ") AS show_unread";
        }
        else
        { //user isn't logged or is guest
            $sql_new = ", 1 AS show_unread";
        }
        //how recent should shown posts be
        $sql_time = '';
        if ($show_recent > 0)
        {
            $recent_time = (time() - ($show_recent * 3600));
            $sql_time = 'AND unix_timestamp(p.created_time) >= ' . $recent_time;
        }
        // access levels
/*        $i = $show_public + 2 * $show_regualar + 4 * $show_special;
        switch ($i)
        {
            case 0:
                $sql_access = '';
                break;
            case 1:
                $sql_access = 'AND u.gid in (0,1)';
                break;
            case 2:
                $sql_access = 'AND u.gid in (-1,18)';
                break;
            case 3:
                $sql_access = 'AND u.gid in (0,1,-1,18)';
                break;
            case 4:
                $sql_access = 'AND u.gid in (19,20,21)';
                break;
            case 5:
                $sql_access = 'AND u.gid in (0,1,19,20,21)';
                break;
            case 6:
                $sql_access = 'AND u.gid in (-1,18,19,20,21)';
                break;
            default:
                $sql_access = '';
        }
*/
        //get posts in selected categories
        $sql_forum = '';
        if ($only_forums)
        {
            $forums =  $only_forums;
            foreach ($forums as $i => $forum)
            {
                $forums[$i] = (int)$forum;
                if ($forum == 0)
                    unset($forums[$i]);
            }
            $only_forums = implode(',', $forums);
            if ($only_forums)
            {
                if ($forums)
                {
                    $sql_forum = " AND (f.ninjaboard_forum_id IN (" . $only_forums .
                        ") OR f.parent_id IN (" . $only_forums . "))";
                }
                else
                {
                    $sql_forum = " AND f.ninjaboard_forum_id IN (" . $only_forums . ")";
                }
            }
        }
        //get posts not in selected categories
        if ($not_forums)
        {
            $forums = $not_forums;
            foreach ($forums as $i => $forum)
            {
                $forums[$i] = (int)$forum;
                if ($forum == 0)
                    unset($forums[$i]);
            }
            $not_forums = implode(',', $forums);
            if ($not_forums)
            {
                if ($forums)
                    $sql_forum .= " AND (f.ninjaboard_forum_id NOT IN (" . $not_forums .
                        ") AND f.parent_id NOT IN (" . $not_forums . "))";
                else
                    $sql_forum .= " AND f.ninjaboard_forum_id NOT IN (" . $not_forums . ")";
            }
        }
        //ASC or DESC
        $sql_order_by = "DESC"; 
		if ($order_by == 1) {$sql_order_by = "ASC";}
        //post type
      	$post_type = "Left Join #__ninjaboard_topics AS t ON p.ninjaboard_topic_id = t.ninjaboard_topic_id";
        if ($which_posts = 1) {$post_type = "Left Join #__ninjaboard_topics AS t ON p.ninjaboard_post_id = t.first_post_id";} 
		if ($which_posts = 2) {$post_type = "Left Join #__ninjaboard_topics AS t ON p.ninjaboard_post_id = t.last_post_id";}
		switch ($name_link)
		{
			//get avatar from cbe profiler
			case 'cbe':
				$select = 'avt.`avatar` as custom_avatar, avt.`user_id`, ';
				$join = 'LEFT JOIN #__cbe avt ON p.`created_user_id`=avt.`user_id` ';
				$and = 'AND avt.`avatarapproved`=1 ';
				break;
			//get avatar from cb profiler
			case 'cb':
				$select = 'avt.`avatar` as custom_avatar, avt.`user_id`, ';
				$join = 'LEFT JOIN #__comprofiler avt ON p.`created_user_id`=avt.`user_id` ';
				$and = 'AND avt.`avatarapproved`=1 ';
				break;
			//get avatar from jomsocial
			case 'js':
				$select = 'avt.`avatar` as custom_avatar, avt.`userid`, ';
				$join = 'LEFT JOIN #__community_users avt ON p.`created_user_id`=avt.`userid` ';
				$and = '';
				break;
			//get avatar from ninjaboard
			case 'nb':
				$select = 'avt.`avatar` as custom_avatar, avt.`ninjaboard_person_id`, ';
				$join = 'LEFT JOIN #__ninjaboard_people avt ON p.`created_user_id`=avt.`ninjaboard_person_id` ';
				$and = '';
				break;
			//default
			default:
				$select = '';
				$join = '';
				$and = '';
				break;	
		}
		$query = "
        SELECT
        " . $select . "
		u.username as author_name,  
		u.id,
        p.locked,
        p.`text` as message,
        p.subject as subject,
		p.created_time as created,
		p.modified as modified,
        p.ninjaboard_post_id as post_id,
        p.ninjaboard_topic_id,
        p.created_user_id as userid,
        p.guest_name as name,
		t.hits as topic_hits,
        t.ninjaboard_topic_id as topic_id,
        t.forum_id,
        f.title as forumname,
        f.ninjaboard_forum_id,
        f.enabled,
        f.parent_id as parent_id
        " . $sql_new . "
        FROM 
        #__ninjaboard_posts AS p
        " . $post_type . "
        Left Join #__ninjaboard_forums AS f ON t.forum_id = f.ninjaboard_forum_id
		Left Join #__users AS u ON p.created_user_id = u.id
        " . $join. "
        WHERE
        p.enabled = '1' AND
        f.enabled = '1'
        " . $sql_time . "		
		" . $sql_forum . "
		" . $and . "
		ORDER BY p.created_time
        LIMIT 0," . $num_posts;
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        //$countid = count($rows);
        if (!count($rows))
            return;
        $items = array();
        foreach ($rows as $row)
        {
            $item = '';
			//lets build the avatar
			//set which avatar file retrieved from the db to use
			$avatar_file = ($name_link=='default') ? $row->avatar_file : $row->custom_avatar;
			//set $avatar_file for tmpl use
			$item->avatar_file = $avatar_file;
			//get paths and default files for avatars 
			switch ($name_link)
			{
				//get avatar from cbe profiler
				case 'cbe':
					//$avatar_file = (strpos($avatar_file, 'gallery/') === false) ? 'tn' . $avatar_file : $avatar_file;
					if ($avatar_file){
						$avatar_path = JURI::base() . 'images/cbe/'.$avatar_file;
					} else {
						$avatar_path = JURI::base() . 'components/com_cbe/images/english/nophoto.png';
					}
					$user_link = JURI::base() . 'index.php?option=com_cbe&task=userProfile&user=';
					$item->avatar = '<a href="'.$user_link.$row->userid .'&Itemid='.$comItemid.'"><img name="'.$row->author_name.'" src="'.$avatar_path.'" '.$avatar_w_h.'="'.str_replace('px', '', $avatar_size).'" alt="" id="nb-posts-avatar" /></a>';
					break;
				//get avatar from cb profiler
				case 'cb':
					//$avatar_file = (strpos($avatar_file, 'gallery/') === false) ? 'tn' . $avatar_file : $avatar_file;
					if ($avatar_file){
						$avatar_path = JURI::base() . 'images/comprofiler/'.$avatar_file;
					} else {
						$avatar_path = JURI::base() . 'components/com_comprofiler/plugin/templates/luna/images/avatar/tnnophoto_n.png';
					}
					$user_link = JURI::base() . 'index.php?option=com_comprofiler&task=userProfile&user=';
					$item->avatar = '<a href="'.$user_link.$row->userid .'&Itemid='.$comItemid.'"><img name="'.$row->author_name.'" src="'.$avatar_path.'" '.$avatar_w_h.'="'.str_replace('px', '', $avatar_size).'" alt="" id="nb-posts-avatar" /></a>';
					break;
				//get avatar from jomsocial
				case 'js':
					if ($avatar_file){
						$avatar_path = JURI::base().$avatar_file;
					} else {
						$avatar_path = JURI::base() . 'components/com_community/assets/default_thumb.jpg';
					}
					$user_link = JURI::base() . 'index.php?option=com_community&view=profile&userid=';
					$item->avatar = '<a href="'.$user_link.$row->userid .'&Itemid='.$comItemid.'"><img name="'.$row->author_name.'" src="'.$avatar_path.'" '.$avatar_w_h.'="'.str_replace('px', '', $avatar_size).'" alt="" id="nb-posts-avatar" /></a>';
					break;
				//get avatar from ninjaboard
				case 'nb':
				case 'default':
				if ($avatar_file){
						$avatar_path = JURI::base() . $avatar_file;
					} else {
						$avatar_path = JURI::base() . 'media/com_ninjaboard/images/avatar.png';
					}
					$user_link = JURI::base().'index.php?option=com_ninjaboard&view=person&id='.$row->userid.'&Itemid='.$comItemid;
					$item->avatar = '<a href="'.$user_link.'"><img name="'.$row->name.'" src="'.$avatar_path.'" '.$avatar_w_h.'="'.str_replace('px', '', $avatar_size).'" alt="" id="nb-posts-avatar" /></a>';
					break;
			} 						
            $item->subject = (stripslashes($row->subject)) ? stripslashes($row->subject) : '';
			$item->subject_max = (JString::strlen($item->subject) > $subject_max) ? JString::substr($item->subject, 0, $subject_max - 4) . '...' : $item->subject;
			$row->message = modNinjaBoardPopularHelper::parseBBcode($row->message);
			$item->message = (stripslashes($row->message)) ? stripslashes($row->message) :'';
			$item->message_max = (JString::strlen($item->message) > $message_max) ? JString::substr($item->message, 0, $message_max - 4) . '...' : $item->message;
			$item->postname = ($row->userid > 0) ? "<a href='" . $user_link . "' title='" . $row->author_name . "'>" . $row->author_name . "</a>" : JText::_('GUEST');
			$item->name = ($row->userid > 0) ? $row->author_name : JText::_('GUEST');
			$item->postlink_s = "<a href='" . JURI::base() . 'index.php?option=com_ninjaboard&view=topic&id=' . $row->topic_id .
            '&Itemid=' . $itemId . '#post' . $row->post_id . "' title='" . $item->subject_max . "'>" . $item->subject_max . "</a>";
			$item->postlink_l = "<a href='" . JURI::base() . 'index.php?option=com_ninjaboard&view=topic&id=' . $row->topic_id .
            '&Itemid=' . $itemId . '#post' . $row->post_id . "' title='" . $item->subject . "'>" . $item->subject . "</a>";
			$item->datetime = date($date_format, strtotime($row->created));
            $item->unreadimage = '<img src="' . JURI::base() . 'modules/mod_ninjaboard_related_posts/images/new.png" alt="new" />';
			$item->online_status = (modNinjaBoardPopularHelper::getOnlineStatus($row->userid)) ? '<span id="nb-posts-online">' . JText::_('ONLINE') . '</span>' : '<span id="nb-posts-offline">' . JText::_('OFFLINE') . '</span>';
			$s = $item_format;
            $s = str_replace('%S', $item->postlink_s, $s);
            $s = str_replace('%L', $item->postlink_l, $s);
            $s = str_replace('%M', $item->message_max, $s);
            $s = str_replace('%T', $item->message, $s);
            $s = str_replace('%N', $item->postname, $s);
            $s = str_replace('%D', $item->datetime, $s);
            $s = str_replace('%H', $row->topic_hits, $s);
            $s = str_replace('%F', $row->forumname, $s);
            $s = str_replace('%I', $item->unreadimage, $s);
            $s = (strpos($s, '%A') === false) ? $s : str_replace('%A', $item->avatar, $s);
            $s = (strpos($s, '%O') === false) ? $s : str_replace('%O', $item->online_status, $s);
            $s = str_replace('title=""', '', $s);
			$item->title = $s;
			$s = $tooltip_format;
            $s = str_replace('%S', $item->subject_max, $s);
            $s = str_replace('%L', $item->subject, $s);
            $s = str_replace('%M', $item->message_max, $s);
            $s = str_replace('%T', $item->message, $s);
            $s = str_replace('%N', $item->name, $s);
            $s = str_replace('%D', date($date_format, strtotime($row->created)), $s);
            $s = str_replace('%H', $row->topic_hits, $s);
            $s = str_replace('%F', $row->forumname, $s);
            $s = str_replace('%I', $item->unreadimage, $s);
            $s = (strpos($s, '%O') === false) ? $s : str_replace('%O', $item->online_status, $s);
            $item->tooltip = $s;
            $items[] = $item;
        }
        return $items;
    }
	function getOnlineStatus($userid)
    {
        $db = &JFactory::getDBO();
        if ($userid > 0)
        {
            $db->setQuery("SELECT count(userid) FROM #__session WHERE userid=" . $userid);
            if ($db->loadResult())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
 	//get menu id
    function getItemId()
    {
        $database = JFactory::getDBO();
        $query = "select id from #__menu where link like '%index.php?option=com_ninjaboard%' limit 1";
        $database->setQuery($query);
        $Itemid = $database->loadResult();
        return $Itemid;
    }
	//get the Itemid from the right component
	function getComItemid($name_link){
		//get paths and default files for avatars
		switch ($name_link)
		{
			//get id from cbe profiler
			case 'cbe':
				$com = 'com_cbe';
				break;
			//get id from cb profiler
			case 'cb':
				$com = 'com_comprofiler';
				break;
			//get id from jomsocial
			case 'js':
				$com = 'com_community';
				break;
			//get id from ninjaboard
			case 'nb':
				$com = 'com_ninjaboard';
				break;
		}
		$database = JFactory::getDBO();
        $query = "select id from #__menu where link like '%index.php?option=".$com."%' limit 1";
        $database->setQuery($query);
        $comItemid = $database->loadResult();
		return $comItemid;
	} 	function getForumId($url_id)
	{
		if($url_id){
			$database = JFactory::getDBO();
			$query = "select forum_id from #__ninjaboard_topics where ninjaboard_topic_id = ".$url_id;
			$database->setQuery($query);
			$forum_id = $database->loadResult();
			return $forum_id;
		}else{
			return;
		}
    }function parseBBcode($input){
		  //load our BBcode parser - Dan: This probably shouldn't be in the controller but I can't think of a better place for it
				if(!class_exists('StringParser_BBCode'))
			include_once(JPATH_BASE."/administrator/components/com_ninja/helpers/bbcode/stringparser_bbcode.class.php");
				$bbcode = new StringParser_BBCode ();	
		  //this is a temporary fix, so the module can appear on pages that have Ninjamonials or Ninjaboard installed
		  //in which case these functions will already have been created. So we don't want to cause an error by creatign thema again
		  if (!function_exists('convertlinebreaks')) {
			 //Initialise the settings
				// Unify line breaks of different operating systems
				function convertlinebreaks ($text) {
				  $text = preg_replace ("/\015\012|\015|\012/", "\n", $text);
					$text = str_replace ("\r\n", "\n", $text);
					$text = str_replace ('\r\n', "\n", $text);
					$text = str_replace ("<br/>", "\n", $text);
					$text = str_replace ("<br />", "\n", $text);
					return str_replace ("<br>", "\n", $text);
				}
				// Remove everything but the newline charachter
				function bbcode_stripcontents ($text) {
					return preg_replace ("/[^\n]/", '', $text);
				}
				function do_bbcode_url ($action, $attributes, $content, $params, $node_object) {
					if (!isset ($attributes['default'])) {
						$url = $content;
						$text = htmlspecialchars ($content);
					} else {
						$url = $attributes['default'];
						$text = $content;
					}
					if ($action == 'validate') {
						if (substr ($url, 0, 5) == 'data:' || substr ($url, 0, 5) == 'file:'
						  || substr ($url, 0, 11) == 'javascript:' || substr ($url, 0, 4) == 'jar:') {
							return false;
						}
						return true;
					}
					return '<a href="'.htmlspecialchars ($url).'">'.$text.'</a>';
				}
				// Function to include images
				function do_bbcode_img ($action, $attributes, $content, $params, $node_object) {
					if ($action == 'validate') {
						if (substr ($content, 0, 5) == 'data:' || substr ($content, 0, 5) == 'file:'
						  || substr ($content, 0, 11) == 'javascript:' || substr ($content, 0, 4) == 'jar:'
						  || substr ($content, -4) == '.php') {
							return false;
						}
						return true;
					}
					return '<img src="'.htmlspecialchars($content).'" alt="">';
				}
				function do_bbcode_color ($action, $attributes, $content, $params, $node_object) {
					//the default attribute is one after the bbtag itself [color=blah]text[/color]
					if (isset ($attributes['default']))
						return '<span style="color:'.$attributes['default'].'">'.$content.'</span>';
					return $content;
				}
				function do_bbcode_quote ($action, $attributes, $content, $params, $node_object) {
				   if (!isset ($attributes['default'])) {
						$whoSaid = '';
					} else {			    	
						$whoSaid = '<span class="nmWhoSaid">'.JText::sprintf('NM_WHOSAID', $attributes['default']).'</span>';
					}
					 return '<blockquote class="nmQuote"><span>'.$whoSaid.$content.'</span></blockquote>';
				}
				function do_bbcode_size ($action, $attributes, $content, $params, $node_object) {
					if (isset ($attributes['default'])) 
					   return '<span style="font-size:'.$attributes['default'].'%">'.$content.'</span>';
					return $content;
				}
				}
				$bbcode->addFilter (1, 'convertlinebreaks');
				$bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'htmlspecialchars');
				$bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'nl2br');
				$bbcode->addParser ('list', 'bbcode_stripcontents');
				$bbcode->addCode ('b', 'simple_replace', null, array ('start_tag' => '<strong>', 'end_tag' => '</strong>'),
								  'inline', array ('listitem', 'block', 'inline', 'link'), array ());
				$bbcode->addCode ('i', 'simple_replace', null, array ('start_tag' => '<em>', 'end_tag' => '</em>'),
								  'inline', array ('listitem', 'block', 'inline', 'link'), array ());
				$bbcode->addCode ('u', 'simple_replace', null, array ('start_tag' => '<u>', 'end_tag' => '</u>'),
								  'inline', array ('listitem', 'block', 'inline', 'link'), array ());
				$bbcode->addCode ('color', 'callback_replace', 'do_bbcode_color', array ('usecontent_param' => 'default'),
								  'inline', array ('listitem', 'block', 'inline', 'link'), array ());
				$bbcode->addCode ('url', 'usecontent?', 'do_bbcode_url', array ('usecontent_param' => 'default'),
								  'link', array ('listitem', 'block', 'inline'), array ('link'));
				$bbcode->addCode ('quote', 'callback_replace', 'do_bbcode_quote', array ('usecontent_param' => 'default'),
								  'block', array ('block'), array ('link','listitem', 'inline'));
				$bbcode->addCode ('size', 'callback_replace', 'do_bbcode_size', array ('usecontent_param' => 'default'),
								  'inline', array ('listitem', 'block', 'inline', 'link'), array ());
				$bbcode->addCode ('img', 'usecontent', 'do_bbcode_img', array (),
								  'image', array ('listitem', 'block', 'inline', 'link'), array ());
				$bbcode->setOccurrenceType ('img', 'image');
				$bbcode->setMaxOccurrences ('image', 4);
				$bbcode->addCode ('ul', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
								  'list', array ('block', 'listitem'), array ());
				$bbcode->addCode ('ol', 'simple_replace', null, array ('start_tag' => '<ol>', 'end_tag' => '</ol>'),
								  'list', array ('block', 'listitem'), array ());
				$bbcode->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
								  'listitem', array ('list'), array ());
				$bbcode->setCodeFlag ('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
				$bbcode->setCodeFlag ('*', 'paragraphs', true);
				$bbcode->setCodeFlag ('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
				$bbcode->setCodeFlag ('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
				$bbcode->setCodeFlag ('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
				$bbcode->setRootParagraphHandling (false);    				
			/*
			 * End setup bbcode
			 */ 	                        
        $parsed_input = $bbcode->parse(trim($input));
		/*
		 * TODO - This is a very quick and dirty emoticon emoticon handler.
		 * This MUST be replaced asap
		 */
		// Smileys to find...
		$emoticonsIn = array(
			':)', 	
			':(',
			':o',
			':s',
			'8)',
			':D'
		);
		// And replace them with...
		$emoticonsOut = array(
			'<img border="0" src="'.JRoute::_('media/com_ninjamonials/images/emoticons/smiley_smile.png').'" alt=":)"/>',
			'<img border="0" src="'.JRoute::_('media/com_ninjamonials/images/emoticons/smiley_sad.png').'" alt=":("/>',
			'<img border="0" src="'.JRoute::_('media/com_ninjamonials/images/emoticons/smiley_surprised.png').'" alt=":o"/>',
			'<img border="0" src="'.JRoute::_('media/com_ninjamonials/images/emoticons/smiley_confused.png').'" alt=":s"/>',
			'<img border="0" src="'.JRoute::_('media/com_ninjamonials/images/emoticons/smiley_cool.png').'" alt="8)"/>',
			'<img border="0" src="'.JRoute::_('media/com_ninjamonials/images/emoticons/smiley_biggrin.png').'" alt=":D"/>'
		);
		$output = str_replace($emoticonsIn, $emoticonsOut, $parsed_input);
		return $output;
	}
}
?>
