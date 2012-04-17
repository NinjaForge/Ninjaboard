<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewForumHtml extends ComNinjaboardViewHtml
{
	public function display()
	{

		$model = $this->getModel();
		$forum = $model->getItem();
		$this->_subtitle = $forum->title;

		if(!$forum->id)
		{
			if(JFactory::getUser()->guest)
			{
				$this->mixin($this->getService('ninja:view.user.mixin'));
				
				$this->setLoginLayout();
				
				return parent::display();
			}
			else
			{
				$this->title = JText::_("Access to this forum declined");
				$this->msg	 = JText::_('COM_NINJABOARD_CONTACT_AN_ADMINISTRATOR_IMMIDIATLY_IF_THIS_IS_AN_MISTAKE');
				JError::raiseError(404, JText::_('COM_NINJABOARD_FORUM_NOT_FOUND'));
				return parent::display();
			}
		}
		
		$model = $this->getService('com://site/ninjaboard.model.forums')->limit(0)->sort('path_sort_ordering')->enabled(true)->recurse(1)->path($forum->id);
		$forums = $model->getList();
		
		//@TODO optimize this in the model instead
		foreach($forums as $row)
		{
			$rows = $this->getService('com://site/ninjaboard.model.forums')
							->sort('path_sort_ordering')
							->enabled(true)
							->recurse(1)
							->path($row->id);

			if($rows->getTotal() < 1) continue;
			$row->subforums = $rows->getList();
		}
		
		$this->assign('forums', $forums);
		
		$this->limit	= KRequest::get('get.limit', 'int', 10);
		$this->total	= $this->getService('com://site/ninjaboard.model.topics')
							->direction('desc')
							->sort('last_post_on')
							->forum($forum->id)
							->limit($this->limit)
							->offset(KRequest::get('get.offset', 'int', 0))
							->getTotal();
		
		if($this->total > 0) {
			$this->assign('topics', 
				$this->getService('com://site/ninjaboard.controller.topic')
				
					//@TODO Figure out why the singular view is used instead of the plural one
					//->setView($this->getService('com://site/ninjaboard.view.topics.html'))
				
					->direction('desc')
					->sort('last_post_on')
					->limit($this->limit)
					->offset(KRequest::get('get.offset', 'int', 0))
					->forum($forum->id)
					->layout('list')
					->display()
			);
		} else {
			$this->assign('topics', false);
		}
		
		$this->assign('pagination', 
			$this->getService('com://site/ninjaboard.template.helper.paginator', array('name' => 'topics'))
				->pagination(array(
					'total' => $this->total,
					'offset' => KRequest::get('get.offset', 'int', 0),
					'limit' => $this->limit,
					'display' => 5
				))
		);
		
		if($model->getTotal())
		{
			ob_start();
			echo $this->getTemplate()->loadIdentifier('com://site/ninjaboard.view.forum.block_subforums', $this->_data)->render(true);
			$this->assign('block_subforums', $this->render(ob_get_clean(), false, $forum->params['module']));
		}
		else
		{
			$this->assign('block_subforums', false);
		}

		$me  = $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$this->watch_button = $me->id && $forum->params['email_notification_settings']['enable_email_notification'];
		$this->assign('me', $me);

        $this->new_topic_button = false;
        if(JFactory::getUser()->guest || ($forum->topic_permissions > 1 && $forum->post_permissions > 1))
        {
    		$this->new_topic_button = '<div class="new-topic">'.str_replace(
    			array('$title', '$link', '$class'), 
    			array(JText::_('COM_NINJABOARD_NEW_TOPIC'), $this->createRoute('view=post&forum='.$forum->id), 'action-new'), 
    			$forum->params['tmpl']['new_topic_button']
    		).'</div>';
    	}
		
		$this->assign('toolbar', $this->new_topic_button);
		
		$this->setLayout('default');
		
		return parent::display();
	}

	/**
	 * Method suitable for callbacks that sets the breadcrumbs according to this view context
	 *
	 * @return void
	 */
	public function setBreadcrumbs()
	{
		$pathway = JFactory::getApplication()->getPathWay();
		
		//Checks the view properties first, in case they're already set
		if(!isset($this->forum))
		{
			$this->forum = $this->getModel()->getItem();
		}
		
		if(!$this->forum->isNestable()) return;
		
		foreach($this->forum->getParents() as $parent)
		{
			$pathway->addItem($parent->title, $this->createRoute('view=forum&id='.$parent->id));
		}
		parent::setBreadcrumbs();
	}
}