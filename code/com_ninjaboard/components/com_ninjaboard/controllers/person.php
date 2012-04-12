<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Person Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerPerson extends ComNinjaboardControllerAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->registerCallback('before.read', array($this, 'setMe'));
		$this->registerCallback(array('before.edit', 'before.apply', 'before.save'), array($this, 'checkPermissions'));
		$this->registerCallback(array('before.edit', 'before.apply', 'before.save'), array($this, 'checkAlias'));
		$this->registerCallback(array('after.add', 'after.edit'), array($this, 'setAvatar'));
	}
	
	public function setMe()
	{
		//When no id is set in the url, then we should assume the user wants to see his own profile
		$me		= $this->getService('com://site/ninjaboard.model.people')->getMe();
		$this->_request->append(array(
			'user' => true,
			'id'   => $me->id
		));

		$this->getModel()->set($this->getRequest());
	}

	public function checkPermissions()
	{
		$me		= $this->getModel()->getMe();
		$person	= $this->getModel()->getItem();
		
		/*
		if($me->id !== $person->id) {
			$this->execute('cancel');

			return false;
		}
		//*/
		
		return $me->id === $person->id;
	}
	
	public function checkAlias(KCommandContext $context)
	{
		if(!isset($context->data->alias, $this->getRequest()->id)) return;
		
		$alias = trim($context->data->alias);
		
		//Don't do anything if alias isn't a value
		if(!$alias) return $this;
		
		$model = $this->getService($this->getModel()->getIdentifier())->not($this->getRequest()->id);

		//Lets find out if this alias is already in use by someone else
		$count = $model->alias($alias)->getTotal();
		if($count > 0) return $this->_checkAliasFailed($context);
		
		$table = $this->getService('com://admin/ninjaboard.database.table.users');
		//By making the alias all caps, MySQL will do an case insensitive search since there's no mixed casing.
		$alias = $table->getDatabase()->quoteValue(strtoupper($alias));
		$query = $table->getDatabase()->getQuery()
												->where('id', 'not in', $this->getRequest()->id)
												->where("(name LIKE $alias OR username LIKE $alias)");
		if($table->count($query) > 0) return $this->_checkAliasFailed($context);
	}
	
	private function _checkAliasFailed(KCommandContext $context)
	{
		JError::raiseWarning(0, sprintf(JText::_('"%s" is already in use. Please choose another one.'), $context->data->alias));
		
		unset($context->data->alias);
		
		//@TODO solve this redirect so it works
		$this->_redirect = 'index.php?option=com_ninjaboard&view=person&id='.$this->getRequest()->id.'&layout=form';
		
		return $this;
	}

	public function setAvatar(KCommandContext $context)
	{
		//@TODO we shouldn't clear all cache, only the cache for this user
		if(JFolder::exists(JPATH_ROOT.'/cache/com_ninjaboard/avatars')) JFolder::delete(JPATH_ROOT.'/cache/com_ninjaboard/avatars');
	
		//If nothing is uploaded, don't execute
		if(!KRequest::get('files.avatar.name', 'raw')) return;

		//Prepare MediaHelper
		JLoader::register('MediaHelper', JPATH_ROOT.'/components/com_media/helpers/media.php');

		$person			= $this->getModel()->getItem();
		
		if(!$person->id) return;
		
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
			JError::raiseWarning(21, sprintf(JText::_('%s failed to upload because this file type is not supported'), $avatar['name']));
			return $this;
		}
		
		if(!MediaHelper::canUpload($avatar, $error)) {
			$message = JText::_("%s failed to upload because %s");
			JError::raiseWarning(21, sprintf($message, $avatar['name'], lcfirst($error)));
			
			return $this;
		}
		if(!MediaHelper::isImage($avatar['name'])) {
			$message = JText::_("%s failed to upload because it's not an image.");
			JError::raiseWarning(21, sprintf($message, $avatar['name']));
			
			return $this;
		}
		
		$this->params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$params = $this->params['avatar_settings'];
		$maxSize = (int) $params['upload_size_limit'];
		if ($maxSize > 0 && (int) $avatar['size'] > $maxSize)
		{
			$message = JText::_("%s failed uploading because it's too large.");
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

	/**
	 * Read action, creates an row if it don't already exist
	 */
	protected function _actionRead(KCommandContext $context)
	{
		$request	= $this->getRequest();
		$row		= parent::_actionRead($context);
		
		if(!isset($request->id)) $request->id = JFactory::getUser()->id;
		
		if(!$row->id && $request->id) {
			//Check that the person exists, before creating Ninjaboard record
			$exists = $this->getService('com://site/ninjaboard.model.users')->id($request->id)->getTotal() > 0;
			if(!$exists) {
				JError::raiseError(404, JText::_('Person not found.'));
			}
		
			$row->id = $request->id;
			$row->save();

			//In order to get the data from the jos_users table, we need to rerun the query by getting a fresh row and setting the data
			$new = $this->getService($this->getModel()->getIdentifier())->id($request->id)->getItem();
			$row->setData($new->getData());
		}
		
		//an id is absolutely required
		if(!$row->id && !JFactory::getUser()->guest) {
			JError::raiseError(404, JText::_('Person not found.'));
			
		}
		
		if(isset($request->layout) && $request->layout == 'form')
		{
			$me		= $this->getModel()->getMe();
			if($me->id && $row->id !== $me->id)
			{
				$message = "You can't edit other users than yourself.";
				JFactory::getApplication()
					->redirect(JRoute::_('&view=person&id='.$row->id.'&layout='), JText::_($message), 'error');
				return $row;
			}
		}

		return $row;
	}

	/*
	 * Generic cancel action
	 *
	 * @return 	void
	 */
	protected function _actionCancel(KCommandContext $context)
	{
		$person	= $this->getModel()->getItem();
		
		$this->_redirect = 'index.php?option=com_ninjaboard&view=person&id='.$person->id;
		
		return $person;
	}

	/**
	 * Apply action, workaround for redirects
	 */
	protected function _actionApply($context)
	{
		$result = parent::_actionApply($context);
	
		$row = parent::_actionRead($context);
		$this->_redirect = 'index.php?option=com_ninjaboard&view=person&id='.$row->id.'&layout=default';
		
		return $result;
	}

	/*
	 * Empty delete action
	 *
	 * Users can't be deleted
	 *
	 * @return 	void
	 */
	protected function _actionDelete()
	{
		return false;
	}
}