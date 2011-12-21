<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: mod_ninjaboard_stats.php 1020 2010-10-28 19:15:07Z stian $
 * @package		Ninjaboard
 * @subpackage	Modules
 * @copyright	Copyright (C) 2010 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
KLoader::load('admin::com.ninja.view.module');
class ModNinjaboard_jumpHtml extends ModDefaultHtml
{

	/**
	 * Model identifier, points to the component model
	 *
	 * @var	string
	 */
	protected $_model = 'admin::com.ninjaboard.model.forums';

	public function __construct($options)
	{
		parent::__construct($options);
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
}