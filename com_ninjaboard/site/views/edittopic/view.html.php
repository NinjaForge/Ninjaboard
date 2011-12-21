<?php
/**
 * @version $Id: view.html.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Ninjaboard Edit Topic View
 *
 * @package Ninjaboard
 */
class NinjaboardViewEditTopic extends JView
{

	function display($tpl = null) {

		// initialize variables
		$app				=& JFactory::getApplication();
		$document			=& JFactory::getDocument();
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardAuth		=& NinjaboardAuth::getInstance();
		$breadCrumbs		=& NinjaboardBreadCrumbs::getInstance();
		$messageQueue		=& NinjaboardMessageQueue::getInstance();
		$ninjaboardIconSet	=& NinjaboardIconSet::getInstance($ninjaboardConfig->getIconSetFile());
	
		// initialize variables
		$model	=& $this->getModel();
		$topic = $model->getTopic(JRequest::getInt('topic', 0));
		$this->assignRef('topic', $topic);
		$post = $model->getPost(JRequest::getInt('post', 0));
		$this->assignRef('post', $post);
		
		// is this a new topic or a topic to edit
		$forumId = JRequest::getInt('forum', 0);
		if ($forumId == 0) {
			$forumId = $topic->id_forum;
		}
		
		$guestTime = $ninjaboardConfig->getBoardSettings('guest_time') * 60;
		
		// edit authentification
		$canEdit = false;
		if ($ninjaboardAuth->getAuth('auth_edit', $forumId)) {	
			if ($topic->id == 0 || $ninjaboardUser->get('id') == $post->id_user && $post->id_user != 0 || 
					$post->id_user == 0 && $post->ip_poster == $_SERVER['REMOTE_ADDR'] && (strtotime(gmdate("Y-m-d H:i:s")) - strtotime($post->date_post)) < $guestTime || 
					$ninjaboardAuth->getUserRole($post->id_forum) > 2) {
				$canEdit = true;
			}	
		}

		if (!$canEdit) {
			$msg = sprintf(JText::_('NB_MSGNOPERMISSION'), JText::_('NB_EDITTOPIC'));
			$messageQueue->addMessage($msg);
			
			if ($topic->id) {
				$link = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$topic->id.'&Itemid='.$this->Itemid, false);				
			} else {
				$link = JRoute::_('index.php?option=com_ninjaboard&view=forum&forum='.$forumId.'&Itemid='.$this->Itemid, false);
			}

			$app->redirect($link);
		}
		
		// preview topic
		$session			=& JFactory::getSession();
		$topicPreviewPost	=  $session->get('ninjaboardPost');

		$session->set('ninjaboardPost', null);
		
		if (isset($topicPreviewPost)) {
			$post->subject = $topicPreviewPost['subject'];
			$post->text = $topicPreviewPost['text'];
			$post->icon_function = $topicPreviewPost['icon_function'];
			$post->enable_emoticons = $topicPreviewPost['enable_emoticons'];
			$post->enable_bbcode = $topicPreviewPost['enable_bbcode'];
			
			$postPreview =& NinjaboardHelper::getPostPreview($post);
			$this->assignRef('postPreview', $postPreview);			
		}
	
		$forum = JRequest::getInt('forum', 0);
		if ($forum != 0) {
			$forum = $model->getForum($forum);
		} else {
			$forum = $model->getForum($post->id_forum);
		}
		
		$category	= $model->getCategory($forum->id_cat);
		
		// load form validation behavior
		JHTML::_('behavior.formvalidation');
		
		// handle page title
		$document->setTitle($ninjaboardConfig->getBoardSettings('board_name'));

		if ($forum->locked) {
			$link = JRoute::_('index.php?option=com_ninjaboard&view=forum&forum='. $forum->id .'&Itemid='.$this->Itemid, false);
			$msg = sprintf(JText::_('NB_MSGFORUMLOCKED'), $forum->name);
			$messageQueue->addMessage($msg);
			$app->redirect($link);
		}
		
		// disable guest name as default
		$this->assign('enableGuestName', 0);
		
		// this is only allowed when guests have permission to post or edit and we do not handle a registered user
		if ($forum->auth_post == 0 && !$ninjaboardUser->get('id') || $forum->auth_edit == 0 &&  !$ninjaboardUser->get('id')) {
			$this->assign('enableGuestName', $ninjaboardConfig->getBoardSettings('enable_guest_name'));
			$this->assign('guestNameRequired', $ninjaboardConfig->getBoardSettings('guest_name_required'));
		}

		// handle bread crumb
		$catRedirect = 'index.php?option=com_ninjaboard&view=board&category='.$category->id.'&Itemid='.$this->Itemid;
		$breadCrumbs->addBreadCrumb($category->name, $catRedirect);
		$redirect = 'index.php?option=com_ninjaboard&view=forum&forum='.$forum->id.'&Itemid='.$this->Itemid;
		$breadCrumbs->addBreadCrumb($forum->name, $redirect);

		$breadCrumbs->addBreadCrumb($post->subject ? $post->subject : JText::_('NB_NEWTOPIC'), '');

		// load the ninjaboard editor object
		//$editor =& NinjaboardHelper::getEditor();
		
		// get post icons
		$postIcons = $ninjaboardIconSet->getIconsByGroup('iconPost');
		$this->assign('postIcons' , $postIcons);
		$iconFunction = $post->id ? $post->icon_function : $ninjaboardConfig->getDefaultTopicIcon();
		$this->assign('iconFunction' , $iconFunction);
			
		// get options
		if ($post->id) {
			//$enableBBCode = $post->enable_bbcode;
			//$enableEmoticons = $post->enable_emoticons;
			$notifyOnReply = $post->notify_on_reply;		
		} else if ($ninjaboardUser->get('id')) {
			//$enableBBCode = $ninjaboardUser->get('enable_bbcode');
			//$enableEmoticons = $ninjaboardUser->get('enable_emoticons');
			$notifyOnReply = $ninjaboardUser->get('notify_on_reply');
		} else {
			
			$notifyOnReply = 0;				
		}
		
		//Only use the board settings for enable bbcode and smilies
		$enableBBCode = $ninjaboardConfig->getBoardSettings('enable_bbcode');
		$enableEmoticons = $ninjaboardConfig->getBoardSettings('enable_emoticons');
		
		$this->assign('enableBBCode' , $enableBBCode);
		$this->assign('enableEmoticons'  , $enableEmoticons);
		
		$lists['notifyonreply']		= JHTML::_('select.booleanlist', 'notify_on_reply',  '', $notifyOnReply);

		$topictype = array();
		$topictype[] = JHTML::_('select.option', 0, JText::_('NB_NORMAL'), 'value', 'text');
		if ($ninjaboardAuth->getAuth('auth_sticky', $forum->id)) {
			$topictype[] = JHTML::_('select.option', 1, JText::_('NB_STICKY'), 'value', 'text');
		}
		if ($ninjaboardAuth->getAuth('auth_announce', $forum->id)) {
			$topictype[] = JHTML::_('select.option', 2, JText::_('NB_ANNOUNCE'), 'value', 'text');
		}	
		$lists['topictype'] = JHTML::_('select.radiolist', $topictype, 'type', 'class="inputbox"', 'value', 'text', 0);
		
		$this->assign('forum', $forum);
		//$this->assign('editor', $editor);
		$this->assign('ninjaboardUser', $ninjaboardUser);
		$this->assignRef('lists', $lists);

		$action = JRoute::_('index.php?option=com_ninjaboard');
		$this->assignRef('action', $action);
		
		// get buttons
		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		$this->assignRef('buttonSubmit', $ninjaboardButtonSet->buttonByFunction['buttonSubmit']);
		$this->assignRef('buttonPreview', $ninjaboardButtonSet->buttonByFunction['buttonPreview']);
		$this->assignRef('buttonCancel', $ninjaboardButtonSet->buttonByFunction['buttonCancel']);
		
		// attachments
		$this->assignRef('enableAttachments', $ninjaboardConfig->getAttachmentSettings('enable_attachments'));
		
		$attachmentsList = array();
		//If this is an edit, then get any existing attachments
		if ($post->id){
		//post_type = 1 is for normal posts.0 = topics
			$sql = "SELECT * FROM #__ninjaboard_attachments WHERE id_post = ".$post->id;
			
			$database =& JFactory::getDBO();
			$database->setQuery($sql);
			$attachmentsList = $database->loadObjectList(); 
			
			if 	($attachmentsList === null)	
				$attachmentsList = array();	
		}
		// attachments
		$this->assignRef('attachmentsList', $attachmentsList);
		

		parent::display($tpl);
	}		

}
?>
