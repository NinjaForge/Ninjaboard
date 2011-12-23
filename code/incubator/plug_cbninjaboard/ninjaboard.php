<?php

/* 
* @version		1.0.0
* @package		plg_cbninjaboard
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



//Basic tab extender
class getNinjaBoardTab extends cbTabHandler {

	/**
	 * Construnctor
	 */
	function getNinjaBoardTab() {

		$this->cbTabHandler();

	}

	function getDisplayTab($tab,$user,$ui) {

		global $my, $_CB_framework;

		$lang = JFactory::getLanguage();

        $lang->load('plg_cbninjaboard');

		$return = null;

		$params = $this->params; //get parameters (plugin and related tab)

		$nbPlugEnabled = $params->get('nbPlugEnabled', "1");

		if($nbPlugEnabled){

			JHTML::stylesheet('ninjaboard.css','components/com_comprofiler/plugin/user/plug_cbninjaboard/');

			//get parameters

			$nbPlugShowSubject = $params->get('nbPlugShowSubject', "1");

			$nbPlugTruncateSubject = $params->get('nbPlugTruncateSubject', "25");

			$nbPlugShowMessage = $params->get('nbPlugShowMessage', "1");

			$nbPlugTruncateMessage = $params->get('nbPlugTruncateMessage', "50");

			$nbPlugShowForum = $params->get('nbPlugShowForum', "1");

			$nbPlugShowHits = $params->get('nbPlugShowHits', "1");

			$nbPlugShowCreated = $params->get('nbPlugShowCreated', "1");

			$nbPlugShowModified = $params->get('nbPlugShowModified', "1");

			$nbPlugDateFormat = $params->get('nbPlugDateFormat', "D, d M");

			$nbPlugShowEdit = $params->get('nbPlugShowEdit', "1");

			$nbPlugSortBy = $params->get('nbPlugSortBy', "1");

			$nbPlugSortOrder = $params->get('nbPlugSortOrder', "DESC");

			$nbPlugShowPagination = $params->get('nbPlugShowPagination', "1");

			$nbPlugPaginationCount = $params->get('nbPlugPaginationCount', "5");
			
			$limit = ($nbPlugPaginationCount > 0) ? $nbPlugPaginationCount : 0;
			
			$offset = JRequest::getVar('limitstart', 0, 'REQUEST');

			//get current user
			$userId = $user->id;
			
			//get user posts
			$rows = $this->getPosts($userId, $nbPlugSortBy, $nbPlugSortOrder, $nbPlugPaginationCount, $limit, $offset);

			//get user post count
			$row_count = $this->countPosts($userId);

			//get item id
			$itemId = $this->getItemId();
			
			

			//create_html
			if($row_count <= 0){

				$list = '<div class="cbNinjaBoard"><div class="cbNBposts"><table><tr><td>You currently have no NinjaBoard posts</td></tr></table></div></div>';

			}else{		

				$list = "";

				//show only selected fields in a table row
				$list .= '<div class="cbNinjaBoard"><div class="cbNBposts"><table><thead><tr>';

				if ($nbPlugShowSubject){ $list .= '<th>'.JText::_('NB_SUBJECT_CBP').'</th>'; }
				
				if ($nbPlugShowMessage){ $list .= '<th>'.JText::_('NB_MESSAGE_CBP').'</th>'; } 

				if ($nbPlugShowForum){ $list .= '<th>'.JText::_('NB_FORUM_CBP').'</th>'; }

				if ($nbPlugShowHits){ $list .= '<th>'.JText::_('NB_HITS_CBP').'</th>'; }

				if ($nbPlugShowCreated){ $list .= '<th>'.JText::_('NB_CREATED_CBP').'</th>'; }

				if ($nbPlugShowModified){ $list .= '<th>'.JText::_('NB_MODIFIED_CBP').'</th>'; }

				if ($nbPlugShowEdit){ $list .= '<th>'.JText::_('NB_VIEW_POST_CBP').'</th>'; } 

				$list .= '</tr></thead><tbody>';

				
				$items = array();

				foreach ($rows as $row){


					//get the item subject, show no subject if there is none, truncate subject if necessary
					$item->subject_long = (stripslashes($row->subject)) ? stripslashes($row->subject) : JText::_('NB_NO_SUBJECT_CBP');

					$item->subject_short = (JString::strlen($row->subject) > $nbPlugTruncateSubject) ? JString::substr($row->subject, 0, $nbPlugTruncateSubject- 4) . '...' : $item->subject_long;

					$item->subject = ($nbPlugTruncateSubject > 0 ) ? $item->subject_short : $item->subject_long;

					//get the item message, show no message if there is none, truncate message if necessary
					$item->message_long = (stripslashes($row->message)) ? stripslashes($row->message) : JText::_('NB_NO_MESSAGE_CBP');

					$item->message_short = (JString::strlen($row->message) > $nbPlugTruncateMessage) ? JString::substr($row->message, 0, $nbPlugTruncateMessage- 4) . '...' : $item->message_long;

					$item->unparsed = ($nbPlugTruncateMessage > 0 ) ? $item->message_short : $item->message_long;
					$item->message = KFactory::get('admin::com.ninja.helper.bbcode')->parse(array('text' => $item->unparsed));

					//link to edit post
					$item->view_link = '<a href="'.(JURI::base().'index.php?option=com_ninjaboard&view=topic&id='.$row->topic_id.'&Itemid='.$itemId.'#post'.$row->post_id).'" title="'.JText::_('NB_VIEW_POST_CBP').'">'.'<img src="'.JURI::base().'components/com_comprofiler/plugin/user/plug_cbninjaboard/post.png" width="32" height="32" alt="'.JText::_('NB_VIEW_POST_CBP').'" />'.'</a>';
					
					//link to forum
					$item->forum_link = '<a href="'.(JURI::base().'index.php?option=com_ninjaboard&view=forum&id='.$row->forum_id.'&Itemid='.$itemId).'" title="'.JText::_($row->forum_name).'">'.JText::_($row->forum_name).'</a>';

					//format dates
					$item->c_datetime = ($row->created > 0) ? date($nbPlugDateFormat, strtotime($row->created)) : JText::_('NB_NO_DATE_CBP');

					$item->m_datetime = ($row->modified > 0) ? date($nbPlugDateFormat, strtotime($row->modified)) : JText::_('NB_NO_DATE_CBP'); 

					//show only selected fields in a table row
					$list .= '<tr>';							

					if ($nbPlugShowSubject){ $list .= '<td>'.$item->subject.'</td>'; }

					if ($nbPlugShowMessage){ $list .= '<td>'.$item->message.'</td>'; } 

					if ($nbPlugShowForum){ $list .= '<td>'.$item->forum_link.'</td>'; }

					if ($nbPlugShowHits){ $list .= '<td>'.$row->hits.'</td>'; }

					if ($nbPlugShowCreated){ $list .= '<td>'.$item->c_datetime.'</td>'; }

					if ($nbPlugShowModified){ $list .= '<td>'.$item->m_datetime.'</td>'; }

					if ($nbPlugShowEdit){ $list .= '<td>'.$item->view_link.'</td>'; } 

					$list .= '</tr>';

				}

				$list .= '</table></div>';
				
				if(($nbPlugShowPagination)&&($row_count>$limit)){
					
					$list .= '<div class="cbNBpagination"><table><tr><td>';
					
					//$list .= KFactory::get('site::com.ninjaboard.helper.paginator',array('name' => 'posts'))->pagination($row_count, $offset, $limit , 4, true);				
					
					jimport('joomla.html.pagination');
					
					$pagination = new JPagination($row_count, $offset, $limit );
					
					$list .= $pagination->getPagesLinks().$pagination->getResultsCounter();

					$list .= '</td></tr></table></div>';
				}
				
				$list .= '</div>';
			
			}

			return $list;
		
		}

	} // end or getDisplayTab function 
	
	//function to get posts	
	function getPosts($userId, $nbPlugSortBy, $nbPlugSortOrder, $nbPlugPaginationCount, $limit, $offset){
			
			//connect to database
			$db =& JFactory::getDBO();

			//sort by field			
			$sql_by = "p.created_time";

			if ($nbPlugSortBy==0){

				$sql_by = "p.subject";

			}elseif($nbPlugSortBy==2){

				$sql_by = "p.modified";

			}
			
			//sort by order
			$sql_order = ($nbPlugSortOrder) ? "DESC" : "ASC";
			
						
			//limit for pagination
			$limit_by = ($nbPlugPaginationCount > 0) ? "LIMIT ".$offset.", ".$limit : "";

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

	f.title as forum_name,

	f.ninjaboard_forum_id as forum_id
	

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
			
			$rows = ($data) ? $data : '0';

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

					$db->setQuery( $query );

					$db_itemId = $db->loadResult();	

					$itemId = ($db_itemId > 0) ? $db_itemId : '999999';	
	
					return $itemId;	

	
		}//eof get menu id

} // end of getNinjaBoardTab class

?>