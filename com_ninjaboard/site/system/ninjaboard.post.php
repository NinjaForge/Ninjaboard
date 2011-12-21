<?php
/**
 * @version $Id: ninjaboard.post.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Post
 *
 * @package Ninjaboard
 */
class NinjaboardPost
{
	/**
	 * posts
	 * @var array
	 */
	var $posts = null;

	function NinjaboardPost($posts) {
		$this->Itemid = JRequest::getInt('Itemid', 0);
		$this->posts = $posts;
	}
	
	function getPostCount (){
		return count($this->posts);
	}
	
	function getPost($index = 0) {
		$post = $this->posts[$index];
		
		$this->preparePost($post);
		$this->assignIcon($post);
		$this->assignButtons($post);
		$this->assignAvatar($post);
		$this->assignRank($post);
		$this->assignAuthor($post);
		$this->assignAuthorRole($post);
		$this->assignOnlineState($post);
		$this->assignAttachments($post);
		
		return $post;
	}
		
	/**
	 * prepare post
	 */
	function preparePost($post) {
		$ninjaboardEngine =& NinjaboardEngine::getInstance();
		
		$ninjaboardEngine->convertToHtml($post);
		$post->postDate = NinjaboardHelper::Date($post->date_post);
				
		if ($post->registerDate) {
			$post->registerDate = NinjaboardHelper::Date($post->registerDate);
		} else {
			$post->registerDate = '';
		}
		
		$post->pid = 'p'. $post->id;
		
	}
			
	/**
	 * assign post icon
	 */
	function assignIcon($post) {
		$ninjaboardIconSet = NinjaboardIconSet::getInstance();
		
		$post->postIconLink = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$post->id_topic.'&Itemid='.$this->Itemid.'#p'.$post->id);
		$post->postIcon = $ninjaboardIconSet->iconByFunction[$post->icon_function];
	}
	
	/**
	 * assign post buttons
	 */
	function assignButtons($post) {
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardAuth		=& NinjaboardAuth::getInstance();
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		
		$guestTime = $ninjaboardConfig->getBoardSettings('guest_time') * 60;
		
		// quote needs reply authentification
		$post->buttonQuote = null;
		if ($ninjaboardAuth->getAuth('auth_reply', $post->id_forum)) {
			$post->buttonQuote = $ninjaboardButtonSet->buttonByFunction['buttonQuote'];
			$post->buttonQuote->href = JRoute::_('index.php?option=com_ninjaboard&view=editpost&topic='.$post->id_topic.'&post=0&quote='.$post->id.'&Itemid='.$this->Itemid);	
		}

		// edit authentification
		$post->buttonEdit = null;
		if ($ninjaboardAuth->getAuth('auth_edit', $post->id_forum)) {
		
			if ($ninjaboardUser->get('id') == $post->id_user && $post->id_user != 0 || 
				$post->id_user == 0 && $post->ip_poster == $_SERVER['REMOTE_ADDR'] && (strtotime(gmdate("Y-m-d H:i:s")) - strtotime($post->date_post)) < $guestTime || 
				$ninjaboardAuth->getUserRole($post->id_forum) > 2) {
				
				$post->buttonEdit = $ninjaboardButtonSet->buttonByFunction['buttonEdit'];
				
				if ($post->id_first_post != $post->id) {
					$post->buttonEdit->href = JRoute::_('index.php?option=com_ninjaboard&view=editpost&topic='.$post->id_topic.'&post='.$post->id.'&Itemid='.$this->Itemid);
				} else {
					$post->buttonEdit->href = JRoute::_('index.php?option=com_ninjaboard&view=edittopic&topic='.$post->id_topic.'&post='.$post->id.'&Itemid='.$this->Itemid);
				}
			}	
		}
		
		// delete authentification
		$post->buttonDelete = null;
		if ($ninjaboardAuth->getAuth('auth_delete', $post->id_forum)) {
		
			if ($ninjaboardUser->get('id') == $post->id_user && $post->id_user != 0 ||
				$post->id_user == 0 && $post->ip_poster == $_SERVER['REMOTE_ADDR'] && (strtotime(gmdate("Y-m-d H:i:s")) - strtotime($post->date_post)) < $guestTime || 
				$ninjaboardAuth->getUserRole($post->id_forum) > 2) {
				
				$post->buttonDelete = $ninjaboardButtonSet->buttonByFunction['buttonDelete'];
				
				if ($post->id_first_post != $post->id) {
					$post->buttonDelete->href = JRoute::_('index.php?option=com_ninjaboard&task=ninjaboarddeletepost&post='.$post->id.'&Itemid='.$this->Itemid);
				} else {
					$post->buttonDelete->href = JRoute::_('index.php?option=com_ninjaboard&task=ninjaboarddeletetopic&topic='.$post->id_topic.'&Itemid='.$this->Itemid);
				}
			}	
		}
		
		// report authentification
		$post->buttonReportPost = null;
		if ($ninjaboardAuth->getAuth('auth_reportpost', $post->id_forum)) {
			$post->buttonReportPost = $ninjaboardButtonSet->buttonByFunction['buttonReportPost'];
			$post->buttonReportPost->href = JRoute::_('index.php?option=com_ninjaboard&view=reportpost&post='.$post->id.'&Itemid='.$this->Itemid);	
		}

		// lock topic authentification
		$post->buttonLockTopicToggle = null;
		if ($post->id_first_post == $post->id && $ninjaboardAuth->getAuth('auth_lock', $post->id_forum)) {
		
			if ($ninjaboardUser->get('id') == $post->id_user && $post->id_user != 0 || 
				$post->id_user == 0 && $post->ip_poster == $_SERVER['REMOTE_ADDR'] && (strtotime(gmdate("Y-m-d H:i:s")) - strtotime($post->date_post)) < $guestTime || 
				$ninjaboardAuth->getUserRole($post->id_forum) > 2) {

				if ($post->status != 1) {
					$post->buttonLockTopicToggle = $ninjaboardButtonSet->buttonByFunction['buttonLockTopic'];
					$post->buttonLockTopicToggle->href = JRoute::_('index.php?option=com_ninjaboard&task=ninjaboardlocktopic&topic='.$post->id_topic.'&Itemid='.$this->Itemid);
				} else {
					$post->buttonLockTopicToggle = $ninjaboardButtonSet->buttonByFunction['buttonUnlockTopic'];
					$post->buttonLockTopicToggle->href = JRoute::_('index.php?option=com_ninjaboard&task=ninjaboardunlocktopic&topic='.$post->id_topic.'&Itemid='.$this->Itemid);
				}
			}	
		}
		
		// move topic authentification
		if ($post->id_first_post == $post->id && $ninjaboardAuth->getUserRole($post->id_forum) > 2) {
			$post->buttonMoveTopic = $ninjaboardButtonSet->buttonByFunction['buttonMoveTopic'];
			$post->buttonMoveTopic->href = JRoute::_('index.php?option=com_ninjaboard&view=movetopic&topic='.$post->id_topic.'&Itemid='.$this->Itemid);	
		}
		
	}
		
	/**
	 * assign avatar
	 */
	function assignAvatar($post) {
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();

		$post->avatarFile = '';
		if ($post->avatar_file != '') {
			$post->avatarFile = JURI::root().DL.$ninjaboardConfig->getAvatarSettings('avatar_path').DL.$post->avatar_file;
			$post->avatarFileAlt = $post->author;
		}
	}
			
	/**
	 * assign author
	 */
	function assignAuthor($post) {		
		$post->authorLink = '';
		$post->postsByAuthorLink = '';
		
		if ($post->author) {
			$post->authorLink = JRoute::_('index.php?option=com_ninjaboard&view=profile&id='.$post->id_user.'&Itemid='.$this->Itemid);
			$post->postsByAuthorLink = JRoute::_('index.php?option=com_ninjaboard&view=userposts&id='.$post->id_user.'&Itemid='.$this->Itemid);
		} else {
			if ($post->guest_author) {
				$post->author = $post->guest_author;
			} else {
				$post->author = JText::_('NB_GUEST');
			}
		}
	}
			
	/**
	 * assign author role
	 */
	function assignAuthorRole($post) {		
		
		if (!isset($post->id_user)) {
			$post->id_user = 0;
		}

		$ninjaboardUser =& NinjaboardUser::getInstance($post->id_user);
		
		if ($ninjaboardUser) {
			$role = $ninjaboardUser->get('role');
	
			if ($role < ($extendedRole = $ninjaboardUser->getExtendedRole($post->id_forum))) {
				$role = $extendedRole;
			}
			if ($role < ($groupRole = $ninjaboardUser->getGroupRole($post->id_forum))) {
				$role = $groupRole;
			}
				
			switch ($role) {
				case 0:
					$post->authorRole = JText::_('NB_GUEST');
					$post->authorClass = JText::_('jbGuest');
					break;
				case 1:
					$post->authorRole = JText::_('NB_REGISTERED');
					$post->authorClass = JText::_('jbRegistered');
					break;
				case 2:
					$post->authorRole = JText::_('NB_PRIVATE');
					$post->authorClass = JText::_('jbPrivate');
					break;
				case 3:
					$post->authorRole = JText::_('NB_MODERATOR');
					$post->authorClass = JText::_('jbModerator');
					break;
				case 4:
					$post->authorRole = JText::_('NB_ADMINISTRATOR');
					$post->authorClass = JText::_('jbAdministrator');
					break;
				default:
					$post->authorRole = '';
					break;		
			}
		}
	}
				
	/**
	 * assign rank
	 */
	function assignRank($post) {
		$ninjaboardRank	=& NinjaboardRank::getInstance();
		
		$rank = $ninjaboardRank->getRank($post->posts);
		
		if ($rank) {
			$post->userRank = $rank->name;
			$post->rankFile = $rank->rank_file;		
		}
	}
			
	/**
	 * assign online state
	 */
	function assignOnlineState($post) {
		$post->onlineState = false;
		$post->onlineStateFile = 'state_offline.png';
		$post->onlineStateAlt = JText::_('NB_OFFLINE');
		
		if ($post->show_online_state) {
			$db		=& JFactory::getDBO();
				
			$query = "SELECT s.*"
					. "\n FROM #__ninjaboard_session AS s"
					. "\n WHERE s.id_user = ".$post->id_user
					;
			$db->setQuery($query);
			
			if ($db->loadResult()) {
				$post->onlineState = true;
				$post->onlineStateFile = 'state_online.png';
				$post->onlineStateAlt = JText::_('NB_ONLINE');
			}
		}
	}

	/**
	 * assign attachments
	 */
	function assignAttachments($post) {
		$db	=& JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__ninjaboard_attachments WHERE id_post = ".$post->id);
		$attachmentsList = $db->loadObjectList();
		$attachmentImages = array();
		$attachmentFiles = array();
				
		// Check which file types are set to be images in the config
		$ninjaboardConfig =& NinjaboardConfig::getInstance();
		$imageExtns = explode(',',$ninjaboardConfig->getAttachmentSettings('img_file_types'));
		
		foreach ($attachmentsList as $attachment){
			//get the file extension
			$fileExt = substr($attachment->file_name, (strrpos($attachment->file_name, '.')+1));
			
									
			//if an image, append to images list
			if (in_array($fileExt, $imageExtns )){
				$attachmentImages[] = $attachment->file_name;				
			} else {
			//if a regular file, append to files list
				$attachmentFiles[] = $attachment->file_name;				
			}
			
										
		}
		
		$post->attachmentImages = $attachmentImages;
		$post->attachmentFiles = $attachmentFiles;
				
	}

	/**
	 * set property
	 *
	 * @access	public
	 */
	function set($property, $value = null) {
		if(isset($this->$property)) {
			$this->$property = $value;
		}			
	}

	/**
	 * get property
	 *
	 * @access	public
	 */
	function get($property, $default = null) {
		if(isset($this->$property)) {
			return $this->$property;
		}			
		return $default;
	}
	
}
?>
