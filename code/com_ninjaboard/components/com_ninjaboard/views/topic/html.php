<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewTopicHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->topic = $this->getModel()->getItem();
		$this->forum = $this->getService('com://site/ninjaboard.model.forums', array('acl' => true))->id($this->topic->forum_id)->getItem();

		$this->user  = JFactory::getUser();
		
		$me  = $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$this->watch_button = $me->id && $this->forum->params['email_notification_settings']['enable_email_notification'];
		
		//Assign forum permissions to topic
		$this->topic->forum_permissions = $this->forum->forum_permissions;
		$this->topic->topic_permissions = $this->forum->topic_permissions;
		$this->topic->post_permissions = $this->forum->post_permissions;
		$this->topic->attachment_permissions = $this->forum->attachment_permissions;

		if((!$this->forum->id || !$this->topic->id || $this->topic->topic_permissions < 1) && JFactory::getUser()->guest)
		{
			$this->mixin($this->getService('ninja:view.user.mixin'));
			
			$this->setLoginLayout();
			
			return parent::display();
		}
		elseif(!$this->topic->id)
		{
			JError::raiseError(404, JText::_("Topic not found."));
			return;
		}
		elseif(!$this->forum->id)
		{
			JError::raiseError(404, JText::_("Forum not found."));
			return;
		}
		
		$this->_subtitle = $this->topic->title;

		//if($this->topic->id && !KRequest::get('get.layout', 'cmd', false)) $this->setLayout('default');

		$state	= $this->getModel()->getState();
		//$limit	= $state->limit ? $state->limit : 6;
		$offset	= $this->getService('com://site/ninjaboard.model.posts')
						->topic($this->topic->id)
						->post($state->post)
						->limit(0)
						->getOffset();
		$offset = KRequest::get('get.offset', 'int', $offset);
		//This is used to set the canonical link correctly in the topic controller after.read
		//@TODO move all this logic out of the view in 1.2
		//@TODO this is causing the getItem returned in the layout to be different than in this view
		$this->getModel()->set(array('limit' => 0, 'offset' => $offset));		

        $controller = $this->getService('com://site/ninjaboard.controller.post');
        $test = $controller->getModel();
        $test->setAcl(false);
        
        //@TODO temporary workaround
        $this->topic = $this->getModel()->getItem();
        $this->topic->forum_permissions = $this->forum->forum_permissions;
        $this->topic->topic_permissions = $this->forum->topic_permissions;
        $this->topic->post_permissions = $this->forum->post_permissions;
        $this->topic->attachment_permissions = $this->forum->attachment_permissions;

		$this->assign('posts',
			$controller

				//@TODO Figure out why the singular view is used instead of the plural one
				//@TODO this shouldn't be necessary no more
				//->setView($this->getService('com://site/ninjaboard.view.posts.html'))
			
			    //Model needs to run with the acl flag off for performance reasons
			    //@NOTE using $this->getService on this model is not a mistake or a typo
			    //->setModel($this->getService('com://site/ninjaboard.model.posts')->setAcl(false))

				->sort('created_on')
				->limit(0)
				->offset($offset)
				->post(false)
				->topic($this->topic->id)
				->layout('default')
				->display()
		);

		if($this->forum->params['view_settings']['new_topic_button'] == 'topic')
		{
			$this->new_topic_button = '<div class="new-topic">'.str_replace(
				array('$title', '$link'), 
				array(JText::_('New Topic'), $this->createRoute('view=post&forum='.$this->forum->id)), 
				$this->forum->params['tmpl']['new_topic_button']
			).'</div>';
		}
		
		$button = false;
		if(JFactory::getUser()->guest || $this->forum->post_permissions > 1)
		{
    		$button = str_replace(
    			array('$title', '$link'), 
    			array(JText::_('Reply topic'), $this->createRoute('view=post&topic='.$this->topic->id)), 
    			$this->forum->params['tmpl']['new_topic_button']
    		);
		}
		//$this->reply_topic_button = $this->forum->post_permissions > 1 ? $button : null;
		$this->reply_topic_button = $button;
		
		$this->lock_topic_button = null;
		$this->move_topic_button = null;
		$this->delete_topic_button = null;

		if($this->forum->topic_permissions > 2)
		{
			$this->lock_topic_button = $this->_createActionButton('lock', 'Lock topic', $this->topic->id, 'lock');
			$this->move_topic_button = str_replace(
				array('$title', '$link'), 
				array(JText::_('Move topic'), $this->createRoute('view=topic&layout=move&id='.$this->topic->id)), 
				$this->forum->params['tmpl']['new_topic_button']
			);
			$this->delete_topic_button = $this->_createActionButton('delete', 'Delete topic', $this->topic->id, 'trash');
		}

		$output = parent::display();

		//@TODO move this to the controller
		$hit = KRequest::get('session.'.$this->getService('ninja:template.helper.document')->formid($this->topic->id), 'boolean');
		if(!$hit && $this->topic->created_user_id != $me->id)
		{
		    //@TODO fix hit db behavior
			//$this->topic->hit();
			KRequest::set('session.'.$this->getService('ninja:template.helper.document')->formid($this->topic->id), true);
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
		$pathway	= JFactory::getApplication()->getPathWay();
		
		//Checks the view properties first, in case they're already set
		if(!isset($this->topic))
		{
			$this->topic = $this->getModel()
								->getItem();
		}
		if(!isset($this->forum))
		{
			$this->forum = $this->getService('com://admin/ninjaboard.model.forums')
																			->id($this->topic->forum_id)
																			->getItem();
		}

        if(!$this->forum->isNestable()) return;

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
			'class' => $this->getService('ninja:template.helper.document')->formid($action)
		)).'>';
		$html[] = '<input type="hidden" name="action" value="'.$action.'" />';
		$html[] = '<input type="hidden" name="_token" value="'.JUtility::getToken().'" />';
		$html[] = str_replace(
			array('$title', '$link', '$class'), 
			array(JText::_($title), '#', 'action-'.$action), 
			$this->forum->params['tmpl']['new_topic_button']
		);
		$html[] = '</form>';
		
		return implode($html);
	}	
}