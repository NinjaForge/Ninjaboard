<?php
/*
* @version		1.0.0
* @package		plg_community_ninjaboard for JomSocial
* @author 		NinjaForge
* @author email	support@ninjaforge.com
* @link			http://ninjaforge.com
* @license      http://www.gnu.org/copyleft/gpl.html GNU GPL
* @copyright	Copyright (C) 2010 NinjaForge - All rights reserved.
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

if(!defined('KOOWA')){
	JError::raiseWarning(0, JText::_("Koowa wasn't found. Please install the Koowa plugnin and enable it."));
	return;
}


require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');

if(!class_exists('plgCommunityNinjaBoard'))
{
	class plgCommunityNinjaBoard extends CApplications
	{
		var $name		= 'NinjaBoard';
		var $_name		= 'ninjaboard';
		var $_user		= null;
	
	    function plgCommunityNinjaBoard(& $subject, $config)
	    {
			$this->_user	=& CFactory::getActiveProfile();
			$this->_my		=& CFactory::getUser();
			$this->db 		= JFactory::getDBO();
			$this->_path	= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_ninjaboard';
	
			parent::__construct($subject, $config);
	    } 
	
 	 	 	 	 		
		function onProfileDisplay()
		{	
		
			JPlugin::loadLanguage( 'plg_community_ninjaboard', JPATH_SITE );
			
			$config	=& CFactory::getConfig();
			
			$this->loadUserParams();

			$my	=& JFactory::getUser();
			$user =& CFactory::getActiveProfile();

			
			// Attach CSS & JS
			$document =& JFactory::getDocument();
			$document->addStyleSheet(JURI::base().'plugins/community/ninjaboard/ninjaboard.css');
			
			
			//get parameters
			$caching = $this->params->get('cache', "0");
						
			$nbPlugShowSubject = $this->params->get('nbPlugShowSubject', "1");

			$nbPlugTruncateSubject = $this->params->get('nbPlugTruncateSubject', "25");

			$nbPlugShowMessage = $this->params->get('nbPlugShowMessage', "1");

			$nbPlugTruncateMessage = $this->params->get('nbPlugTruncateMessage', "50");

			$nbPlugShowForum = $this->params->get('nbPlugShowForum', "1");

			$nbPlugShowHits = $this->params->get('nbPlugShowHits', "1");

			$nbPlugShowCreated = $this->params->get('nbPlugShowCreated', "1");

			$nbPlugShowModified = $this->params->get('nbPlugShowModified', "1");

			$nbPlugDateFormat = $this->params->get('nbPlugDateFormat', "D, d M");

			$nbPlugShowEdit = $this->params->get('nbPlugShowEdit', "1");

			$nbPlugSortBy = $this->params->get('nbPlugSortBy', "1");

			$nbPlugSortOrder = $this->params->get('nbPlugSortOrder', "DESC");

			$nbPlugShowPagination = $this->params->get('nbPlugShowPagination', "0");

			$nbPlugPaginationCount = $this->params->get('nbPlugPaginationCount', "5");
			
			$userId = $user->id;
			
			$limit = ($nbPlugPaginationCount > 0) ? $nbPlugPaginationCount : 0;
			
			$offset = JRequest::getVar('limitstart', 0, 'REQUEST');
			
			//get user posts
			$rows = $this->getPosts($userId, $nbPlugSortBy, $nbPlugSortOrder, $nbPlugPaginationCount, $limit, $offset);

			//get user post count
			$row_count = $this->countPosts($userId);
			
			//get itemid
			$itemId = $this->getItemId();
			
	    	$cache =& JFactory::getCache('plgCommunityNinjaBoard');
	    	$cache->setCaching($caching);
			$callback = array('plgCommunityNinjaBoard', '_getNinjaBoardHTML');					
			
 
			$content = $cache->call($callback, $rows, $row_count, $user->id, $nbPlugShowSubject, $nbPlugTruncateSubject, $nbPlugShowMessage, $nbPlugTruncateMessage, $nbPlugShowForum, $nbPlugShowHits, $nbPlugShowCreated, $nbPlugShowModified, $nbPlugDateFormat, $nbPlugShowEdit, $nbPlugSortBy, $nbPlugSortOrder, $nbPlugShowPagination, $nbPlugPaginationCount, $offset, $limit, $itemId, $this->params, $user, $my);
			
			return $content; 	
		}
		
		
		//creates NinjaBoard HTML
		function _getNinjaBoardHTML($rows, $row_count, $userId, $nbPlugShowSubject, $nbPlugTruncateSubject, $nbPlugShowMessage, $nbPlugTruncateMessage, $nbPlugShowForum, $nbPlugShowHits, $nbPlugShowCreated, $nbPlugShowModified, $nbPlugDateFormat, $nbPlugShowEdit, $nbPlugSortBy, $nbPlugSortOrder, $nbPlugShowPagination, $nbPlugPaginationCount, $offset, $limit, $itemId, &$params, &$user, &$my){
			
			$mainframe =& JFactory::getApplication();
			
			$column_array = array($nbPlugShowSubject, $nbPlugShowMessage, $nbPlugShowForum, $nbPlugShowHits, $nbPlugShowCreated, $nbPlugShowModified, $nbPlugShowEdit);
			
			$columns = array_sum($column_array);

											
			if($row_count <= 0){
				
				$html = '<div class="jsNinjaBoard"><div class="jsNBposts"><table><tr><td>You currently have no NinjaBoard posts</td></tr></table></div>';

				
			}else{	


				//show only selected fields in a table row
				$html = '<div class="jsNinjaBoard"><div class="jsNBposts"><table><thead><tr>';

													
				if ($nbPlugShowSubject){ $html .= '<th>'.JText::_('NB_SUBJECT_JSP').'</th>'; }

				if ($nbPlugShowMessage){ $html .= '<th>'.JText::_('NB_MESSAGE_JSP').'</th>'; } 

				if ($nbPlugShowForum){ $html .= '<th>'.JText::_('NB_FORUM_JSP').'</th>'; }

				if ($nbPlugShowHits){ $html .= '<th>'.JText::_('NB_HITS_JSP').'</th>'; }

				if ($nbPlugShowCreated){ $html .= '<th>'.JText::_('NB_CREATED_JSP').'</th>'; }

				if ($nbPlugShowModified){ $html .= '<th>'.JText::_('NB_MODIFIED_JSP').'</th>'; }

				if ($nbPlugShowEdit){ $html .= '<th>'.JText::_('NB_VIEW_POST_JSP').'</th>'; } 

				$html .= '</tr></thead><tbody>';

				$items = array();

				foreach($rows as $row) {

					//get the item subject, show no subject if there is none, truncate subject if necessary
					$item->subject_long = (stripslashes($row->subject)) ? stripslashes($row->subject) : JText::_('NB_NO_SUBJECT_JSP');

					$item->subject_short = (JString::strlen($item->subject_long) > $nbPlugTruncateSubject) ? JString::substr($item->subject_long, 0, $subject_max- 4) . '...' : $item->subject_long;

					$item->subject = ($nbPlugTruncateSubject > 0 ) ? $item->subject_short : $item->subject_long;

					//get the item message, show no message if there is none, truncate message if necessary
					$item->message_long = (stripslashes($row->message)) ? stripslashes($row->message) : JText::_('NB_NO_MESSAGE_JSP');

					$item->message_short = (JString::strlen($item->message_long) > $nbPlugTruncateMessage) ? JString::substr($item->message_long, 0, $nbPlugTruncateMessage- 4) . '...' : $item->message_long;

					$item->unparsed = ($nbPlugTruncateMessage > 0 ) ? $item->message_short : $item->message_long;
					$item->message = KFactory::get('admin::com.ninja.helper.bbcode')->parse(array('text' => $item->unparsed));
					//link to edit post
					$item->view_link = '<a href="'.(JURI::base().'index.php?option=com_ninjaboard&view=topic&id='.$row->topic_id.'&Itemid='.$itemId.'#post'.$row->post_id).'" title="'.JText::_('NB_VIEW_POST_JSP').'">'.'<img src="'.JURI::base().'plugins/community/ninjaboard/post.png" width="32" height="32" alt="'.JText::_('NB_VIEW_POST_JSP').'" />'.'</a>';
					
					//link to forum
					$item->forum_link = '<a href="'.(JURI::base().'index.php?option=com_ninjaboard&view=forum&id='.$row->forum_id.'&Itemid='.$itemId).'" title="'.JText::_($row->forum_name).'">'.JText::_($row->forum_name).'</a>';
					
										
					//format dates
					$item->c_datetime = ($row->created > 0) ? date($nbPlugDateFormat, strtotime($row->created)) : JText::_('NB_NO_DATE_JSP');

					$item->m_datetime = ($row->modified > 0) ? date($nbPlugDateFormat, strtotime($row->modified)) : JText::_('NB_NO_DATE_JSP'); 

					//show only selected fields in a table row
					$html .= '<tr>';

					if ($nbPlugShowSubject){ $html .= '<td>'.$item->subject.'</td>'; }

					if ($nbPlugShowMessage){ $html .= '<td>'.$item->message.'</td>'; } 

					if ($nbPlugShowForum){ $html .= '<td>'.$item->forum_link.'</td>'; }

					if ($nbPlugShowHits){ $html .= '<td>'.$row->hits.'</td>'; }

					if ($nbPlugShowCreated){ $html .= '<td>'.$item->c_datetime.'</td>'; }

					if ($nbPlugShowModified){ $html .= '<td>'.$item->m_datetime.'</td>'; }

					if ($nbPlugShowEdit){ $html .= '<td>'.$item->view_link.'</td>'; } 
					
					$html .= '</tr>';


				}

				$html .= '</tbody></table></div>';
				
				//if(($nbPlugShowPagination)&&($row_count>$limit)){
					
					$html .= '<div class="jsNBpagination"><table><tr><td>';
					
					//$html .= KFactory::get('site::com.ninjaboard.helper.paginator',array('name' => 'posts'))->pagination($row_count, $offset, $limit , 4, true);	

					jimport('joomla.html.pagination');
					
					$pagination = new JPagination($row_count, $offset, $limit );
					
					$html .= $pagination->getPagesLinks().$pagination->getResultsCounter();


					$html .= '</td></tr></table></div>';
				//}
				
				$html .= '</div>';
				
			}

			return $html;
			
		} 
		
			
	
	function onAppDisplay(){
			
			ob_start();
			$limit=0;
			$html= $this->onProfileDisplay($limit);
			echo $html;
			
			$content = ob_get_contents();
			ob_end_clean(); 
		
			return $content;
			
	}
		
		
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
			$rows = $db->loadObjectList();
			
			if($this->db->getErrorNum()) {
				JError::raiseError(500, $this->db->stderr());
			}		
			
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
			$row_count = count($rows);
			
			if($this->db->getErrorNum()) {
				JError::raiseError( 500, $this->db->stderr());
			}		
			
			return $row_count;
		}
		
	//get menu id
	function getItemId(){

			$db = JFactory::getDBO();

			$query = "select id from #__menu where link like '%index.php?option=com_ninjaboard%' limit 1";

					$db->setQuery($query);

					$db_itemId = $db->loadResult();	

					$itemId = ($db_itemId > 0) ? $db_itemId : '999999';
					
					if($this->db->getErrorNum()) {
						JError::raiseError(500, $this->db->stderr());
					}		
	
					return $itemId;	

	
		}//eof get menu id
	}	
}
?>