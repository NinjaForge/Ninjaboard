<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewPostsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->assign('params', $this->getService('com://admin/ninjaboard.model.settings')->getParams());
		$state			= $this->getModel()->getState();
		if(!isset($this->total) && !$this->total)
		{
			$this->total	= $this->getModel()->getTotal();
		}

		$this->topic	= $this->getService('com://site/ninjaboard.model.topics')
																			->id($state->topic)
																			->getItem();


		$this->forum	= $this->getService('com://site/ninjaboard.model.forums')
																			->id($this->topic->forum_id)
																			->getItem();


		$this->user		= JFactory::getUser();


		//Assign forum permissions to topic
		$this->topic->forum_permissions = $this->forum->forum_permissions;
		$this->topic->topic_permissions = $this->forum->topic_permissions;
		$this->topic->post_permissions = $this->forum->post_permissions;
		$this->topic->attachment_permissions = $this->forum->attachment_permissions;

		$this->delete_post_button = '<a class="button delete" href="#">' . JText::_('COM_NINJABOARD_DELETE_POST') . '</a>';
		//Add alias filter for $edit_post_button so it works with 98-template
		$this->getTemplate()->getFilter('alias')->append(array(
			'@edit_post_button' => '$this->getView()->edit_post_button',
			'@quote_post_button' => '$this->getView()->quote_post_button'
		));
		
		$this->assign('pagination', 
			$this->getService('com://site/ninjaboard.template.helper.paginator', array('name' => 'posts'))
				->pagination(array('total' => $this->total, 'offset' => $state->offset, 'limit' => $state->limit))
		);

		return parent::display();
	}
	
	public function edit_post_button($id)
	{
		return '<a class="button edit" href="'.$this->createRoute('view=post&id='.$id).'"<span class="symbol edit">' . JText::_('COM_NINJABOARD_EDIT_POST') . '</span></a>';
	}
	
	public function quote_post_button($id)
	{
		return '<a class="button edit" href="'.$this->createRoute('view=post&topic='.$this->topic->id.'&quote='.$id).'"<span class="symbol edit">' . JText::_('COM_NINJABOARD_QUOTE_POST') . '</span></a>';

	}
}