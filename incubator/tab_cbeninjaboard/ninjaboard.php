<?php

/* 
* @version		1.0.0
* @package		tab_cbeninjaboard
* @author 		NinjaForge
* @author email	support@ninjaforge.com
* @link			http://ninjaforge.com
* @license      http://www.gnu.org/copyleft/gpl.html GNU GPL
* @copyright	Copyright (C) 2010 NinjaForge - All rights reserved.
* Last updated: 1st June, 2010 
*/

/** ensure this file is being included by a parent file */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if(!defined('KOOWA')){
	JError::raiseWarning(0, JText::_("Koowa wasn't found. Please install the Koowa plugnin and enable it."));
	return;
}

//include config

include_once('administrator/components/com_cbe/ue_config.php');	



//include tabhandler

global $mainframe, $ueConfig;

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_cbe'.DS.'classes'.DS.'CBETabHandler.php');





//Basic tab extender

class CBE_ninjaboard extends CBETabHandler {



	var $params;

	function __construct($parent) {

		$this->parent = $parent;

		$this->params = $this->parent->getParams();

	}

	

	

	

	function display() {

		

		global $user, $mainframe, $ueConfig;

		

		$my =& JFactory::getUser();



		$lang =& JFactory::getLanguage();



        $lang->load('plg_cbeninjaboard');



		$return = null;





		JHTML::stylesheet('ninjaboard.css','components/com_cbe/enhanced/ninjaboard/');



		//get parameters

		$ninjaboard_ShowSubject = $this->params['ninjaboard_ShowSubject'];

		$ninjaboard_TruncateSubject = $this->params['ninjaboard_TruncateSubject'];

		$ninjaboard_ShowMessage = $this->params['ninjaboard_ShowMessage'];

		$ninjaboard_TruncateMessage = $this->params['ninjaboard_TruncateMessage'];

		$ninjaboard_ShowForum = $this->params['ninjaboard_ShowForum'];

		$ninjaboard_ShowHits = $this->params['ninjaboard_ShowHits'];

		$ninjaboard_ShowCreated = $this->params['ninjaboard_ShowCreated'];

		$ninjaboard_ShowModified = $this->params['ninjaboard_ShowModified'];

		$ninjaboard_DateFormat = $this->params['ninjaboard_DateFormat'];

		$ninjaboard_ShowEdit = $this->params['ninjaboard_ShowEdit'];

		$ninjaboard_SortBy = $this->params['ninjaboard_SortBy'];

		$ninjaboard_SortOrder = $this->params['ninjaboard_SortOrder'];

		$ninjaboard_ShowPagination = $this->params['ninjaboard_ShowPagination'];

		$ninjaboard_PaginationCount = $this->params['ninjaboard_PaginationCount'];

		$limit = ($ninjaboard_PaginationCount > 0) ? $ninjaboard_PaginationCount : 0;
			
		$offset = JRequest::getVar('limitstart', 0, 'REQUEST');





		//connect to database

		$database = &JFactory::getDBO();



		//get current user

		$userId = $user->id;

		

		//get user posts

		$rows = $this->getPosts($userId, $ninjaboard_SortBy, $ninjaboard_SortOrder, $ninjaboard_PaginationCount, $limit, $offset);



		//get user post count

		$row_count = $this->countPosts($userId);



		//get item id

		$itemId = $this->getItemId();







		//create_html

		if($row_count <= 0){

			$list = '<div class="cbeNinjaBoard"><div class="cbeNBposts"><table><tr><td>You currently have no NinjaBoard posts</td></tr></table></div>';

		}else{	



			$list = '<div class="cbeNinjaBoard"><div class="cbeNBposts"><table><thead><tr>';



			if ($ninjaboard_ShowSubject){ $list .= '<th>'.JText::_('Subject').'</th>'; }

			

			if ($ninjaboard_ShowMessage){ $list .= '<th>'.JText::_('Message').'</th>'; } 



			if ($ninjaboard_ShowForum){ $list .= '<th>'.JText::_('Forum').'</th>'; }



			if ($ninjaboard_ShowHits){ $list .= '<th>'.JText::_('Hits').'</th>'; }



			if ($ninjaboard_ShowCreated){ $list .= '<th>'.JText::_('Created').'</th>'; }



			if ($ninjaboard_ShowModified){ $list .= '<th>'.JText::_('Modified').'</th>'; }



			if ($ninjaboard_ShowEdit){ $list .= '<th>'.JText::_('View Post').'</th>'; } 



			$list .= '</tr></thead><tbody>';



			

			$items = array();



			foreach ($rows as $row){





				//get the item subjec, show no subject if there is none, truncate subject if necessary

				$item->subject_long = (stripslashes($row->subject)) ? stripslashes($row->subject) : JText::_('No Subject');



				$item->subject_short = (JString::strlen($row->subject) > $ninjaboard_TruncateSubject) ? JString::substr($row->subject, 0, $subject_max- 4) . '...' : $item->subject_long;



				$item->subject = ($ninjaboard_TruncateSubject > 0 ) ? $item->subject_short : $item->subject_long;



				//get the item message, show no message if there is none, truncate message if necessary

				$item->message_long = (stripslashes($row->message)) ? stripslashes($row->message) : JText::_('No Message');



				$item->message_short = (JString::strlen($row->message) > $ninjaboard_TruncateMessage) ? JString::substr($row->message, 0, $ninjaboard_TruncateMessage - 4) . '...' : $item->message_long;



				$item->unparsed = ($ninjaboard_TruncateMessage > 0 ) ? $item->message_short : $item->message_long;
				$item->message = KFactory::get('admin::com.ninja.helper.bbcode')->parse(array('text' => $item->unparsed));



				//link to edit post
				$item->view_link = '<a href="'.(JURI::base().'index.php?option=com_ninjaboard&view=topic&id='.$row->topic_id.'&Itemid='.$itemId.'#post'.$row->post_id).'" title="'.JText::_('View Post').'">'.'<img src="'.JURI::base().'components/com_cbe/enhanced/ninjaboard/post.png" width="32" height="32" alt="View Post" />'.'</a>';
				

				//link to forum

				$item->forum_link = '<a href="'.(JURI::base().'index.php?option=com_ninjaboard&view=forum&id='.$row->forum_id.'&Itemid='.$itemId).'" title="'.JText::_($row->forumname).'">'.JText::_($row->forumname).'</a>';



				//format dates

				$item->c_datetime = ($row->created > 0) ? date($ninjaboard_DateFormat, strtotime($row->created)) : JText::_('No Date');



				$item->m_datetime = ($row->modified > 0) ? date($ninjaboard_DateFormat, strtotime($row->modified)) : JText::_('No Date'); 



				//show only selected fields in a table row

				$list .= '<tr>';							


				if ($ninjaboard_ShowSubject){ $list .= '<td>'.$item->subject.'</td>'; }


				if ($ninjaboard_ShowMessage){ $list .= '<td>'.$item->message.'</td>'; } 


				if ($ninjaboard_ShowForum){ $list .= '<td>'.$item->forum_link.'</td>'; }



				if ($ninjaboard_ShowHits){ $list .= '<td>'.$row->hits.'</td>'; }



				if ($ninjaboard_ShowCreated){ $list .= '<td>'.$item->c_datetime.'</td>'; }



				if ($ninjaboard_ShowModified){ $list .= '<td>'.$item->m_datetime.'</td>'; }



				if ($ninjaboard_ShowEdit){ $list .= '<td>'.$item->view_link.'</td>'; } 



				$list .= '</tr>';



			}



			$list .= '</tbody></table></div>';

			

			if(($ninjaboard_ShowPagination)&&($row_count>$limit)){

					
					$list .= '<div class="cbeNBpagination"><table><tr><td>';
					
					//$list .= KFactory::get('site::com.ninjaboard.helper.paginator',array('name' => 'posts'))->pagination($row_count, $offset, $limit , 4, true);	
					
					jimport('joomla.html.pagination');
					
					$pagination = new JPagination($row_count, $offset, $limit );
					
					$list .= $pagination->getPagesLinks().$pagination->getResultsCounter();

					$list .= '</td></tr></table></div>';

			}

			

			$list .= '</div>';

	

		}



		echo $list;



	} // end or getDisplayTab function 





	//function to get posts	

	function getPosts($userId, $ninjaboard_SortBy, $ninjaboard_SortOrder, $ninjaboard_PaginationCount, $limit, $offset){

			

		//connect to database

		$db =& JFactory::getDBO();

	

		//sort by field			

		$sql_by = "p.created_time";

	

		if ($ninjaboard_SortBy==0){

	

			$sql_by = "p.subject";

	

		}elseif($ninjaboard_SortBy==2){

	

			$sql_by = "p.modified";

	

		}

		

		//sort by order

		$sql_order = ($ninjaboard_SortOrder) ? "DESC" : "ASC";

		

					

		//limit for pagination

		$limit_by = ($ninjaboard_PaginationCount > 0) ? "LIMIT ".$offset.", ".$limit : "";

	

		$query = '



	SELECT

	p.locked,

	p.`text` as message,

	p.subject as subject,

	unix_timestamp(p.created_time) as created,

	unix_timestamp(p.modified) as modified,

	p.ninjaboard_post_id as post_id,

	p.ninjaboard_topic_id,

	p.created_user_id as userId,

	t.ninjaboard_topic_id as topic_id,

	t.hits as hits,

	t.forum_id,

	t.moved_from_forum_id,

	f.title as forumname,

	f.ninjaboard_forum_id,

	f.enabled,

	f.parent_id as parent_id	

	FROM 

	#__ninjaboard_posts AS p

	Left Join #__ninjaboard_topics AS t ON p.ninjaboard_topic_id = t.ninjaboard_topic_id

	Left Join #__ninjaboard_forums AS f ON t.forum_id = f.ninjaboard_forum_id

	
	WHERE

	p.enabled = "1" AND

	t.moved_from_forum_id = "0" AND

	f.enabled = "1" AND

	p.created_user_id = '.$userId.'

	
	ORDER BY '.$sql_by.' '.$sql_order.'

	'.$limit_by;


		$db->setQuery($query);

		$data = $db->loadObjectList();

		

		$rows = ($data > 0) ? $data : '0';

	

		return $rows;

	}

		

		

	//counts posts for userId

	function countPosts($userId){	

		

		//connect to database

		$db =& JFactory::getDBO();



		$query = '



	SELECT

	p.enabled,

	t.moved_from_forum_id,

	f.enabled,
	
	p.created_user_id,

	p.ninjaboard_topic_id,

	t.ninjaboard_topic_id,

	t.forum_id,

	f.ninjaboard_forum_id


	FROM 

	#__ninjaboard_posts AS p

	Left Join #__ninjaboard_topics AS t ON p.ninjaboard_topic_id = t.ninjaboard_topic_id

	Left Join #__ninjaboard_forums AS f ON t.forum_id = f.ninjaboard_forum_id


	WHERE

	p.enabled = "1" AND

	t.moved_from_forum_id = "0" AND

	f.enabled = "1" AND

	p.created_user_id = '.$userId;


		$db->setQuery($query);

		$rows = $db->loadObjectList();

		$count = count($rows);

		

		$row_count = ($count > 0) ? $count : '0';

		

		return $row_count;

		

	}

		

	//get menu id

	function getItemId(){



		$db = JFactory::getDBO();



		$query = "select id from #__menu where link like '%index.php?option=com_ninjaboard%' limit 1";

	

		$db->setQuery($query);

	

		$db_itemId = $db->loadResult();	

	

		$itemId = ($db_itemId > 0) ? $db_itemId : '999999';	

	

		return $itemId;	

	

	}//eof get menu id

}

?>