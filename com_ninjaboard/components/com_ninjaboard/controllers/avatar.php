<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Avatar Controller
 *
 * Used mainly to display people avatars in various sizes
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerAvatar extends ComNinjaboardControllerAttachment
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		KRequest::set('get.format', 'file');

		//When no id is set in the url, then we should assume the user wants to see his own profile
		$me		= KFactory::get('site::com.ninjaboard.model.people')->getMe();
		$config->append(array(
			'request' => array(
				'id'   => $me->id
			)
		));

		parent::__construct($config);

		//@TOD To prevent errors like on profile edit screen, remember to remove this line if we add layouts
		$this->_request->layout = 'default';
		$this->_request->format = 'file';
	}
	
	/**
	 * Read action, creates an row if it don't already exist
	 */
	protected function _actionRead(KCommandContext $context)
	{
		$request	= $this->getRequest();
		$row		= parent::_actionRead($context);
		
		//Only auto create people that exist
		if(!$row->id && $request->id) {
			//Check that the person exists, before creating Ninjaboard record
			$exists = KFactory::get('site::com.ninjaboard.model.users')->id($request->id)->getTotal() > 0;
			if(!$exists) {
				JError::raiseWarning(404, JText::_('Person not found.'));
				return $row;
			}
		
			$row->id = $request->id;
			$row->save();
			
			//In order to get the data from the jos_users table, we need to rerun the query by getting a fresh row and setting the data
			$new = KFactory::tmp($this->getModel())->id($row->id)->getItem();
			$row->setData($new->getData());
		}

		return $row;
	}

	/*
	 * Empty delete action
	 *
	 * Avatars can't be deleted by the controller
	 *
	 * @return 	void
	 */
	protected function _actionDelete()
	{
		return $this->getModel()->getItem();
	}

	/*
	 * Empty edit action
	 *
	 * Avatars can't be edited by the controller
	 *
	 * @return 	void
	 */
	protected function _actionEdit()
	{
		return $this->getModel()->getItem();
	}

	/*
	 * Empty add action
	 *
	 * Avatars can't be added by the controller
	 *
	 * @return 	void
	 */
	protected function _actionAdd()
	{
		return $this->getModel()->getItem();
	}
}