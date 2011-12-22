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
		$this->assign('params', KFactory::get('admin::com.ninjaboard.model.settings')->getParams());
	
		$this->limit	= KRequest::get('get.limit', 'int', 10);
		$model	= $this->getModel()
							->limit($this->limit)
							->sort('last_post_on')
							->at(KRequest::get('get.at', 'int', false))
							->direction('desc')
							->offset(KRequest::get('get.offset', 'int', 0));

		$this->topics	= $model->getList();
		$this->total	= $model->getTotal();

		$this->assign('pagination', 
			KFactory::get('site::com.ninjaboard.template.helper.paginator', array('name' => 'topics'))
				->pagination($this->total, KRequest::get('get.offset', 'int', 0), $this->limit, 4)
		);
		
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
		$pathway = KFactory::get('lib.koowa.application')->getPathWay();
		$id		 = $model->getState()->at;
		
		if(!$id) return;

		$person = KFactory::tmp('admin::com.ninjaboard.model.people')->id($id)->getItem();
		//@TODO consider using the username instead of display_name in this part
		$name	= $person->display_name;

		$pathway->addItem('@'.$name, $this->createRoute('view=topics&at='.$id));
	}
}