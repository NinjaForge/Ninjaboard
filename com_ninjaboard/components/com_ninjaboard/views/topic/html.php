<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewTopicHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$topic 	  = $this->getModel()->getItem();
		$this->forum 	  = KFactory::get('site::com.ninjaboard.model.forums')->id($topic->forum_id)->getItem();
		$this->user = KFactory::get('lib.joomla.user');
		
		$me  = KFactory::get('admin::com.ninjaboard.model.people')->getMe();
		$this->watch_button = (bool)$me->id;
		
		//Assign forum permissions to topic
		$topic->forum_permissions = $this->forum->forum_permissions;
		$topic->topic_permissions = $this->forum->topic_permissions;
		$topic->post_permissions = $this->forum->post_permissions;
		$topic->attachment_permissions = $this->forum->attachment_permissions;
		
		if((!$this->forum->id || !$topic->id) && KFactory::tmp('lib.joomla.user')->guest)
		{
			$this->mixin(KFactory::get('admin::com.ninja.view.user.mixin'));
			
			$this->setLoginLayout();
			
			return parent::display();
		}
		elseif(!$topic->id)
		{
			JError::raiseError(404, JText::_("Topic not found."));
			return;
		}
		elseif(!$this->forum->id)
		{
			JError::raiseError(404, JText::_("Forum not found."));
			return;
		}
		
		$this->_subtitle = $topic->title;

		//if($topic->id && !KRequest::get('get.layout', 'cmd', false)) $this->setLayout('default');

		$state	= $this->getModel()->getState();
		$limit	= $state->limit ? $state->limit : 6;
		$offset	= KFactory::tmp('site::com.ninjaboard.model.posts')
						->topic($topic->id)
						->post($state->post)
						->limit($limit)
						->getOffset();
		$offset = KRequest::get('get.offset', 'int', $offset);
		$this->assign('posts',
			KFactory::get('site::com.ninjaboard.controller.post')
				->sort('created_on')
				->limit($limit)
				->offset($offset)
				->post(false)
				->topic($topic->id)
				->layout('default')
				->browse()
		);

		
		$button = str_replace(
			array('$title', '$link'), 
			array(JText::_('Reply topic'), $this->createRoute('view=post&topic='.$topic->id)), 
			$this->forum->params['tmpl']['new_topic_button']
		);
		//$this->reply_topic_button = $this->forum->post_permissions > 1 ? $button : null;
		$this->reply_topic_button = $button;
		
		$this->lock_topic_button = null;
		$this->move_topic_button = null;
		$this->delete_topic_button = null;
		if($this->forum->topic_permissions > 2)
		{
			$this->lock_topic_button = $this->_createActionButton('lock', 'Lock topic', $topic->id, 'lock');
			$this->move_topic_button = str_replace(
				array('$title', '$link'), 
				array(JText::_('Move topic'), $this->createRoute('view=topic&layout=move&id='.$topic->id)), 
				$this->forum->params['tmpl']['new_topic_button']
			);
			$this->delete_topic_button = $this->_createActionButton('delete', 'Delete topic', $topic->id, 'trash');
		}

		$output = parent::display();

		//@TODO move this to the controller
		$hit = KRequest::get('session.'.KFactory::get('admin::com.ninja.helper.default')->formid($topic->id), 'boolean');
		if(!$hit && $topic->created_user_id != $me->id)
		{
			$topic->hit();
			KRequest::set('session.'.KFactory::get('admin::com.ninja.helper.default')->formid($topic->id), true);
		}
		
		return $output;
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
		if(!isset($this->topic))
		{
			$this->topic = $this->getModel()
								->getItem();
		}
		if(!isset($this->forum))
		{
			$this->forum = KFactory::get('site::com.ninjaboard.model.forums')
																			->id($this->topic->forum_id)
																			->getItem();
		}
		
		foreach($this->forum->getParents() as $parent)
		{
			$pathway->addItem($parent->title, $this->createRoute('view=forum&id='.$parent->id));
		}
		$pathway->addItem($this->forum->title, $this->createRoute('view=forum&id='.$this->forum->id));
		
		$pathway->addItem($this->topic->subject ? $this->topic->subject : JText::_('New Topic'), $this->createRoute('view=topic&id='.$this->topic->id));
	}
	
	private function _createActionButton($action, $title, $id, $symbol = '&#8986;')
	{
		$html[] = '<form '.KHelperArray::toString(array(
			'action' => $this->createRoute('view=topic&id='.$id.'&action='.$action),
			'method' => 'post',
			'class' => KFactory::get('admin::com.ninja.helper.default')->formid($action)
		)).'>';
		$html[] = '<input type="hidden" name="action" value="'.$action.'" />';
		$html[] = '<input type="hidden" name="_token" value="'.JUtility::getToken().'" />';
		$html[] = str_replace(
			array('$title', '$link'), 
			array(JText::_($title), '#'), 
			$this->forum->params['tmpl']['new_topic_button']
		);
		$html[] = '</form>';
		
		return implode($html);
	}
	
	public function _createToolbar()
	{
		return $this;
	}
	
	public function renderTitle()
	{
		return;
	}
}