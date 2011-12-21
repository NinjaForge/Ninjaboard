<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1750 2011-04-09 21:57:02Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewPostHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->assign('params', KFactory::get('admin::com.ninjaboard.model.settings')->getParams());
		$this->assign('me', KFactory::get('admin::com.ninjaboard.model.people')->getMe());
	    $this->topicreview = false;
	
		$post					= $this->getModel()->getItem();
		if(!$post->ninjaboard_topic_id) $post->ninjaboard_topic_id = $this->getModel()->getState()->topic;
		$this->topic = $topic	= KFactory::get('site::com.ninjaboard.model.topics')
									->id($post->ninjaboard_topic_id)
									->getItem();
		if(!$topic->forum_id) $topic->forum_id = KRequest::get('get.forum', 'int');
		$this->forum = $forum	= KFactory::get('site::com.ninjaboard.model.forums')->id($topic->forum_id)->getItem();

		if((!$forum->id || $forum->post_permissions < 2) && KFactory::tmp('lib.joomla.user')->guest)
		{
			$this->mixin(KFactory::get('admin::com.ninja.view.user.mixin'));
			
			$this->setLoginLayout();
			
			return parent::display();
		}
		elseif(!$forum->id || $forum->post_permissions < 2)
		{
			//JError::raiseNotice(401, JText::_("You don't have the permissions to post in this forum."));
			return;
		}
		
		if(!empty($post->id)) {
			$title = $post->subject ? $post->subject : $topic->title;
			$this->_subtitle = $title;
			$this->title = sprintf(JText::_('Editing post «%s»'), $title);
			if($this->me->id == $post->created_by) {
				$this->notify = (bool)KFactory::tmp('admin::com.ninjaboard.model.watches')
																			->by($this->me->id)
																			->type_name('topic')
																			->type_id($topic->id)
																			->getTotal();
			} else {
				$this->notify = -1;
			}
		}  else if($topic->id) {
			$this->_subtitle = $topic->title;
			$this->title = sprintf(JText::_('Post Reply to %s in %s'), "'".$topic->title."'", $forum->title);
			$this->notify = $this->me->notify_on_reply_topic;
			
			$model	= KFactory::tmp('site::com.ninjaboard.model.posts')
						->sort('created_on')
						->direction('desc')
						->limit(5)
						->offset(0)
						->post(false)
						->topic($topic->id);
			$view	= KFactory::tmp('site::com.ninjaboard.view.posts.html', array('model' => $model));
			$total  = count($model->getList());
			$this->topicreview = $total > 0 ? $view->assign('total', $total) : false;
			/*
			//@TODO figure out why action.browse is executed before this one, as that's why we have the setView workaround
			$controller = KFactory::tmp('site::com.ninjaboard.controller.post')
				->sort('created_on')
				->direction('desc')
				->limit(5)
				->offset(0)
				->post(false)
				->topic($topic->id)
				->setModel(KFactory::tmp('site::com.ninjaboard.model.posts'));
			$this->topicreview = $controller
			
				//@TODO Figure out why the singular view is used instead of the plural one
				//@NOTE A fresh model is passed to the view as com.ninjaboard.controller.post have already 
				//      executed browse at this point. Remember to investigate why that is
				->setView(KFactory::tmp('site::com.ninjaboard.view.posts.html', array('model' => $controller->getModel()->set($controller->getRequest()))))
			
				->layout('default')
				->display();
			 //*/
		} else {
			$this->title = $this->_subtitle = sprintf(JText::_('Create New Topic in %s'), $forum->title);
			$this->notify = $this->me->notify_on_create_topic;
		}

		if($post->id) $this->create_topic_button_title = JText::_('Save');
		else $this->create_topic_button_title = JText::_('Submit');
		$this->create_topic_button = str_replace(array('$title', '$link'), array($this->create_topic_button_title, '#'), $this->params['tmpl']['create_topic_button']);
		
		$this->preview_button = str_replace(array('$title', '$link'), array(JText::_('Preview'), '#'), $this->params['tmpl']['create_topic_button']);

		//if($topic->id && !KRequest::get('get.layout', 'cmd', false)) $this->setLayout('default');
		
		$this->post = KFactory::get('site::com.ninjaboard.model.posts')->id(KRequest::get('get.post', 'int'))->getItem();
		$this->assign('attachments', $post->id ? KFactory::tmp('site::com.ninjaboard.model.attachments')->post($post->id)->getList() : array());

		return parent::display();
	}

	/**
	 * Method suitable for callbacks that sets the breadcrumbs according to this view context
	 *
	 * @return void
	 */
	public function setBreadcrumbs()
	{
		$pathway	= KFactory::get('lib.koowa.application')->getPathWay();		
		
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
			$this->topic = KFactory::get('admin::com.ninjaboard.model.topics')
																			->id($this->post->ninjaboard_topic_id)
																			->getItem();
		}
		if(!isset($this->forum))
		{
			$this->forum = KFactory::get('admin::com.ninjaboard.model.forums')
																			->id($this->topic->forum_id)
																			->getItem();
		}
		
		foreach($this->forum->getParents() as $parent)
		{
			$pathway->addItem($parent->title, $this->createRoute('view=forum&id='.$parent->id));
		}
		$pathway->addItem($this->forum->title, $this->createRoute('view=forum&id='.$this->forum->id));
		
		$pathway->addItem($this->getDocumentSubtitle(), $this->createRoute('view=topic&id='.$this->topic->id));
	}
}