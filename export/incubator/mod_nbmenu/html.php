<?php
/* @version		1.0.0
 * @package		mod_ninjaboard_menu
 * @author 		Stephanie Scmidt
 * @author mail	admin@dwtutorials.com
 * @link		http://www.dwtutorials.com
 * @copyright	Copyright (C) 2009 Stephanie Scmidt - All rights reserved.
*/
		
KLoader::load('admin::com.ninja.view.module');
class ModNbmenuHtml extends ComNinjaViewModuleHtml
{
	
	/**
	 * Model identifier, points to the component model
	 *
	 * @var	string
	 */
	protected $_model = 'admin::com.ninjaboard.model.forums';

	public function __construct(array $options = array())
	{
		parent::__construct($options);
		KFactory::get($this->getModel())->recurse(1);
	}
	
	public function display()
	{
		$model		  = KFactory::get($this->getModel());
		$this->forums = $model->getList();
		$this->total  = $model->getTotal();
		$this->state  = $model->getState();
		$this->forum  = $model->getItem();
		$this->i = 0;
		
		parent::display();
	}
	
	protected function prepare()
	{
		return $this->subforum = KFactory::tmp($this->getModel())
									->recurse(1)
									->path($this->forum->id)
									->direction($this->state->direction)
									->order($this->state->order);
	}
}