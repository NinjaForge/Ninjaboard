<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Permissions Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerPermission extends ComNinjaControllerView
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		$this
			->registerActionAlias('add',		'edit')
			->registerActionAlias('apply',		'save');
	}
	
	/**
	 * Gets the redirect URL from the sesison and sets it in the controller
	 *
	 * @return void
	 */
	public function loadRedirect(KCommandContext $context) {}
	
	protected function _actionEdit()
	{
		$identifier = $this->getIdentifier();
		$id			= $identifier->type.'_'.$identifier->package.'.'.$identifier->name.'.';
		$data		= KRequest::get('post', 'string');
		$assets		= KFactory::get('admin::com.ninja.helper.access')->models->assets; 
		$model		= KFactory::get($assets);
		
		foreach($data['access'] as $controller => $access)
		{
			$name = $id.$controller;
			$data = array(
				'name'  => $name,
				'title' => KInflector::humanize($controller).' permissions',
				'rules' => json_encode($access)
			);

			
			$table  = KFactory::get($model->getTable());
			
			$query = KFactory::tmp('lib.koowa.database.query')->where('name', '=', $name);
			$table
				->fetchRow($query)
				->setData($data)
				->save();
		}
			
		$this->_redirect = 'index.php?option=com_ninjaboard&view=permissions';
		
		return $this->getModel()->getList();
	}
}