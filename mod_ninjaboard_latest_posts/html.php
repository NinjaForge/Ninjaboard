<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/*
* @version		1.0.2
* @package		mod_ninjaboard_latest_posts
* @author 		NinjaForge
* @author email	support@ninjaforge.com
* @link			http://ninjaforge.com
* @license      http://www.gnu.org/copyleft/gpl.html GNU GPL
* @copyright	Copyright (C) 2010 NinjaForge - All rights reserved.
*/
KLoader::load('admin::com.ninja.view.module.html');
KLoader::load('site::com.ninjaboard.view.posts');


//class helper
class ModNinjaboard_latest_postsHtml extends ComNinjaViewModuleHtml
{		

	 

	public function display()
	{
				
		$this->assign('params', KFactory::get('site::mod.ninjaboard_latest_posts.settings')->getParams());
		
		$this->direction = @$params->get('order_by');
		$this->limit = @$params->get('num_posts');
		
		if (@$params->get('which_posts')==1){ $this->sort ='first_post.created_time'; }
		elseif (@$params->get('which_posts')==2){ $this->sort ='last_post.created_time'; }
		else{ $this->sort = 'created_time'; } 
		
		$model	= KFactory::get($this->getModel())
							->limit($this->limit)
							->sort($this->sort())
							->direction($this->direction());
		
		$this->topics	= $model->getList();
		$this->total	= $model->getTotal();
		
		parent::display();

	}
}
?>



