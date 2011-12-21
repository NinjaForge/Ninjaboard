<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: user.php 1388 2011-01-11 15:49:06Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard User Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerUser extends ComNinjaboardControllerDefault
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
				->registerFunctionBefore('add',	'setUserData')
				->registerFunctionBefore('edit',	'setUserData')
				->registerFunctionAfter('upload',	'setAvatar');

		$this->registerCallback('before.browse', array($this, 'showSearchTips'));
	}
	
	public function showSearchTips()
	{
		$this->_raiseClosableMessage('Use flags like username:bob and email:bob@example.com for advanced searching.');
	}
	
	public function setAvatar()
	{
		if(!$avatar = KRequest::get('files.uploaded', 'string', false)) return;
		
		$data = array('avatar' => $avatar);
		$data['id'] = $this->getRequest()->id;
		
		$rowset = KFactory::tmp('admin::com.ninjaboard.model.people')
				->set($this->getRequest())
				->getItem()
				->setData($data)
				->save();		
	}
	
	public function getUploadDestination()
	{
		$identifier = $this->getIdentifier();
		$option		= $identifier->type.'_'.$identifier->package;
		$user		= KFactory::get($this->getModel())->getItem();
		$path		= '/media/'.$option.'/images/avatars/'.KRequest::get('get.id', 'int', $user->id).'/';
		return $path;
	}
	
	/*
	 * Generic save action
	 *
	 *	@param	mixed 	Either a scalar, an associative array, an object
	 * 					or a KDatabaseRow
	 * @return KDatabaseRow 	A row object containing the saved data
	 */
	protected function _actionSave($data)
	{
		$row = $this->execute('edit', $data);
		
		$this->_redirect = KRequest::get('session.com.dispatcher.referrer', 'url');
		return $row;
	}

	/*
	 * Generic apply action
	 *
	 *	@param	mixed 	Either a scalar, an associative array, an object
	 * 					or a KDatabaseRow
	 * @return 	KDatabaseRow 	A row object containing the saved data
	 */
	protected function _actionApply(KCommandContext $context)
	{
		$result = $this->execute('edit', $context);

		$this->_redirect = 'view='.$this->_identifier->name.'&id='.$this->getRequest()->id;
		return $result;
	}

	/**
	 * Generic edit action, saves over an existing item
	 *
	 * @param	mixed 	Either a scalar, an associative array, an object
	 * 					or a KDatabaseRow
	 * @return KDatabaseRowset 	A rowset object containing the updated rows
	 */
	protected function _actionEdit(KCommandContext $context)
	{
		$context->data->id = $this->getRequest()->id;
	
		$person = KFactory::tmp('admin::com.ninjaboard.model.people')
				->id($context->data->id)
				->getItem()
				->setData(KConfig::toData($context->data));

		$person->save();

		return $person;
	}

	public function setUserData(KCommandContext $context)
	{
		//if(KRequest::type() == 'FLASH') return true;
		$data = $context->data;
		$id   = $this->getRequest()->id;

		if(isset($data->usergroup))
		{
			$data  = array('ninjaboard_user_group_id' => $data['usergroup']);
			$table = KFactory::get('admin::com.ninjaboard.model.usergroupmaps')->getTable();
			
			$table->select(array('joomla_user_id' => $id), KDatabase::FETCH_ROWSET)->delete();

			if($data['ninjaboard_user_group_id'][0] === '0') return $this;

			foreach($data['ninjaboard_user_group_id'] as $group)
			{
				
				$usergroup = KFactory::tmp('admin::com.ninjaboard.model.usergroupmaps')
					->getItem()
					->setData(array(
						'joomla_user_id' => $id,
						'ninjaboard_user_group_id' => $group
					))
					->save();					
			}
		}
	}

	/**
	 * Generic method to set model states
	 *
	 * @return KDatabaseRow 	A row object containing the filtered data
	 */
	protected function _setModelState()
	{
		parent::_setModelState();

		if($group = KRequest::get('get.f.usergroup', 'int')) $this->getModel()->setState('usergroup', $group);
		return $this;
	}
}