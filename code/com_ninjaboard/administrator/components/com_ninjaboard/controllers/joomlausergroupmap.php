<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard User Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerJoomlausergroupmap extends ComNinjaboardControllerDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		$this->registerActionAlias('add',		'edit')
			 ->registerActionAlias('apply',		'save');
			 
		//Workaround for #269
		$this->_request->layout = 'default';
	}
	
	/**
	 * Gets the redirect URL from the sesison and sets it in the controller
	 *
	 * @return void
	 */
	public function loadRedirect(KCommandContext $context) {}
	
	/**
	 * Gets the redirect URL from the sesison and sets it in the controller
	 *
	 * @return void
	 */
	public function setMessage() {}
	
	public function _actionEdit(KCommandContext $context)
	{
		$data  = $context->data;
		$model = $this->getModel();
		$table = $model->getTable();
		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery();

		$ids = array();
		foreach($data['group'] as $joomla => $ninjaboard)
		{
			$ids[] = $joomla;
		}
		
		$table->getDatabase()->execute('TRUNCATE TABLE `#__ninjaboard_joomla_user_group_maps`');

		foreach($ids as $id)
		{			
			$table->getDatabase()->execute('INSERT INTO `#__ninjaboard_joomla_user_group_maps` (`joomla_gid`, `ninjaboard_gid`) VALUES ('.$id.', '.(int)$data['group'][$id].')');
			
			//$table->insert(array('joomla_gid' => $id, 'ninjaboard_gid' => $data['group'][$id]));
		}
		
		$tmpl = false;
		if(KRequest::get('post.tmpl', 'cmd') == 'component') $tmpl = '&tmpl=component';
		$this->_redirect = 'index.php?option=com_ninjaboard&view=joomlausergroupmaps'.$tmpl;
		if($tmpl) $this->_redirect_message = '<script type="text/javascript">window.parent.document.getElementById("sbox-btn-close").fireEvent("click")</script>';
		// @TODO this is a temporary workaround, find out why the proper way, using setRedirect(), stopped working.
		echo $this->_redirect_message;
		//$this->setRedirect('view=joomlausergroupmaps'.$tmpl, $tmpl ? '<script type="text/javascript">window.parent.document.getElementById("sbox-btn-close").fireEvent("click")</script>' : null);
		
		return $this->getModel()->getList();
	}	
}