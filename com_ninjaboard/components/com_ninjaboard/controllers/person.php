<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: person.php 1567 2011-02-16 23:52:24Z stian $
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
		//When no id is set in the url, then we should assume the user wants to see his own profile
		$me		= KFactory::get('site::com.ninjaboard.model.people')->getMe();
		$config->request->append(array(
			'user' => true,
			'id'   => $me->id
		));
		
		parent::__construct($config);

		$this->registerCallback(array('before.edit', 'before.apply', 'before.save'), array($this, 'checkPermissions'));
		$this->registerCallback(array('before.edit', 'before.apply', 'before.save'), array($this, 'checkAlias'));
		$this->registerCallback(array('after.add', 'after.edit'), array($this, 'setAvatar'));
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
		
		$alias = $context->data->alias;

		//Lets find out if this alias is already in use by someone else
		$count = KFactory::tmp($this->getModel()->getIdentifier())->alias($alias)->not($this->getRequest()->id)->getTotal();
		if($count > 0 && $alias != '')
		{
			JError::raiseWarning(0, sprintf(JText::_('The screen name "%s" is already in use by someone else. Please choose another one.'), $context->data->alias));
			
			unset($context->data->alias);
			
			//@TODO solve this redirect so it works
			$this->_redirect = 'index.php?option=com_ninjaboard&view=person&id='.$this->getRequest()->id.'&layout=form';
		}
	}

	public function setAvatar(KCommandContext $context)
	{
		//@TODO we shouldn't clear all cache, only the cache for this user
		if(JFolder::exists(JPATH_ROOT.'/cache/com_ninjaboard/avatars')) JFolder::delete(JPATH_ROOT.'/cache/com_ninjaboard/avatars');
	
		//If nothing is uploaded, don't execute
		if(!KRequest::get('files.avatar.name', 'raw')) return;

		//Prepare MediaHelper
		JLoader::register('MediaHelper', JPATH_ROOT.'/components/com_media/helpers/media.php');

		$person			= KFactory::tmp('admin::com.ninjaboard.model.people')->id($context->result->id)->getItem();
		$error			= null;
		$errors			= array();
		$identifier		= $this->getIdentifier();
		$name			= $identifier->type.'_'.$identifier->package;
		$relative		= '/media/'.$name.'/images/avatars/'.$person->id.'/';
		$absolute		= JPATH_ROOT.$relative;
		$attachments	= array();
		
		
		$avatar = KRequest::get('files.avatar', 'raw');
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
		
		$this->params = KFactory::get('admin::com.ninjaboard.model.settings')->getParams();
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

		$person->avatar = $relative.$upload;
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
		
		if(!isset($request->id)) $request->id = KFactory::get('lib.joomla.user')->id;
		
		if(!$row->id && $request->id) {
			//Check that the person exists, before creating Ninjaboard record
			$exists = KFactory::get('site::com.ninjaboard.model.users')->id($request->id)->getTotal() > 0;
			if(!$exists) {
				JError::raiseError(404, JText::_('Person not found.'));
			}
		
			$row->id = $request->id;
			$row->save();

			//In order to get the data from the jos_users table, we need to rerun the query by getting a fresh row and setting the data
			$new = KFactory::tmp($this->getModel()->getIdentifier())->id($request->id)->getItem();
			$row->setData($new->getData());
		}
		
		//an id is absolutely required
		if(!$row->id && !KFactory::get('lib.joomla.user')->guest) {
			JError::raiseError(404, JText::_('Person not found.'));
			
		}
		
		if(isset($request->layout) && $request->layout == 'form')
		{
			$me		= $this->getModel()->getMe();
			if($me->id && $row->id !== $me->id)
			{
				$message = "You can't edit other users than yourself.";
				KFactory::get('lib.koowa.application')
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
		$person	= KFactory::get($this->getModel())->getItem();
		
		$this->_redirect = 'index.php?option=com_ninjaboard&view=person&id='.$person->id;
	}

	/**
	 * Apply action, workaround for redirects
	 */
	protected function _actionApply($data)
	{
		$result = parent::_actionApply($data);
	
		$this->_redirect = 'index.php?option=com_ninjaboard&view=person&id='.$result->id.'&layout=default';
		
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