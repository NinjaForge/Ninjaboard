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
		
		$this->registerCallback(array('before.add', 'before.edit'), array($this, 'setUserData'));
		$this->registerCallback(array('after.add', 'after.edit'), array($this, 'setAvatar'));

		$this->registerCallback('before.browse', array($this, 'showSearchTips'));
	}
	
	public function showSearchTips()
	{
		$this->_raiseClosableMessage('COM_NINJABOARD_USE_FLAGS_FOR_ADVANCED_SEARCHING');
	}
	
	public function setAvatar(KCommandContext $context)
	{
		//@TODO we shouldn't clear all cache, only the cache for this user
		if(JFolder::exists(JPATH_ROOT.'/cache/com_ninjaboard/avatars')) JFolder::delete(JPATH_ROOT.'/cache/com_ninjaboard/avatars');
	
		//If nothing is uploaded, don't execute
		if(!KRequest::get('files.avatar.name', 'raw')) return;

		//Prepare MediaHelper
		JLoader::register('MediaHelper', JPATH_ROOT.'/components/com_media/helpers/media.php');

		$person			= $this->getService('com://admin/ninjaboard.model.people')->id($context->result->id)->getItem();
		$error			= null;
		$errors			= array();
		$identifier		= $this->getIdentifier();
		$name			= $identifier->type.'_'.$identifier->package;
		$relative		= '/media/'.$name.'/images/avatars/'.$person->id.'/';
		$absolute		= JPATH_ROOT.$relative;
		$attachments	= array();
		
		
		$avatar = KRequest::get('files.avatar', 'raw');

		//if we are a bmp we cant upload it
		if (strtolower(JFile::getExt($avatar['name'])) == 'bmp') {
			JError::raiseWarning(21, sprintf(JText::_('COM_NINJABOARD_FAILED_TO_UPLOAD_BECAUSE_THIS_FILE_TYPE_IS_NOT_SUPPORTED'), $avatar['name']));
			return $this;
		}
		
		if(!MediaHelper::canUpload($avatar, $error)) {
			$message = JText::_('COM_NINJABOARD_FAILED_TO_UPLOAD_BECAUSE');
			JError::raiseWarning(21, sprintf($message, $avatar['name'], lcfirst($error)));
			
			return $this;
		}
		if(!MediaHelper::isImage($avatar['name'])) {
			$message = JText::_('COM_NINJABOARD_FAILED_TO_UPLOAD_BECAUSE_ITS_NOT_AN_IMAGE');
			JError::raiseWarning(21, sprintf($message, $avatar['name']));
			
			return $this;
		}
		
		$this->params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$params = $this->params['avatar_settings'];
		$maxSize = (int) $params['upload_size_limit'];
		if ($maxSize > 0 && (int) $avatar['size'] > $maxSize)
		{
			$message = JText::_('COM_NINJABOARD_FAILED_UPLOADING_BECAUSE_ITS_TOO_LARGE');
			JError::raiseWarning(21, sprintf($message, $avatar['name']));
			
			return $this;
		}
			

		$upload = JFile::makeSafe(uniqid(time())).'.'.JFile::getExt($avatar['name']);
		JFile::upload($avatar['tmp_name'], $absolute.$upload);

		$person->avatar		= $relative.$upload;
		$person->avatar_on	= gmdate('Y-m-d H:i:s');
		$person->save();
		
		return $this;
	}
	
	public function getUploadDestination()
	{
		$identifier = $this->getIdentifier();
		$option		= $identifier->type.'_'.$identifier->package;
		$user		= $this->getModel()->getItem();
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
		
		$this->_redirect = 'index.php?option=com_ninjaboard&view=users';
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

		$this->_redirect = 'index.php?option=com_ninjaboard&view='.$this->getIdentifier()->name.'&id='.$this->getRequest()->id;
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
	
		$person = $this->getService('com://admin/ninjaboard.model.people')
				->id($context->data->id)
				->getItem()
				->setData(KConfig::unbox($context->data));

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
			$table = $this->getService('com://admin/ninjaboard.model.usergroupmaps')->getTable();
			
			$table->select(array('joomla_user_id' => $id), KDatabase::FETCH_ROWSET)->delete();

			if($data['ninjaboard_user_group_id'][0] === '0') return $this;

			foreach($data['ninjaboard_user_group_id'] as $group)
			{
				
				$usergroup = $this->getService('com://admin/ninjaboard.model.usergroupmaps')
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