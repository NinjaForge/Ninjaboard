<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewTopicsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->assign('params', $this->getService('com://admin/ninjaboard.model.settings')->getParams());
	
		$this->limit	= KRequest::get('get.limit', 'int', 10);
		$model	= $this->getModel()
							->limit($this->limit)
							->sort('last_post_on')
							->at(KRequest::get('get.at', 'int', false))
							->direction('desc')
							->offset(KRequest::get('get.offset', 'int', 0))
							->sticky(0);
		
		$state = clone $model->getState();
		
		$this->topics	= $model->getList();
		$this->total	= $model->getTotal();

		$this->assign('pagination', 
			$this->getService('com://site/ninjaboard.template.helper.paginator', array('name' => 'topics'))
				->pagination($this->total, KRequest::get('get.offset', 'int', 0), $this->limit, 4)
		);
		
		$stickymodel = KService::get('com://admin/ninjaboard.model.topics')
							->offset(0)
							->limit(0)
							->direction('desc')
							->sort('last_post_on')
							->forum($state->forum)
							->sticky(1);

		$this->stickies	= $stickymodel->getList();

		return parent::display();
	}

	/**
	 * Method suitable for callbacks that sets the breadcrumbs according to this view context
	 *
	 * @return void
	 */
	public function setBreadcrumbs()
	{
		parent::setBreadcrumbs();

		$model	 = $this->getModel();
		$pathway = JFactory::getApplication()->getPathWay();
		$id		 = $model->getState()->at;
		
		if(!$id) return;

		$person = $this->getService('com://admin/ninjaboard.model.people')->id($id)->getItem();
		//@TODO consider using the username instead of display_name in this part
		$name	= $person->display_name;

		$pathway->addItem('@'.$name, $this->createRoute('view=topics&at='.$id));
	}
}