<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1563 2011-02-16 14:50:47Z stian $
 * @forum	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewForumsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->assign('params', KFactory::get('admin::com.ninjaboard.model.settings')->getParams());
		$me = KFactory::get('admin::com.ninjaboard.model.people')->getMe();
		
		$this->showtopics = false;
		if(isset($this->params['view_settings']['displayed_elements']) && $me->topic_permissions > 0)
		{
			if(in_array('showtopics', $this->params['view_settings']['displayed_elements']))
			{
				$this->showtopics = true;
				
				//$state		= KFactory::get($this->getModel())->getState();
				/*$this->limit	= KRequest::get('get.limit', 'int', 10);
				$this->offset	= KRequest::get('get.offset', 'int', 0);
				$topicsmodel	= KFactory::tmp('site::com.ninjaboard.model.topics')
									->limit($this->limit)
									->offset($this->offset)
									->sort('last_post_date')
									->direction('desc');
				$this->total	= $topicsmodel->getTotal();
				$this->length	= KFactory::tmp('site::com.ninjaboard.model.topics')->getTotal();
				
				$this->assign('pagination', 
					KFactory::get('site::com.ninjaboard.template.helper.paginator', array('name' => 'topics'))
						->pagination($this->total, $this->offset, $this->limit, 4)
				);*/
				
				
				
				$this->limit	= KRequest::get('get.limit', 'int', 10);
				$this->offset	= KRequest::get('get.offset', 'int', 0);
				$this->total	= KFactory::get('site::com.ninjaboard.model.topics')->getTotal();

				$this->assign('topics', 
					KFactory::get('site::com.ninjaboard.controller.topic')

						//@TODO Figure out why the singular view is used instead of the plural one
						->setView(KFactory::get('site::com.ninjaboard.view.topics.html'))

						->direction('desc')
						->sort('last_post.created_time')
						->limit($this->limit)
						->offset($this->offset)
						->layout('list')
						->display()
				);
				
				$this->assign('pagination', 
					KFactory::get('site::com.ninjaboard.template.helper.paginator', array('name' => 'topics'))
						->pagination($this->total, $this->offset, $this->limit, 4, false)
				);
			}
		}
		
		return parent::display();
	}
	
	public function prepare($forum)
	{
		$linktitle = $this->params['view_settings']['forums_title'] == 'linkable';

		//$model = KFactory::tmp('site::com.ninjaboard.model.forums')->enabled(true)->path($forum->id);

		$this->assign(array(
			'forum' 			=> $forum,
			'forums'			=> $forum->subforums,
			'forums_total'		=> count($forum->subforums),
			//'forums'			=> $forum->getChildren(array('enabled' => true)),
			//'forums'			=> $model->getList(),
			//'forums_total'		=> $model->getTotal()
		));

		if(count($forum->subforums) < 1) return false;

		$chromed = $this->render(
			$this->getTemplate()->loadIdentifier('site::com.ninjaboard.view.forum.block_subforums', $this->_data)->render(true),
			$forum->title,
			$forum->params['module']
		);

		//if($forum->params['module']['position']) return;
		
		return $chromed;
	}

	/**
	 * We don't want this view to mess with the pathway
	 *
	 * @return void
	 */
	/*
	public function setBreadcrumbs()
	{
		//Do nothing
	}
	//*/
}