<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1596 2011-02-20 01:24:22Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewPostsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->assign('params', KFactory::get('admin::com.ninjaboard.model.settings')->getParams());
		$state			= $this->getModel()->getState();
		if(!isset($this->total) && !$this->total)
		{
			$this->total	= $this->getModel()->getTotal();
		}

		$this->topic	= KFactory::get('site::com.ninjaboard.model.topics')
																			->id($state->topic)
																			->getItem();


		$this->forum	= KFactory::get('site::com.ninjaboard.model.forums')
																			->id($this->topic->forum_id)
																			->getItem();


		$this->user		= KFactory::get('lib.joomla.user');


		//Assign forum permissions to topic
		$this->topic->forum_permissions = $this->forum->forum_permissions;
		$this->topic->topic_permissions = $this->forum->topic_permissions;
		$this->topic->post_permissions = $this->forum->post_permissions;
		$this->topic->attachment_permissions = $this->forum->attachment_permissions;

		$this->delete_post_button = '<a class="button delete" href="#">' . JText::_('Delete post') . '</a>';
		//Add alias filter for $edit_post_button so it works with 98-template
		$this->getTemplate()->getFilter('alias')->append(array(
			'@edit_post_button' => 'KFactory::get($this->getView())->edit_post_button'
		));

		$this->assign('pagination', 
			KFactory::get('site::com.ninjaboard.template.helper.paginator', array('name' => 'posts'))
				->pagination($this->total, $state->offset, $state->limit, 4)
		);

		return parent::display();
	}
	
	public function edit_post_button($id)
	{
		return '<a class="button edit" href="'.$this->createRoute('view=post&id='.$id).'"<span class="symbol edit">' . JText::_('Edit post') . '</span></a>';
	}
}