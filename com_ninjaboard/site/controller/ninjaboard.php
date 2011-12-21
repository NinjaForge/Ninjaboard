<?php defined('_JEXEC') or die('Restricted access');
/**
 * @version $Id: ninjaboard.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 */
 
jimport('joomla.application.component.controller');

/**
 * Ninjaboard Controller
 *
 * @package Ninjaboard
 */
class NinjaboardController extends JController
{
	/**
	 * display ninjaboard
	 */
	function display() {

		// initialize variables
		$document			=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$menu				=& JSite::getMenu();
		
		$fp 				= FirePHP::getInstance(true);
		//if(!$ninjaboardConfig->getBoardSettings('debug')){
		//	$fp->setEnabled(false);
		//}

		// initialize variables
		$item = $menu->getActive();
		
		if (! $ninjaboardConfig->getBoardSettings('published') && JRequest::getVar('info') != 'board_offline')
		{
			$this->setRedirect(JRoute::_('index.php?option=com_ninjaboard&view=information&info=board_offline&Itemid='. $item->id, false));

			return;
		}
		else
			unset($menu);

		$viewName	=  JRequest::getVar('view');
		$viewType	=  $document->getType();
		$view		=& $this->getView($viewName, $viewType);
		
		/*
		 * Setup bbcode
		 */
		$fp->log('Setup bbcode');
		
		//use this so we don't double up in the code tag beatifier's initializer JS 
		$codeInitialized = 0;
		$view->assign('codeInitialized', $codeInitialized);
		
		//load our BBcode parser - Dan: This probably shouldn't be in the controller but I can't think of a better place for it
			require_once(NB_BASEPATH.DS.'system'.DS.'stringparser_bbcode.class.php');
			$bbcode = new StringParser_BBCode ();
			
			//Initialise the settings
			// Unify line breaks of different operating systems
			function convertlinebreaks ($text) {
			    $text = preg_replace ("/\015\012|\015|\012/", "\n", $text);
				$text = str_replace ("<br />", "", $text);
				$text = str_replace ("<br/>", "", $text);
				return str_replace ("<br>", "", $text);
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
			
			function do_bbcode_code ($action, $attributes, $content, $params, $node_object) {
			   if (!isset ($attributes['default'])) {
			        $brush='php';
			    } else {
			    	
			         $brush=strtolower($attributes['default']);
			    }
				
				//TODO - load the code parser JS into memory
				$document= & JFactory::getDocument();
			     if ($document)
			     {
			        $document->addStylesheet(NB_TEMPLATES_LIVE.'/ninjaboard/js/codestyler/styles/shCore.css');
					$document->addStylesheet(NB_TEMPLATES_LIVE.'/ninjaboard/js/codestyler/styles/shThemeDefault.css');
					$document->addScript(NB_TEMPLATES_LIVE.'/ninjaboard/js/codestyler/scripts/shCore.js');
					
					//if a user entered a parameter for the code type, then check that a brush exists for it.
					//if the bursh doesn't exist, just use the plain brush
					$searchFile = NB_TEMPLATES.'/ninjaboard/js/codestyler/scripts/shBrush'.$brush.'.js';
					
					if (!file_exists($searchFile)){
						$brush = 'plain';
					}
					
					$document->addScript(NB_TEMPLATES_LIVE.'/ninjaboard/js/codestyler/scripts/shBrush'.$brush.'.js');
					
					if (!$view->codeInitialized){
						$initialiserJS='	SyntaxHighlighter.config.clipboardSwf = "'.NB_TEMPLATES_LIVE.'/ninjaboard/js/codestyler/scripts/clipboard.swf";
										SyntaxHighlighter.all();';
						
						$document->addScriptDeclaration($initialiserJS);
					
						$view->codeInitialized = 1;
					}
			     } 
				//strip any line breaks out of the code 
				$content= str_replace ("<br />", "", $content);
				$content= str_replace ("<br>", "", $content);
				$content= str_replace ("<br/>", "", $content);
			   
			   return '<div class="nbCode"><pre class="brush:'.$brush.';stripBrs:true;">'.$content.'</pre></div>';
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
			
			$bbcode->addFilter (1, 'convertlinebreaks');
			
			$bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'htmlspecialchars');
			$bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'nl2br');
			$bbcode->addParser ('list', 'bbcode_stripcontents');
				
			$bbcode->addCode ('b', 'simple_replace', null, array ('start_tag' => '<strong>', 'end_tag' => '</strong>'),
			                  'inline', array ('listitem', 'block', 'inline', 'link'), array ());
			$bbcode->addCode ('code', 'usecontent', 'do_bbcode_code', array ('usecontent_param' => 'default'),
			                  'block', array ('block'), array ('listitem', 'inline', 'link'));
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
			$bbcode->setRootParagraphHandling (true);
				
		
			$view->assign('bbcode', $bbcode);
		/*
		 * End setup bbcode
		 */
		
		// set the layout
		$view->setLayout('ninjaboard');
				
		$model =& $this->getModel($viewName);

		if (!JError::isError($model))
			$view->setModel($model, true);

		// assign document
		$view->assignRef('document', $document);

		// add style
		$document->addStyleSheet(NinjaboardHelper::getStyleSheet(true));
		
		// add template path
		$view->addTemplatePath(NinjaboardHelper::getTemplatePath().'/controller');	
		$view->addTemplatePath(NinjaboardHelper::getTemplatePath());

		/**
		 * ToDo: find a better solution!
		 */
				
		// add template language
		$langFile = NinjaboardHelper::getTemplatePath().DS.'language'.DS.NinjaboardHelper::getLocale().DS.str_replace('.xml', '.ini', $ninjaboardConfig->getTemplateFile());
		
		if (!is_file($langFile))
			$langFile = NinjaboardHelper::getTemplatePath().DS.'language'.DS.'en-GB'.DS.str_replace('.xml', '.ini', $ninjaboardConfig->getTemplateFile());	
		
		$lang =& JFactory::getLanguage();
		$lang->_load($langFile); // sorry for calling private function, but there is no other solution at the moment!!!
	
		// assign layout
		$view->assignRef('layout', $ninjaboardConfig->getLayoutName());
					
		// assign item
		$view->assignRef('item', $item);
		
		// assign item params
		$params = new JParameter($item->params);
		$view->assignRef('params', $params);
		
		// assign style sheet path
		$view->assign('styleSheetPath', NinjaboardHelper::getStyleSheetPath());
				
		// assign style sheet path live
		$view->assign('styleSheetPathLive', NinjaboardHelper::getStyleSheetPath(true));
		
		// assign template path
		$view->assign('templatePath', NinjaboardHelper::getTemplatePath());
		
		// assign template path live
		$view->assign('templatePathLive', NinjaboardHelper::getTemplatePath(true));
		
		// assign board name	
		$view->assign('boardName', $ninjaboardConfig->getBoardSettings('board_name'));
		
		// assign allow user registration
		$usersConfig =& JComponentHelper::getParams('com_users');
		$view->assign('allowUserRegistration', $usersConfig->get('allowUserRegistration'));
		
		// assign some global variables
		$view->assign('showBoxLatestItems', $ninjaboardConfig->getViewSettings('show_latestitems'));
		$view->assign('showBoxStatistic', $ninjaboardConfig->getViewSettings('show_statistic'));
		$view->assign('showBoxWhosOnline', $ninjaboardConfig->getViewSettings('show_whosonline'));
		$view->assign('showBoxLegend', $ninjaboardConfig->getViewSettings('show_legend'));
		$view->assign('showBoxFooter', $ninjaboardConfig->getViewSettings('show_footer'));
		$view->assign('footerShowMyProfile', $ninjaboardConfig->getViewFooterSettings('show_myprofile'));
		$view->assign('footerShowLogout', $ninjaboardConfig->getViewFooterSettings('show_logout'));
		$view->assign('footerShowLogin', $ninjaboardConfig->getViewFooterSettings('show_login'));
		$view->assign('footerShowRegister', $ninjaboardConfig->getViewFooterSettings('show_register'));
		$view->assign('footerShowSearch', $ninjaboardConfig->getViewFooterSettings('show_search'));
		$view->assign('footerShowLatestPosts', $ninjaboardConfig->getViewFooterSettings('show_latestposts'));
		$view->assign('footerShowUserList', $ninjaboardConfig->getViewFooterSettings('show_userlist'));
		$view->assign('footerShowTerms', $ninjaboardConfig->getViewFooterSettings('show_terms'));
		
		// assign bread crumb
		$breadCrumbs   =& NinjaboardBreadCrumbs::getInstance();
		$breadCrumbUrl =  JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$item->id);
		$breadCrumbs->addBreadCrumb($ninjaboardConfig->getBoardSettings('breadcrumb_index'), $breadCrumbUrl);
		$view->assignRef('breadCrumbs', $breadCrumbs);
		
		// assign current url
		if ($viewName != 'login')
		{
			$uri = JUri::getInstance();

			$view->assign('redirect', $uri->toString());
		}
		else
			$view->assign('redirect', JRequest::getVar('redirect'));
		
		// assign current time
		$currentTime = NinjaboardHelper::Date(gmdate("Y-m-d H:i:s"));
		$view->assignRef('currentTime', $currentTime);
		
		// assign current time zone name
		$currentTimeZoneName = NinjaboardHelper::getTimeZoneName();
		$view->assignRef('currentTimeZoneName', $currentTimeZoneName);		
		
		// assign current ninjaboard user
		$ninjaboardUser =& NinjaboardHelper::getNinjaboardUser();
		$view->assignRef('ninjaboardUser', $ninjaboardUser);

		// assign item id
		$view->assign('Itemid', $item->id);

		// display the view
		$view->assign('error', $this->getError());
		$view->display();

	}
	
	/**
	 * preview post (topic or post)
	 */
	function ninjaboardPreview()
	{
		// initialize variables
		$task		= JRequest::getVar('task');
		$Itemid		= JRequest::getInt('Itemid');
		$post		= JRequest::get('post');
		
		$session =& JFactory::getSession();
		$session->set('ninjaboardPost', $post);

		switch ($task)
		{
			case 'ninjaboardpreviewtopic':
				if ($post['id_post'] == 0)
					$link = JRoute::_('index.php?option=com_ninjaboard&view=edittopic&forum='.(int)$post['id_forum'].'&topic='.(int)$post['id_topic'].'&Itemid='.$Itemid, false);
				else
					$link = JRoute::_('index.php?option=com_ninjaboard&view=edittopic&topic='.(int)$post['id_topic'].'&post='.(int)$post['id_post'].'&Itemid='.$Itemid, false);
				break;
			case 'ninjaboardpreviewpost':
				$link = JRoute::_('index.php?option=com_ninjaboard&view=editpost&topic='.(int)$post['id_topic'].'&post='.(int)$post['id_post'].'&Itemid='.$Itemid, false);
				break;
			default:
				$link = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$Itemid, false);
		}

		$this->setRedirect($link);
	}
	
	/**
	 * save the topic
	 */
	function ninjaboardSaveTopic()
	{
		JRequest::checkToken() or jexit('Invalid Token.');

		// initialize variables
		$db					=& JFactory::getDBO();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$messageQueue		=& NinjaboardMessageQueue::getInstance();
		$syslog				=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$task				=  JRequest::getVar('task', 'savetopic', 'POST', 'STRING');
		$Itemid				=  JRequest::getVar('Itemid',     0,  'POST', 'INT');
		$post				=  JRequest::get('post');

		# ToDo: Check out how this automated was (think JTable) works.
		$post['enable_bbcode']    = (int) $post['enable_bbcode'];
		$post['enable_emoticons'] = (int) $post['enable_emoticons'];
		$post['notify_on_reply']  = (int) $post['notify_on_reply'];
		$post['type']             = (int) $post['type'];
		$post['id_forum']         = (int) $post['id_forum'];
		$post['id_topic']         = (int) $post['id_topic'];
		$post['id_post']          = (int) $post['id_post'];

		#JArrayHelper::toInteger();


		// flood interval
		$time = NinjaboardHelper::getLastPostTimeByIP($_SERVER['REMOTE_ADDR']);
		if (isset($time))
		{
			$floodInterval = $ninjaboardConfig->getBoardSettings('flood_interval');

			if ((strtotime(gmdate("Y-m-d H:i:s")) - strtotime($time)) < $floodInterval)
			{
				$msg = JText::sprintf('NB_MSGFLOODINTERVAL', $floodInterval);

				$syslog->write($msg, 'warning', __LINE__);

				$messageQueue->addMessage($msg);

				$this->setRedirect(
					JRoute::_('index.php?option=com_ninjaboard&view=edittopic&forum='.$post['id_forum'].'&topic='.$post['id_topic'].'&Itemid='.$Itemid, false)
				);

				return;
			}
		}
		
		// check authorization to prevent spoofing and hacking
		$ninjaboardAuth =& NinjaboardAuth::getInstance();

		if (!$ninjaboardAuth->getAuth('auth_post', $post['id_forum']))
		{

			$ninjaboardForum =& JTable::getInstance('NinjaboardForum');
			$ninjaboardForum->load($post['id_forum']);

			$messageQueue->addMessage(
				JText::sprintf('NB_MSGNOACCESSPOST', $ninjaboardForum->name)
			);

			$this->setRedirect(
				JRoute::_('index.php?option=com_ninjaboard&view=forum&forum='.$post['id_forum'].'&Itemid='.$Itemid, false)
			);

			return;
		}

		$isNew = $post['id_topic'] < 1;

		$ninjaboardTopic =& JTable::getInstance('NinjaboardTopic');

		if (!$ninjaboardTopic->bind($post))
		{
			$msg = $ninjaboardTopic->getError();

			$syslog->write($msg, 'error', __LINE__);

			$messageQueue->addMessage($msg);

			return;
		}
				
		$ninjaboardTopic->id = $post['id_topic'];

		if (!$ninjaboardTopic->store())
		{
			$msg = $ninjaboardTopic->getError();

			$syslog->write($msg, 'error', __LINE__);

			$messageQueue->addMessage($msg);

			return;
		}

		$ninjaboardPost =& JTable::getInstance('NinjaboardPost');

		if (!$ninjaboardPost->bind($post))
		{
			$msg = $ninjaboardPost->getError();

			$syslog->write($msg, 'error', __LINE__);

			$messageQueue->addMessage($msg);

			return;
		}
		
		$ninjaboardPost->id = $post['id_post'];
		
		if ($isNew)
		{
			$ninjaboardPost->id_topic	= $ninjaboardTopic->id;
			$ninjaboardPost->date_post	= gmdate("Y-m-d H:i:s");
			$ninjaboardPost->id_user	= $ninjaboardUser->get('id');
			$ninjaboardPost->ip_poster	= $_SERVER['REMOTE_ADDR'];
		}
		else
			$ninjaboardPost->date_last_edit = gmdate("Y-m-d H:i:s");
		
		if (!$ninjaboardPost->store())
		{
			$msg = $ninjaboardPost->getError();

			$syslog->write($msg, 'error', __LINE__);

			$messageQueue->addMessage($msg);

			return;
		}
		
		// rework topic
		if ($isNew)
		{
			$ninjaboardTopic->id_first_post	= $ninjaboardPost->id;
			$ninjaboardTopic->id_last_post	= $ninjaboardPost->id;
		}
		
		if (!$ninjaboardTopic->store())
		{
			$msg = $ninjaboardTopic->getError();

			$syslog->write($msg, 'error', __LINE__);

			$messageQueue->addMessage($msg);

			return;
		}
		
		// rework forum
		if ($isNew)
		{		
			$ninjaboardForum =& JTable::getInstance('NinjaboardForum');
			$ninjaboardForum->load($ninjaboardTopic->id_forum);
			$ninjaboardForum->posts++;
			$ninjaboardForum->topics++;
			$ninjaboardForum->id_last_post = $ninjaboardPost->id;
			
			if (!$ninjaboardForum->store())
			{
				$msg = $ninjaboardForum->getError();

				$syslog->write($msg, 'error', __LINE__);

				$messageQueue->addMessage($msg);

				return;
			}
		
			$userPosts = $ninjaboardUser->get('posts');
			$userPosts++;
			$ninjaboardUser->set('posts', $userPosts);
			$ninjaboardUser->save();
	
		}
	
		// save guest user
		$ninjaboardUser->saveGuestUser($ninjaboardPost->id , $post['guest_name']);
		
		//remove any unwanted attachments if we are editing
		$post = JRequest::get('post');
		
		$removeAttachArray = $post['removeattach'];
		
		if (is_array($removeAttachArray)){	
			foreach ($removeAttachArray as $removeAttachFile)
			{
				NinjaboardHelper::removeAttachment($removeAttachFile);
					
			}
		}
				
		if (is_array($_FILES['attachmentList']['name'])) {
			// upload all selected attachments	
			
			$fileCount = count($_FILES['attachmentList']['name']);
				
			for ($i = 0; $i < $fileCount; $i++ ) {
			
			//check there is a file to upload incase they left a blank upload box
				if ($_FILES['attachmentList']['name'][$i])
					NinjaboardHelper::saveAttachment($ninjaboardPost->id, $ninjaboardPost->id_user, $i);
			
			}
		} 
		
		$msg = JText::_('NB_MSGTOPICSAVED');
		$link = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$ninjaboardTopic->id.'&Itemid='.$Itemid, false);
		$messageQueue->addMessage($msg);
		$this->setRedirect($link);		
	}
	
	/**
	 * saves the post
	 */
	function ninjaboardSavePost()
	{
		JRequest::checkToken() or jexit('Invalid Token.');

		// initialize variables
		$app				=& JFactory::getApplication();
		$db					=& JFactory::getDBO();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$messageQueue		=& NinjaboardMessageQueue::getInstance();
		$syslog				=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$task				=  JRequest::getCmd('task');
		$Itemid				=  JRequest::getInt('Itemid');
		$post				=  JRequest::get('post');

		# ToDo: Check out how this automated was (think JTable) works.
		$post['enable_bbcode']    = (int) $post['enable_bbcode'];
		$post['enable_emoticons'] = (int) $post['enable_emoticons'];
		$post['notify_on_reply']  = (int) $post['notify_on_reply'];
		$post['type']             = (int) $post['type'];
		$post['id_forum']         = (int) $post['id_forum'];
		$post['id_topic']         = (int) $post['id_topic'];
		$post['id_post']          = (int) $post['id_post'];
		
		// flood interval
		$time = NinjaboardHelper::getLastPostTimeByIP($_SERVER['REMOTE_ADDR']);

		if (isset($time))
		{
			$floodInterval = $ninjaboardConfig->getBoardSettings('flood_interval');

			if ((strtotime(gmdate("Y-m-d H:i:s")) - strtotime($time)) < $floodInterval)
			{
				$msg = JText::sprintf('NB_MSGFLOODINTERVAL', $floodInterval);

				$syslog->write($msg, 'warning', __LINE__);

				$messageQueue->addMessage($msg);

				$this->setRedirect(
					JRoute::_('index.php?option=com_ninjaboard&view=editpost&topic='.$post['id_topic'].'&post='.$post['id_post'].'&Itemid='.$Itemid, false)
				);

				return;
			}
		}
		
		// check authorization to prevent spoofing and hacking
		$ninjaboardAuth =& NinjaboardAuth::getInstance();

		if (!$ninjaboardAuth->getAuth('auth_reply', $post['id_forum']))
		{
			$ninjaboardForum =& JTable::getInstance('NinjaboardForum');
			$ninjaboardForum->load($post['id_forum']);

			$messageQueue->addMessage(
				JText::sprintf('NB_MSGNOACCESSREPLY', $ninjaboardForum->name)
			);

			$app->redirect(
				JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='. $post['id_topic'] .'&Itemid='. $Itemid, false)
			);
		}

		$isNew = $post['id_post'] < 1;
		
		$ninjaboardPost = & JTable::getInstance('NinjaboardPost');

		if (!$ninjaboardPost->bind($post))
		{
			$msg = $ninjaboardPost->getError();

			$syslog->write($msg, 'error', __LINE__);

			$messageQueue->addMessage($msg);

			return;
		}
		
		$ninjaboardPost->id = $post['id_post'];
	//TODO - Dan. Put code here to store the parent post
		if ($isNew)
		{
			$ninjaboardPost->date_post = gmdate("Y-m-d H:i:s");
			$ninjaboardPost->id_user	= $ninjaboardUser->get('id');
		
	//TODO - put some secure code here to get the name and email of the user if a guest	
		/*	if (!$ninjaboardUser->get('id'))
			{
				$ninjaboardPost->guest_name	= JRequest::get('post')
			}*/
			
			$ninjaboardPost->ip_poster = $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			$ninjaboardPost->date_last_edit = gmdate("Y-m-d H:i:s");
	//TODO - Dan. Put code here for edit reason and who did it
		}
		
		if (!$ninjaboardPost->subject)
		{
			$ninjaboardTopic     =& JTable::getInstance('NinjaboardTopic');
			$ninjaboardTopicPost =& JTable::getInstance('NinjaboardPost');

			$ninjaboardTopic->load($ninjaboardPost->id_topic);
			$ninjaboardTopicPost->load($ninjaboardTopic->id_first_post);
			$ninjaboardPost->subject = 'Re: '. $ninjaboardTopicPost->subject;		
		}

		if (!$ninjaboardPost->store())
		{
					# ToDo: Syslogging.
			$messageQueue->addMessage($post->getError());

			return;
		}
		
		if ($isNew)
		{
			// rework topic		
			$ninjaboardTopic =& JTable::getInstance('NinjaboardTopic');
			$ninjaboardTopic->load($ninjaboardPost->id_topic);
			$ninjaboardTopic->replies++;
			$ninjaboardTopic->id_last_post = $ninjaboardPost->id;

			if (!$ninjaboardTopic->store())
			{
				$msg = $ninjaboardTopic->getError();

				$syslog->write($msg, 'error', __LINE__);

				$messageQueue->addMessage($msg);

				return;
			}
			
			// rework forum
			$ninjaboardForum =& JTable::getInstance('NinjaboardForum');
			$ninjaboardForum->load($ninjaboardPost->id_forum);
			$ninjaboardForum->posts++;
			$ninjaboardForum->id_last_post = $ninjaboardPost->id;
			
			if (!$ninjaboardForum->store())
			{
				$msg = $ninjaboardForum->getError();

				$syslog->write($msg, 'error', __LINE__);

				$messageQueue->addMessage($msg);

				return;
			}
				
			$userPosts = $ninjaboardUser->get('posts');
			$userPosts++;

			$ninjaboardUser->set('posts', $userPosts);
			$ninjaboardUser->save();
	
			$ninjaboardMail =& NinjaboardMail::getInstance();	
			$ninjaboardMail->sendNotifyOnReplyMail($ninjaboardPost->id_topic);
		}
	
		// save guest user
		$ninjaboardUser->saveGuestUser($ninjaboardPost->id , $post['guest_name']);

	//remove any unwanted attachments if we are editing
		$post = JRequest::get('post');
		
		$removeAttachArray = $post['removeattach'];
		
		if (is_array($removeAttachArray)){	
			foreach ($removeAttachArray as $removeAttachFile)
			{
				NinjaboardHelper::removeAttachment($removeAttachFile);
					
			}
		}
		
		if (is_array($_FILES['attachmentList']['name'])) {
			// upload all selected attachments	
			
			$fileCount = count($_FILES['attachmentList']['name']);
				
			for ($i = 0; $i < $fileCount; $i++ ) {
			
				NinjaboardHelper::saveAttachment($ninjaboardPost->id, $ninjaboardPost->id_user, $i);
			
			}
		} 

		
		
		$msg =  JText::_('NB_MSGPOSTSAVED');
		$messageQueue->addMessage($msg);
		
		$link = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$ninjaboardPost->id_topic.'&Itemid='.$Itemid, false);
		$this->setRedirect($link);
	}
	
	/**
	 * delete the topic
	 */
	function ninjaboardDeleteTopic()
	{
		// initialize variables
		$db					=& JFactory::getDBO();
		$ninjaboardTopic	=& JTable::getInstance('NinjaboardTopic');
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$messageQueue		=& NinjaboardMessageQueue::getInstance();
		$syslog				=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$topicId			=  JRequest::getInt('topic', 0);
		$Itemid				=  JRequest::getInt('Itemid');
		
			
		$ninjaboardTopic =& JTable::getInstance('NinjaboardTopic');
		
		if ($ninjaboardTopic->load($topicId)) {
		
			$forumId = $ninjaboardTopic->id_forum;
			$topicLastPostId = $ninjaboardTopic->id_last_post;
			
			//Before deleting, get all the posts associated with this topic, so we can get rid of the attachments
			$db->setQuery('
				SELECT * FROM #__ninjaboard_posts
				WHERE id_topic = '.$topicId
			);
			
			$deletedPostsList = $db->loadObjectList();
			
			// delete all topic depended posts first
			$db->setQuery('
				DELETE FROM #__ninjaboard_posts
				WHERE id_topic = '.$topicId
			);
			
			if (!$db->query())
			{
				$messageQueue->addMessage(
					JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()), 'error'
				);
				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				return;
			}
			
			$postsDeleted = $db->getAffectedRows();

			// delete the topic
			if (!$ninjaboardTopic->delete())
			{
				$msg = $ninjaboardTopic->getError();

				$syslog->write($msg, 'error', __LINE__);

				$messageQueue->addMessage($msg);

				return;
			}
			
			// rework forum
			$ninjaboardForum =& JTable::getInstance('NinjaboardForum');

			if ($ninjaboardForum->load($forumId))
			{
				$ninjaboardForum->posts -= $postsDeleted;
				$ninjaboardForum->topics--;

				if ($ninjaboardForum->id_last_post == $topicLastPostId)
					$ninjaboardForum->id_last_post = NinjaboardHelper::getLastPostIdForum($forumId);
				
				if (!$ninjaboardForum->store())
				{
					$msg = $ninjaboardForum->getError();

					$syslog->write($msg, 'error', __LINE__);

					$messageQueue->addMessage($msg);
					
					return;
				}
			}
			
			//Delete the attachments for all posts in this topic
			foreach ($deletedPostsList as $deletedPost){
			//Remove any attachments
			//First delete the files				
				$db->setQuery("SELECT * FROM #__ninjaboard_attachments WHERE id_post = ".$deletedPost->id);
				$attachmentsList = $db->loadObjectList();
				
				foreach ($attachmentsList as $attachment){
					NinjaboardHelper::removeAttachment($attachment->id);
				}
				//Then delete the records which point to the files
				$db->setQuery('DELETE FROM #__ninjaboard_attachments WHERE id_post = '.$deletedPost->id);
				if (!$db->query()) {
					$msg = $db->getErrorMsg();

					$syslog->write($msg, 'error', __LINE__);

					$messageQueue->addMessage($msg);
					
					return;
				}
			}
						
			$msg  = JText::_('NB_MSGTOPICDELETED');
			$link = JRoute::_('index.php?option=com_ninjaboard&view=forum&forum='.$forumId.'&Itemid='.$Itemid, false);			
		}
		else
		{
			$msg  = JText::sprintf('NB_MSGTOPICNOTFOUND', $topicId);
			$link = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$Itemid, false);			
		}

		$messageQueue->addMessage($msg);
		$this->setRedirect($link);
	}
	
	/**
	 * delete the topic
	 */
	function ninjaboardDeletePost()
	{
		// initialize variables
		$db				=& JFactory::getDBO();
		$ninjaboardPost =& JTable::getInstance('NinjaboardPost');
		$ninjaboardUser	=& NinjaboardHelper::getNinjaboardUser();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$syslog			=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$postId			=  JRequest::getInt('post', 0);
		$Itemid			=  JRequest::getInt('Itemid');
		
		if ($ninjaboardPost->load($postId))
		{
			$topicId = $ninjaboardPost->id_topic;
			$forumId = $ninjaboardPost->id_forum;
				
			// delete the post
			if (!$ninjaboardPost->delete())
			{
				$msg = $ninjaboardPost->getError();

				$syslog->write($msg, 'error', __LINE__);

				$messageQueue->addMessage($msg);

				return;
			}

			// rework topic		
			$ninjaboardTopic =& JTable::getInstance('NinjaboardTopic');

			if ($ninjaboardTopic->load($topicId))
			{
				$ninjaboardTopic->replies--;

				if ($ninjaboardTopic->id_last_post == $postId)
					$ninjaboardTopic->id_last_post = NinjaboardHelper::getLastPostIdTopic($topicId);
		
				if (!$ninjaboardTopic->store())
				{
					$msg = $ninjaboardTopic->getError();

					$syslog->write($msg, 'error', __LINE__);

					$messageQueue->addMessage($msg);

					return;
				}
			}
			
			// rework forum
			$ninjaboardForum =& JTable::getInstance('NinjaboardForum');

			if ($ninjaboardForum->load($forumId))
			{
				$ninjaboardForum->posts--;

				if ($ninjaboardForum->id_last_post == $postId)
					$ninjaboardForum->id_last_post = NinjaboardHelper::getLastPostIdForum($forumId);
				
				if (!$ninjaboardForum->store())
				{
					$msg = $ninjaboardForum->getError();

					$syslog->write($msg, 'error', __LINE__);

					$messageQueue->addMessage($msg);

					return;
				}
			}
				
			
			//Remove any attachments
			//First delete the files
			$db->setQuery("SELECT * FROM #__ninjaboard_attachments WHERE id_post = ".$postId);
			$attachmentsList = $db->loadObjectList();
			
			foreach ($attachmentsList as $attachment){
				NinjaboardHelper::removeAttachment($attachment->id);
			}
			//Then delete the records which point to the files
			$db->setQuery('DELETE FROM #__ninjaboard_attachments WHERE id_post = '.$postId);
			if (!$db->query()) {
				$msg = $db->getErrorMsg();

				$syslog->write($msg, 'error', __LINE__);

				$messageQueue->addMessage($msg);

				return;
			} 
			
			$msg  = JText::_('NB_MSGPOSTDELETED');
			$link = JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$topicId.'&Itemid='.$Itemid, false);
		}
		else
		{
			$msg  = JText::sprintf('NB_MSGPOSTNOTFOUND', $postId);
			$link = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$Itemid, false);
		}
		
		$messageQueue->addMessage($msg);
		$this->setRedirect($link);
	}
			
	/**
	 * login
	 */
	function ninjaboardLogin()
	{

		// initialize variables
		$app			=& JFactory::getApplication();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$syslog			=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$username		=  JRequest::getVar('login_username', '', 'method', 'username');
		$password		=  JRequest::getString('login_password', '', 'post', JREQUEST_ALLOWRAW);		
		$redirect		=  JRequest::getVar('redirect');
		$Itemid			=  JRequest::getInt('Itemid');
		

		if (empty($username))
		{
			$messageQueue->addMessage(JText::_('NB_MSGENTERUSERNAME'));

			$this->setRedirect(
				JRoute::_('index.php?option=com_ninjaboard&view=login&Itemid='.$Itemid, false)
			);
			return;
		}
		elseif (empty($password))
		{
			$messageQueue->addMessage(JText::_('NB_MSGENTERPASS'));

			$this->setRedirect(
				JRoute::_('index.php?option=com_ninjaboard&view=login&Itemid='.$Itemid, false)
			);
			return;
		}
		
		if (empty($redirect))
		{
			// if we don't have redirect url, we simply redirect to board index

			$redirect = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$Itemid, false);
		}
		
		$options = array(
			'remember'	=> JRequest::getBool('remember', false),
			'return'	=> $redirect
		);
		$credentials = array(
			'username' => $username,
			'password' => $password
		);
		$error = $app->login($credentials, $options);
		
		if (JError::isError($error))
		{
			$redirect = JRoute::_('index.php?option=com_ninjaboard&view=login&Itemid='.$Itemid, false);
			$messages = $app->getMessageQueue();
			
			// ToDo: check why this doesn't work?!
			// $messageQueue->addMessage( JText::_($application->getError(true)));
			
			if (is_array($messages) && count($messages))
			{
				// pass through joomla messages

				foreach ($messages as $msg)
				{
					if (isset($msg['message']))
					{
						$messageQueue->addMessage($msg['message']);
					}
				}
			}
			// ToDo: sorry for that... Joomla should have an unqueue function
			//       or a passing through message queue or something like that
			$app->_messageQueue = null;
		}

		$this->setRedirect($redirect);
	}
			
	/**
	 * logout
	 */
	function ninjaboardLogout()
	{
		
		// initialize variables
		$app			=& JFactory::getApplication();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$Itemid			=  JRequest::getInt('Itemid');
		
		//preform the logout action
		$error = $app->logout();
		
		// destroy the session
		$session			=& JFactory::getSession();
		$ninjaboardSession	=& JTable::getInstance('ninjaboardsession');
		$ninjaboardSession->destroy($session->getId());
		
		$messageQueue->addMessage(JText::_('NB_MSGLOGGEDOUT'));
		$this->setRedirect(JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$Itemid, false));
	}
		
	/**
	 * saves the profile
	 */
	function ninjaboardSaveProfile()
	{
		JRequest::checkToken() or jexit('Invalid Token,.');

		// initialize variables
		$db				=& JFactory::getDBO();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$ninjaboardUser	=& NinjaboardHelper::getNinjaboardUser();
		$syslog			=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$Itemid			= JRequest::getInt('Itemid');

		// ToDo: remember last page and redirect user after saving profile
		
		$redirect = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$Itemid, false);
		$msg      = JText::_('NB_MSGPROFILESAVED');
				
		// get the request data
		$post = JRequest::get('post');

		foreach($post as $key => $value)
		{
			//prevent form injection

			$post[$key] = htmlspecialchars($value);
		}
		
		$forbiddenFields = array('usertype','block','sendEmail','gid','registerDate','lastvisitDate','activation','params','posts','role','system_emails');
		$formHack = false;

		foreach($forbiddenFields as $field)
		{
			if (array_key_exists($field, $post))

				$formHack = true;
		}
		
		if ($formHack || $ninjaboardUser->get('id') != $post['id'])
		{
			// do we have a form hack or is someone trying to edit other users profile? 

			$msg = JText::_('NB_MSGNOPERMISSIONCHANGEPROFILE');

			$syslog->write($msg, 'warning', __LINE__);

			$messageQueue->addMessage($msg);

			$this->setRedirect(JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$Itemid, false));

			return;
		}

		if (!$ninjaboardUser->saveProfile($post))
		{
			$redirect = JRoute::_('index.php?option=com_ninjaboard&view=editprofile&Itemid='.$Itemid, false);
			$msg      = JText::_($ninjaboardUser->getError());		

			$syslog->write($msg, 'error', __LINE__);
		}
		
		$messageQueue->addMessage($msg);

		$this->setRedirect($redirect. $result);				
	}
	
	/**
	 * register profile
	 */
	function ninjaboardRegisterProfile()
	{
	
		// initialize variables
		$db					=& JFactory::getDBO();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$messageQueue		=& NinjaboardMessageQueue::getInstance();
		$syslog				=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$ninjaboardUser		= new NinjaboardUser(0, true);
		$usersConfig		=& JComponentHelper::getParams('com_users');
		$Itemid				=  JRequest::getVar('Itemid');

		// is registration allowed?
		if (!$usersConfig->get('allowUserRegistration'))
		{
			$messageQueue->addMessage(JText::_('NB_MSGREGISTRATIONNOTALLOWED'));

			$this->setRedirect(JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='.$this->Itemid, false));

			return;
		}		

		// get the request data
		$post = JRequest::get('post');

		if (!$ninjaboardUser->saveProfile($post))
		{
			$redirect = JRoute::_('index.php?option=com_ninjaboard&view=register&Itemid='.$Itemid, false);

			$msg = JText::_($ninjaboardUser->getError());
			
			$syslog->write($msg, 'error', __LINE__);

			$messageQueue->addMessage($msg);

			$session =& JFactory::getSession();
			$session->set('ninjaboardRegisterForm', $post);	
		}
		else
		{
			if ($ninjaboardConfig->getCaptchaSettings('captcha_register'))
			{
				if (md5($post['captchacode']) == $_SESSION['captcha_code'])
				{
					// do it!				
				}
				else
				{
					// don't do it!
				}
			}
		
			// send email
			$ninjaboardMail =& NinjaboardMail::getInstance();
			$ninjaboardMail->sendRegistrationMail($ninjaboardUser);
	
			$redirect = JRoute::_('index.php?option=com_ninjaboard&view=information&info=account_activation&user='.$ninjaboardUser->get('id').'&Itemid='.$Itemid, false);			
		}

		$this->setRedirect($redirect);		
	}	
	
	/**
	 * register profile
	 */
	function ninjaboardActivateProfile()
	{
		
		// initialize variables
		$db					=& JFactory::getDBO();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$messageQueue		=& NinjaboardMessageQueue::getInstance();
		$syslog				=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$Itemid				=  JRequest::getInt('Itemid');
		
		$activation = JRequest::getVar('activation', '');

		if ($activation)
		{
			$db->setQuery(
				'SELECT u.id
				    FROM #__users AS u
				    WHERE u.activation = '.$db->Quote($activation)
			);
			$userId = $db->loadResult();
			
			if ($userId)
			{
				$ninjaboardUser = NinjaboardUser::getInstance($userId);
				
				if ($ninjaboardUser)
				{
					$ninjaboardUser->set('activation', '');
					$ninjaboardUser->set('block', 0);
					$ninjaboardUser->save();
					
					// ToDo: what if admin changes account activation meanwhile?!
					if ($ninjaboardConfig->getBoardSettings('account_activation') == 2)
					{
						// send email
						$ninjaboardMail =& NinjaboardMail::getInstance();
						$ninjaboardMail->sendConfirmationMail($ninjaboardUser);
					}
					
					$redirect = JRoute::_('index.php?option=com_ninjaboard&view=information&info=account_activated&user='.$ninjaboardUser->get('id').'&Itemid='.$Itemid, false);
				}
				else
				{
					$redirect = JRoute::_('index.php?option=com_ninjaboard&view=information&info=account_activation_failed&Itemid='.$Itemid.'&user='.$userId.'&activation='.$activation, false);
				}			
			}
			else
			{
				$messageQueue->addMessage(
					JText::sprintf('NB_ERROR_SQL', $db->getErrorNum), 'error'
				);
				$syslog->write($db->getErrorMsg, 'error', __LINE__);

				$redirect = JRoute::_('index.php?option=com_ninjaboard&view=information&info=account_activation_failed&Itemid='.$Itemid, false);
			}

		}
		else
		{
			// ToDo: what if there is no activation code?
		}

		$this->setRedirect($redirect);		
	}
	
	/**
	 * request login
	 */
	function ninjaboardRequestLogin()
	{
		// initialize variables
		$db				=& JFactory::getDBO();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$syslog			=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$email			=  JRequest::getVar('email');
		$Itemid			=  JRequest::getInt('Itemid');

		$db->setQuery(
			'SELECT u.id
			    FROM #__users AS u
			    WHERE u.email = '.$db->Quote($email)
		);
		$userId = $db->loadResult();

		if ($userId)
		{
			// set activation code

			jimport('joomla.user.helper');

			$ninjaboardUser =& NinjaboardUser::getInstance($userId);
			$ninjaboardUser->set('activation', md5(JUserHelper::genRandomPassword()));
			
			// do not use ninjaboard user object to save activation 
			// user::save() disallow saving admin users by non admin
			$db->setQuery(
				'UPDATE #__users
				    SET activation = '.$db->Quote($ninjaboardUser->get('activation')).'
				    WHERE id = '.$userId.'
				    AND block = 0'
			);
			
			if ($db->query())
			{
				// send email

				$ninjaboardMail =& NinjaboardMail::getInstance();

				if ($ninjaboardMail->sendRequestLoginMail($ninjaboardUser))

					$redirect = JRoute::_('index.php?option=com_ninjaboard&view=information&info=request_login&Itemid='.$Itemid, false);

				else
				{
					// ToDo: unreachable for now: see NinjaboardHelper::sendRequestLoginMail!!!

					$redirect = JRoute::_('index.php?option=com_ninjaboard&view=requestlogin&Itemid='.$Itemid, false);

					$messageQueue->addMessage(
						JText::_('NB_MSGMAILNOTSENT')
					);					
				}
			}
			else
			{
				$redirect = JRoute::_('index.php?option=com_ninjaboard&view=requestlogin&Itemid='.$Itemid, false);

				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				$messageQueue->addMessage(
					JText::sprintf('NB_ERROR_SQL', $db->getErrorNum), 'error'
				);
			}
		}
		else
		{
			$redirect = JRoute::_('index.php?option=com_ninjaboard&view=requestlogin&Itemid='.$Itemid, false);

			$messageQueue->addMessage(
				JText::_('NB_MSGUSERNOTFOUND')
			);			
		}

		$this->setRedirect($redirect);
	}	
	
	/**
	 * request login
	 */
	function ninjaboardResetLogin()
	{
		// initialize variables
		$db				=& JFactory::getDBO();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$syslog			=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$ninjaboardUser	=& NinjaboardUser::getInstance(JRequest::getInt('id', 0));
		$activation		=  JRequest::getVar('activation', '');
		$Itemid			=  JRequest::getInt('Itemid');
	
		if ($ninjaboardUser->get('activation') == $activation)
		{
			// get the request data
		
			$password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
			$redirect = JRoute::_('index.php?option=com_ninjaboard&view=information&info=reset_success&Itemid='. $Itemid, false);
			
			// do not use ninjaboard user object to save activation. user::save() disallow saving admin users by non admin			
			jimport('joomla.user.helper');

			$salt		= JUserHelper::genRandomPassword(32);
			$crypt		= JUserHelper::getCryptedPassword($password, $salt);
			$password	= $crypt.':'.$salt;
	
			// Build the query
			$db->setQuery('
				UPDATE
					#__users
				SET
					password   = '.$db->Quote($password).',
					activation = ""
				WHERE
					id = '.(int) $ninjaboardUser->get('id').'
				AND
					activation = '.$db->Quote($activation).'
				AND
					block = 0
			');

			if (!$db->query())
			{
				$redirect = JRoute::_('index.php?option=com_ninjaboard&view=information&info=reset_failure&Itemid='. $Itemid, false);

				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				$messageQueue->addMessage(
					JText::_($ninjaboardUser->getError())
				);		
			}			
		}
		else
		{
			$redirect = JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='. $Itemid, false);

			$messageQueue->addMessage(
				JText::_('NB_MSGACTIVATIONFAILED')
			);
		}
		
		$this->setRedirect($redirect);		
	}
	
	/**
	 * report post
	 */
	function ninjaboardReportPost()
	{

		JRequest::checkToken() or jexit('Invalid Token,.');
	
		// initialize variables
		$ninjaboardMail	=& NinjaboardMail::getInstance();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$postId			= JRequest::getInt('post', 0);
		$reportComment	= JRequest::getVar('report_comment', '');
		$Itemid			= JRequest::getInt('Itemid');

		$ninjaboardMail->sendReportPostMail($postId, $reportComment);
		
		$this->setRedirect(
			JRoute::_('index.php?option=com_ninjaboard&view=board&Itemid='. $Itemid, false)
		);		
	}
	
	/**
	 * lock topic
	 */
	function ninjaboardLockTopic()
	{
	
		// initialize variables
		#$messageQueue	=& NinjaboardMessageQueue::getInstance();
		#$syslog			=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$row			=& JTable::getInstance('NinjaboardTopic');
		$topicId		=  JRequest::getInt('topic', 0);
		$Itemid			=  JRequest::getInt('Itemid');

		if ($row->load($topicId))
		{
			$row->status = 1;
			$row->store();
		}

		$this->setRedirect(
			JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$topicId.'&Itemid='. $Itemid, false)
		);
	}
	
	/**
	 * unlock topic
	 */
	function ninjaboardUnlockTopic()
	{
		# Instantiate objects and initialize variables
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$syslog			=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		$row			=& JTable::getInstance('NinjaboardTopic');
		$topicId		=  JRequest::getInt('topic', 0);
		$Itemid			=  JRequest::getInt('Itemid', 0);

		if ($row->load($topicId))
		{
			$row->status = 0;
			$row->store();
		}
	
		$this->setRedirect(
			JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$topicId.'&Itemid='.$Itemid, false)
		);
	}
	
	/**
	 * move topic
	 */
	function ninjaboardMoveTopic()
	{
		JRequest::checkToken() or jexit('Invalid Token.');

		# Instantiate objects
		$db				=& JFactory::getDBO();
		$ninjaboardAuth	=& NinjaboardAuth::getInstance();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();
		$syslog			=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);

		# Initialize parameters
		$sourceId	= JRequest::getInt('sforum', 0);
		$forumId	= JRequest::getInt('dforum', 0);
		$topicId	= JRequest::getInt('topic',  0);
		$Itemid		= JRequest::getInt('Itemid', 0);

		$messageQueue->addMessage(JText::_('NB_MSGTOPICMOVED'));
		

		if ($ninjaboardAuth->getUserRole($forumId) > 2)
		{
			# TODO: Put this into a model.
			####################################################################
			# 1. Update topics record

			$db->setQuery('
				UPDATE #__ninjaboard_topics
				SET id_forum = '.$forumId.'
				WHERE id = '.$topicId
			);
	
			if (!$db->query())
			{
				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				$messageQueue->addMessage(
					JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()), 'error'
				);
			}

			####################################################################
			# 2. Update posts record

			$db->setQuery('
				UPDATE #__ninjaboard_posts
				SET id_forum = '.$forumId.'
				WHERE id_topic = '.$topicId
			);
				
			if (!$db->query())
			{
				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				$messageQueue->addMessage(
					JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()), 'error'
				);
			}

			####################################################################
			# 3. Update forums records (topics/posts counts)

			# First we increase the destination counts.
			$db->setQuery('
				UPDATE #__ninjaboard_forums
				SET topics = topics + 1, posts = posts + (
					SELECT replies + 1 as posts
					FROM #__ninjaboard_topics
					WHERE id = '.$topicId.'
				)
				WHERE id = '.$forumId
			);
			if (!$db->query())
			{
				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				$messageQueue->addMessage(
					JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()), 'error'
				);
			}

			# Secind we decrease the source counts.
			$db->setQuery('
				UPDATE #__ninjaboard_forums
				SET topics = topics - 1, posts = posts - (
					SELECT replies + 1 as posts
					FROM #__ninjaboard_topics
					WHERE id = '.$topicId.'
				)
				WHERE id = '.$sourceId
			);
			if (!$db->query())
			{
				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				$messageQueue->addMessage(
					JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()), 'error'
				);
			}

			if (! $syslog->error)
				$syslog->write(
					JText::sprintf('NB_LOG_TOPICMOVED', $topicId, $sourceId, $forumId), 'info', __LINE__
				);
		}
		else
		{
			$msg = JText::sprintf('NB_MSGNOPERMISSION', JText::_('NB_MOVETOPIC'));

			$syslog->write($msg, 'warning', __LINE__);
			$messageQueue->addMessage($msg, 'alert');
		}
		
		$this->setRedirect(
			JRoute::_('index.php?option=com_ninjaboard&view=topic&topic='.$topicId.'&Itemid='. $Itemid, false)
		);
	}
	
	/**
	 * feed
	 */
	function ninjaboardFeed()
	{
		// initialize variables
		$ninjaboardFeed = NinjaboardFeed::getInstance();
		
		echo $ninjaboardFeed->createFeed();		
	}	
}
?>
