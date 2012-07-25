<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewPostHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->assign('params', $this->getService('com://admin/ninjaboard.model.settings')->getParams());
		$this->assign('me', $this->getService('com://admin/ninjaboard.model.people')->getMe());
	    $this->topicreview = false;
	
		$post					= $this->getModel()->getItem();
		if(!$post->ninjaboard_topic_id) $post->ninjaboard_topic_id = $this->getModel()->getState()->topic;
		$this->topic = $topic	= $this->getService('com://site/ninjaboard.model.topics')
									->id($post->ninjaboard_topic_id)
									->getItem();
		if(!$topic->forum_id) $topic->forum_id = KRequest::get('get.forum', 'int');
		$this->forum = $forum	= $this->getService('com://site/ninjaboard.model.forums')->id($topic->forum_id)->getItem();
		$this->topic->attachment_permissions = $this->forum->attachment_permissions;
		$this->topic->attachment_settings = $this->params['attachment_settings']['enable_attachments'];

		if((!$forum->id || $forum->post_permissions < 2) && JFactory::getUser()->guest)
		{
			$this->mixin($this->getService('ninja:view.user.mixin'));
			
			$this->setLoginLayout();
			
			return parent::display();
		}
		elseif(!$forum->id || $forum->post_permissions < 2)
		{
			JError::raiseNotice(401, JText::_('COM_NINJABOARD_YOU_DONT_HAVE_THE_PERMISSIONS_TO_POST_IN_THIS_FORUM'));
			return;
		}
		
		if(!empty($post->id)) {
			$title = $post->subject ? $post->subject : $topic->title;
			$this->_subtitle = $title;
			$this->title = sprintf(JText::_('COM_NINJABOARD_EDITING_POST'), $title);
			if($this->me->id == $post->created_by) {
				$this->notify = (bool)$this->getService('com://admin/ninjaboard.model.watches')
																			->by($this->me->id)
																			->type_name('topic')
																			->type_id($topic->id)
																			->getTotal();
			} else {
				$this->notify = -1;
			}
		}  else if($topic->id) {
			$this->_subtitle = $topic->title;
			$this->title = sprintf(JText::_('COM_NINJABOARD_POST_REPLY_TO_IN'), "'".$topic->title."'", $forum->title);
			$this->notify = $this->me->notify_on_reply_topic;
			
			$controller	= $this->getService('com://site/ninjaboard.controller.post', array('request' => array('view' => 'posts')))
						->sort('created_on')
						->direction('desc')
						->limit(5)
						->offset(0)
						->post(false)
						->topic($topic->id)
						->layout('default');
			//$view	= $this->getService('com://site/ninjaboard.view.posts.html', array('model' => $model));
			$total  = count($controller->getModel()->getList());
			$this->topicreview = $total > 0 ? $controller->display() : false;
			/*
			//@TODO figure out why action.browse is executed before this one, as that's why we have the setView workaround
			$controller = $this->getService('com://site/ninjaboard.controller.post')
				->sort('created_on')
				->direction('desc')
				->limit(5)
				->offset(0)
				->post(false)
				->topic($topic->id)
				->setModel($this->getService('com://site/ninjaboard.model.posts'));
			$this->topicreview = $controller
			
				//@TODO Figure out why the singular view is used instead of the plural one
				//@NOTE A fresh model is passed to the view as com.ninjaboard.controller.post have already 
				//      executed browse at this point. Remember to investigate why that is
				->setView($this->getService('com://site/ninjaboard.view.posts.html', array('model' => $controller->getModel()->set($controller->getRequest()))))
			
				->layout('default')
				->display();
			 //*/
		} else {
			$this->title = $this->_subtitle = sprintf(JText::_('COM_NINJABOARD_CREATE_NEW_TOPIC_IN'), $forum->title);
			$this->notify = $this->me->notify_on_create_topic;
		}

		if($post->id) $this->create_topic_button_title = JText::_('COM_NINJABOARD_SAVE');
		else $this->create_topic_button_title = JText::_('COM_NINJABOARD_SUBMIT');
		$this->create_topic_button = str_replace(array('$title', '$link'), array($this->create_topic_button_title, '#'), $this->params['tmpl']['create_topic_button']);
		
		$this->preview_button = str_replace(array('$title', '$link'), array(JText::_('COM_NINJABOARD_PREVIEW'), '#'), $this->params['tmpl']['create_topic_button']);

		//if($topic->id && !KRequest::get('get.layout', 'cmd', false)) $this->setLayout('default');
		
		$this->post = $this->getService('com://site/ninjaboard.model.posts')->id(KRequest::get('get.post', 'int'))->getItem();
		$this->assign('attachments', $post->id ? $this->getService('com://site/ninjaboard.model.attachments')->post($post->id)->getList() : array());

		return parent::display();
	}

	/**
	 * Method suitable for callbacks that sets the breadcrumbs according to this view context
	 *
	 * @return void
	 */
	public function setBreadcrumbs()
	{
		$app		= JFactory::getApplication();
		$pathway	= $app->getPathWay();		
		$menu	 	= $app->getMenu()->getActive()->query;
		
		//Checks the view properties first, in case they're already set
		if(!isset($this->post))
		{
			$this->post = $this->getModel()->getItem();
			if(!$this->post->ninjaboard_topic_id)
			{
				$this->post->ninjaboard_topic_id = $this->getModel()
																	->getState()
																	->topic;
			}
		}

		if(!isset($this->topic))
		{
			$this->topic = $this->getService('com://admin/ninjaboard.model.topics')
																			->id($this->post->ninjaboard_topic_id)
																			->getItem();
		}
		if(!isset($this->forum))
		{
			// there will allways be a forum id, its either in the topic (if its existing) or its in the request
			$forum_id = $this->topic->forum_id ? $this->topic->forum_id : KRequest::get('get.forum', 'int');
			$this->forum = $this->getService('com://admin/ninjaboard.model.forums')
																			->id($forum_id)
																			->getItem();
		}
		
		if(!$this->forum->isNestable()) return;
		
		foreach($this->forum->getParents() as $parent)
		{
			$pathway->addItem($parent->title, $this->createRoute('view=forum&id='.$parent->id));
		}

		if ($menu['view'] != 'forum') $pathway->addItem($this->forum->title, $this->createRoute('view=forum&id='.$this->forum->id));
		
		$pathway->addItem($this->getDocumentSubtitle(), $this->createRoute('view=topic&id='.$this->topic->id));
	}
}