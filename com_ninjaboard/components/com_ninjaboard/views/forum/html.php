<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1826 2011-04-26 22:10:53Z stian $
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
			if(KFactory::get('lib.joomla.user')->guest)
			{
				$this->mixin(KFactory::get('admin::com.ninja.view.user.mixin'));
				
				$this->setLoginLayout();
				
				return parent::display();
			}
			else
			{
				$this->title = JText::_("Access to this forum declined");
				$this->msg	 = JText::_('Contact an administrator immidiatly if this is an mistake.');
				JError::raiseError(404, JText::_('Forum not found'));
				return parent::display();
			}
		}
		
		$forums = KFactory::tmp('site::com.ninjaboard.model.forums')->limit(0)->sort('path_sort_ordering')->enabled(true)->recurse(1)->path($forum->id);
		
		//@TODO optimize this in the model instead
		foreach($forums->getList() as $row)
		{
			$rows = KFactory::tmp('site::com.ninjaboard.model.forums')
							->sort('path_sort_ordering')
							->enabled(true)
							->recurse(1)
							->path($row->id);

			if($rows->getTotal() < 1) continue;
			$row->subforums = $rows->getList();
		}
		
		$this->assign('forums', $forums->getList());
		
		$this->limit	= KRequest::get('get.limit', 'int', 10);
		$this->total	= KFactory::tmp('site::com.ninjaboard.model.topics')
							->direction('desc')
							->sort('first_post.created_time')
							->forum($forum->id)
							->limit($this->limit)
							->offset(KRequest::get('get.offset', 'int', 0))
							->getTotal();
		
		$this->assign('topics', 
			KFactory::get('site::com.ninjaboard.controller.topic')
			
				//@TODO Figure out why the singular view is used instead of the plural one
				->setView(KFactory::get('site::com.ninjaboard.view.topics.html'))
			
				->direction('desc')
				->sort('last_post.created_time')
				->limit($this->limit)
				->offset(KRequest::get('get.offset', 'int', 0))
				->forum($forum->id)
				->layout('list')
				->display()
		);
		
		$this->assign('pagination', 
			KFactory::get('site::com.ninjaboard.template.helper.paginator', array('name' => 'topics'))
				->pagination($this->total, KRequest::get('get.offset', 'int', 0), $this->limit, 4)
		);
		
		if($forums->getTotal())
		{
			ob_start();
			echo $this->getTemplate()->loadIdentifier('site::com.ninjaboard.view.forum.block_subforums', $this->_data)->render(true);
			$this->assign('block_subforums', $this->render(ob_get_clean(), false, $forum->params['module']));
		}
		else
		{
			$this->assign('block_subforums', false);
		}

		$me  = KFactory::get('admin::com.ninjaboard.model.people')->getMe();
		$this->watch_button = $me->id && $forum->params['email_notification_settings']['enable_email_notification'];

        $this->new_topic_button = false;
        if(KFactory::get('lib.joomla.user')->guest || ($forum->topic_permissions > 1 && $forum->post_permissions > 1))
        {
    		$this->new_topic_button = '<div class="new-topic">'.str_replace(
    			array('$title', '$link'), 
    			array(JText::_('New Topic'), $this->createRoute('view=post&forum='.$forum->id)), 
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
		$pathway = KFactory::get('lib.koowa.application')->getPathWay();
		
		//Checks the view properties first, in case they're already set
		if(!isset($this->forum))
		{
			$this->forum = $this->getModel()->getItem();
		}
		
		foreach($this->forum->getParents() as $parent)
		{
			$pathway->addItem($parent->title, $this->createRoute('view=forum&id='.$parent->id));
		}
		parent::setBreadcrumbs();
	}
}