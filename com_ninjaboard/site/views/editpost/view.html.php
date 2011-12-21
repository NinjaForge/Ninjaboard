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

jimport( 'joomla.application.component.view');

/**
 * Ninjaboard Edit Post View
 *
 * @package Ninjaboard
 */
class NinjaboardViewEditPost extends JView
{

	function display( $tpl = null ) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardAuth		=& NinjaboardAuth::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();	
		$messageQueue 	=& NinjaboardMessageQueue::getInstance();
		$ninjaboardIconSet 	=& NinjaboardIconSet::getInstance($ninjaboardConfig->getIconSetFile());

		// initialize variables
		$model =& $this->getModel();
		$topic = $model->getTopic(JRequest::getVar('topic', 0, '', 'int'));
		$this->assignRef('topic', $topic);
		$topicSubject = $model->getTopicSubject($topic->id_first_post);
		$this->assignRef('topicSubject', $topicSubject);
		$post = $model->getPost(JRequest::getVar('post', 0, '', 'int'));
		$this->assignRef('post', $post);		
		$forum = $model->getForum($topic->id_forum);		
		$category	= $model->getCategory($forum->id_cat);

		$guestTime = $ninjaboardConfig->getBoardSettings('guest_time') * 60;
		
		// edit authentification
		$canEdit = false;
		if ($ninjaboardAuth->getAuth('auth_edit', $topic->id_forum)) {	
			if ($post->id == 0 || $ninjaboardUser->get('id') == $post->id_user && $post->id_user != 0 || 
					$post->id_user == 0 && $post->ip_poster == $_SERVER['REMOTE_ADDR'] && (strtotime(gmdate("Y-m-d H:i:s")) - strtotime($post->date_post)) < $guestTime || 
					$ninjaboardAuth->getUserRole($post->id_forum) > 2) {
				$canEdit = true;
			}	
		}

		if (!$canEdit) {
			$msg = sprintf(JText::_('NB_MSGNOPERMISSION'), JText::_('NB_EDITPOST'));
			$messageQueue->addMessage($msg);
			
			$link = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$topic->id.'&Itemid='.$this->Itemid, false);
			if ($post->id) {
				$link .= '#'.$post->id;
			}

			$mainframe->redirect($link);
		}
		
		// preview post
		$session =& JFactory::getSession();
		$topicPreviewPost = $session->get('ninjaboardPost');
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
	
		$quote = JRequest::getVar('quote', 0, '', 'int');
		if ($quote != 0) {
			$quotePost = $model->getPostQuote($quote);
			$post->text = '[quote]'. $quotePost->text .'[/quote]';
		}
		
		// load all posts associated with the topic for topic review 
		$posts	= new NinjaboardPost($model->getTopicPosts($topic->id));
		$this->assignRef('posts', $posts);
		
		// request variables
		$limit		= JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('posts_per_page'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		
		$total = count($posts->posts);
		$this->assignRef('total', $total);
		
		jimport('joomla.html.pagination');
		$this->pagination = new JPagination($total, $limitstart, $limit);
						
		// load form validation behavior
		JHTML::_('behavior.formvalidation');
		
		// handle page title
		$document->setTitle($ninjaboardConfig->getBoardSettings('board_name'));

		if ($forum->locked) {
			$link = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='. $topic->id .'&Itemid='. $this->Itemid, false);
			$msg = sprintf(JText::_('NB_MSGFORUMLOCKED'), $forum->name);
			$messageQueue->addMessage($msg);
			$mainframe->redirect($link);
		}

		// disable guest name as default
		$this->assign('enableGuestName', 0);
		
		// this is only allowed when guests have permission to post or edit and we do not handle a registered user
		if ($forum->auth_post == 0 && !$ninjaboardUser->get('id') || $forum->auth_edit == 0 &&  !$ninjaboardUser->get('id')) {
			$this->assign('enableGuestName', $ninjaboardConfig->getBoardSettings('enable_guest_name'));
			$this->assign('guestNameRequired', $ninjaboardConfig->getBoardSettings('guest_name_required'));
		}
		
		$this->assign('enableReplySubject', $ninjaboardConfig->getBoardSettings('enable_reply_subject'));
		
		// handle bread crumb
		$catRedirect = 'index.php?option=com_ninjaboard&view=board&category='.$category->id.'&Itemid='.$this->Itemid;
		$breadCrumbs->addBreadCrumb($category->name, $catRedirect);
		$redirect = 'index.php?option=com_ninjaboard&view=forum&forum='.$forum->id.'&Itemid='.$this->Itemid;
		$breadCrumbs->addBreadCrumb($forum->name, $redirect);
		$breadCrumbs->addBreadCrumb($post->subject ? $post->subject : JText::_('Re:'), '');
		
		// get post icons
		$postIcons = $ninjaboardIconSet->getIconsByGroup('iconPost');
		$this->assign('postIcons' , $postIcons);
		$post->id ? $iconFunction = $post->icon_function : $iconFunction = $ninjaboardConfig->getDefaultPostIcon();
		$this->assign('iconFunction' , $iconFunction);

		// get options
		if ($post->id) {
			$notifyOnReply = $post->notify_on_reply;		
		} else if ($ninjaboardUser->get('id')) {
			$notifyOnReply = $ninjaboardUser->get('notify_on_reply');
		} else {			
			$notifyOnReply = 0;				
		}
		
		//Only use the board settings for enable bbcode and smilies
		$enableBBCode = $ninjaboardConfig->getBoardSettings('enable_bbcode');
		$enableEmoticons = $ninjaboardConfig->getBoardSettings('enable_emoticons');
		
		$this->assign('enableBBCode' , $enableBBCode);
		$this->assign('enableEmoticons'  , $enableEmoticons);
			
		$lists['bbcode'] = JHTML::_('select.booleanlist', 'enable_bbcode', '', $enableBBCode);		
		$lists['enableemoticons'] = JHTML::_('select.booleanlist', 'enable_emoticons', '', $enableEmoticons);
		$lists['notifyonreply'] = JHTML::_('select.booleanlist', 'notify_on_reply', '', $notifyOnReply);

		$this->assign('topic' , $topic);
		$this->assignRef('lists', $lists);

		$action = JRoute::_('index.php?option=com_ninjaboard');
		$this->assignRef('action', $action);
		
		// get buttons
		$ninjaboardButtonSet	=& NinjaboardButtonSet::getInstance();
		$this->assignRef('buttonSubmit', $ninjaboardButtonSet->buttonByFunction['buttonSubmit']);
		$this->assignRef('buttonPreview', $ninjaboardButtonSet->buttonByFunction['buttonPreview']);
		$this->assignRef('buttonCancel', $ninjaboardButtonSet->buttonByFunction['buttonCancel']);
		$this->assignRef('buttonAddFile', $ninjaboardButtonSet->buttonByFunction['buttonAddFile']);
		$this->assignRef('buttonRemoveFile', $ninjaboardButtonSet->buttonByFunction['buttonRemoveFile']);
		
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
