<?php
/**
 * @version $Id: ninjaboard.user.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter');

/**
 * Ninjaboard User
 *
 * @package Ninjaboard
 */
class NinjaboardUser extends JUser
{
	/**
	 * ninjaboard user table
	 *
	 * @var object
	 */
	var $_ninjaboardUserTable = null;

	/**
	 * ninjaboard profile table
	 *
	 * @var object
	 */
	var $_ninjaboardProfileTable = null;

	/**
	 * ninjaboard avatar
	 *
	 * @var object
	 */
	var $ninjaboardAvatar = null;

	/**
	 * constructor
	 */
	function __construct($identifier = 0, $new = false) {

		// create the ninjaboarduser table object
		$this->_ninjaboardUserTable 	=& JTable::getInstance('NinjaboardUser');
		
		// create the user table object
		$this->_ninjaboardProfileTable 	=& JTable::getInstance('NinjaboardProfile');
		
		parent::__construct($identifier);
		
		// Load the user if it exists
		if (!empty($identifier)) {
			$this->load($identifier);
		} else {
			if ($new) {
				$ninjaboardConfig =& NinjaboardConfig::getInstance();
				
				//initialise
				$this->_ninjaboardUserTable->id = 0;
				$this->_ninjaboardUserTable->role = $ninjaboardConfig->getUserSettingsDefaults('role');
				$this->_ninjaboardUserTable->show_email = $ninjaboardConfig->getUserSettingsDefaults('show_email');
				$this->_ninjaboardUserTable->show_online_state = $ninjaboardConfig->getUserSettingsDefaults('show_online_state');
				$this->_ninjaboardUserTable->enable_bbcode = $ninjaboardConfig->getUserSettingsDefaults('enable_bbcode');
				$this->_ninjaboardUserTable->enable_emoticons = $ninjaboardConfig->getUserSettingsDefaults('enable_emoticons');
				$this->_ninjaboardUserTable->notify_on_reply = $ninjaboardConfig->getUserSettingsDefaults('notify_on_reply');
				$this->_ninjaboardUserTable->time_zone = $ninjaboardConfig->getUserSettingsDefaults('time_zone');
				$this->_ninjaboardUserTable->time_format = $ninjaboardConfig->getUserSettingsDefaults('time_format');
			}
		}		
		
	}

	/**
	 * get instance
	 *
	 * @access 	public
	 * @param int
	 * @return object
	 */
	function &getInstance($id = 0) {
	
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		if(!is_numeric($id)) {
			jimport('joomla.user.helper');
			if (!$id = JUserHelper::getUserId($id)) {
				JError::raiseWarning('SOME_ERROR_CODE', 'NinjaboardUser::_load: User '.$id.' does not exist');
				return false;
			}
		}

		if (empty($instances[$id])) {
			$ninjaboardUser = new NinjaboardUser($id);
			$instances[$id] = $ninjaboardUser;
		}

		return $instances[$id];
	}

	/**
	 * set property
	 *
	 * @access	public
	 */
	function set($property, $value=null) {
	
		if(isset($this->_ninjaboardUserTable->$property)) {
			$this->_ninjaboardUserTable->$property = $value;
		}
		if(isset($this->_ninjaboardProfileTable->$property)) {
			$this->_ninjaboardProfileTable->$property = $value;
		}
		parent::set($property, $value);			
	}

	/**
	 * get property
	 *
	 * @access	public
	 */
	function get($property, $default=null) {

		if(isset($this->_ninjaboardUserTable->$property)) {
			return $this->_ninjaboardUserTable->$property;
		}
		if(isset($this->_ninjaboardProfileTable->$property)) {
			return $this->_ninjaboardProfileTable->$property;
		}	
		
		$value = parent::get($property, $default);
		if(isset($value)) {
			return $value;
		}			
		return $default;
	}
	
	/**
	 * bind user
	 *
	 * @access 	public
	 */
	function bind(&$array) {
	
		if (!parent::bind($array)) {
			return false;
		} else if (!$this->_ninjaboardUserTable->bind($array)) {
			$this->setError(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardUserTable->getError()));
			return false;
		} else if (!$this->_ninjaboardProfileTable->bind($array)) {
			$this->setError(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardProfileTable->getError()));
			return false;
		}

		return true;
	}

	/**
	 * save ninjaboard user
	 *
	 * @access 	public
	 */
	function save($updateOnly = false) {
		$db   =& JFactory::getDBO();
		$isNew = $this->id ? false : true;
		
		if ($isNew) {
			$usersConfig = &JComponentHelper::getParams('com_users');
			$newUsertype = $usersConfig->get('new_usertype');
			$this->set('usertype', $newUsertype);
			
			$authorize =& JFactory::getACL();
			$this->set('gid', $authorize->get_group_id('', $newUsertype, 'ARO'));
			// $ninjaboardUser->set('gid', 28); // In JooBB we only allow create a simple registred user!!!
			
			$this->set('registerDate', gmdate("Y-m-d H:i:s"));			
		}
		
		if (!parent::save($updateOnly)) {
			return false;
		}
		
		if ($isNew) {
			$row =& JTable::getInstance('NinjaboardUser');
			if (!$row->load($this->id)) {
			
				// try to create a dummy
				$db->setQuery("INSERT INTO #__ninjaboard_users (id) VALUES ($this->id)");
				if ($db->query()) {
					$this->_ninjaboardUserTable->id = $this->id;
				} else {
					JError::raiseWarning(JText::sprintf('NB_MSGACTIONFAILED', $db->getError()));
					return false;					
				}
			}
			$row =& JTable::getInstance('NinjaboardProfile');
			if (!$row->load($this->id)) {
			
				// try to create a dummy
				$db->setQuery("INSERT INTO #__ninjaboard_profiles (id) VALUES ($this->id)");
				if ($db->query()) {
					$this->_ninjaboardProfileTable->id = $this->id;
				} else {
					JError::raiseWarning(JText::sprintf('NB_MSGACTIONFAILED', $db->getError()));
					return false;				
				}
			}
		}
		
		// handle avatar
		if (!$this->ninjaboardAvatar) {	
			$this->ninjaboardAvatar = new NinjaboardAvatar($this->get('avatar_file'));
		}		

		if (JRequest::getVar('deleteavatar', '0') && $this->get('avatar_file') != '') {
			if ($this->ninjaboardAvatar->deleteAvatar($this->get('avatar_file'))) {
				$this->set('avatar_file', '');		
			}
		}
		$this->ninjaboardAvatar->uploadAvatarFile(JRequest::getVar('avatarfile', '', 'files', 'array'), $this);

		if ($this->getError()) {
			return false;			
		}
		
		if (!$this->_ninjaboardUserTable->check()) {
			$this->setError(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardUserTable->getError()));
			return false;
		} else if (!$result = $this->_ninjaboardUserTable->store()) {
			$this->setError(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardUserTable->getError()));
			return false;
		} else if (!$this->_ninjaboardProfileTable->check()) {
			$this->setError(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardProfileTable->getError()));
			return false;
		} else if (!$result = $this->_ninjaboardProfileTable->store()) {
			$this->setError(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardProfileTable->getError()));
			return false;
		}
		
		return true;
	}

	/**
	 * delete ninjaboard user
	 *
	 * @access 	public
	 */
	function delete() {
	
		if (!parent::delete()) {
			return false;
		} else if (!$result = $this->_ninjaboardUserTable->delete($this->_id)) {
			$this->setError(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardUserTable->getError()));
			return false;
		} else if (!$result = $this->_ninjaboardProfileTable->delete($this->_id)) {
			$this->setError(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardProfileTable->getError()));
			return false;
		}
		
		return true; 
	}

	/**
	 * load ninjaboard user
	 *
	 * @access 	public
	 */
	function load($id) {
		$db   =& JFactory::getDBO();
		
		// first of all load the original joomla user data
		if (!parent::load($id)) {
			return false;
		}	

		if (!$this->_ninjaboardUserTable->load($id)) {
		
			// try to create a dummy
			$db->setQuery("INSERT INTO #__ninjaboard_users (id) VALUES ($id)");
			if (!$db->query()) {
				JError::raiseWarning(JText::sprintf('NB_MSGACTIONFAILED', $db->getError()));
				return false;
			} else if (!$this->_ninjaboardUserTable->load($id)) {
				JError::raiseWarning(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardUserTable->getError()));
				return false;			
			}

		}
		
		if (!$this->_ninjaboardProfileTable->load($id)) {
		
			// try to create a dummy
			$db->setQuery("INSERT INTO #__ninjaboard_profiles (id) VALUES ($id)");
			if (!$db->query()) {
				JError::raiseWarning(JText::sprintf('NB_MSGACTIONFAILED', $db->getError()));
				return false;
			} else if (!$this->_ninjaboardProfileTable->load($id)) {
				JError::raiseWarning(JText::sprintf('NB_MSGACTIONFAILED', $this->_ninjaboardProfileTable->getError()));
				return false;			
			}

		}
		
		if (!$this->ninjaboardAvatar) {	
			$this->ninjaboardAvatar = new NinjaboardAvatar($this->get('avatar_file'));
		}		

		return true;	
	}
	
	/**
	 * saves the profile
	 *
	 * @access 	public
	 */
	function saveProfile($data) {
		global $Itemid;

		// initialize variables
		$db	= & JFactory::getDBO();

		if (!$this->bind($data)) {
			JError::raiseWarning(JText::sprintf('NB_MSGACTIONFAILED', $this->getError()));
			return false;
		} else if (!$this->save()) {
			JError::raiseWarning(JText::sprintf('NB_MSGACTIONFAILED', $this->getError()));
			return false;
		}
		return true;	
	}
		
	/**
	 * get extended role
	 *
	 * @access 	public
	 */	
	function getExtendedRole($id_forum) {
		$db =& JFactory::getDBO();

		$query = "SELECT max(a.role)"
				. "\n FROM #__ninjaboard_forums_auth AS a"
				. "\n WHERE a.id_forum = ". $id_forum
				. "\n AND a.id_user = ". $this->get('id')
				. "\n AND a.id_group = 0"
				;		
		$db->setQuery($query);
		$result = $db->loadResult();
		
		if ($result == null) {
			$role = 0;
		} else {
			$role = $result;
		}

		return $role;
	}
	
	/**
	 * get group role
	 *
	 * @access 	public
	 */	
	function getGroupRole($id_forum) {
		$db   =& JFactory::getDBO();
				
		// get the group main role
		$query = "SELECT max(g.role) AS role"
				. "\n FROM #__ninjaboard_groups_users AS gu"
				. "\n INNER JOIN #__ninjaboard_groups AS g ON g.id = gu.id_group"
				. "\n WHERE gu.id_user = ". $this->get('id')
				;		
		$db->setQuery($query);
		$mainRole = $db->loadResult();
		
		if ($mainRole == null) {
			$mainRole = 0;
		}
	
		// get the group extended role
		$query = "SELECT max(a.role) AS role"
				. "\n FROM #__ninjaboard_groups_users AS gu"
				. "\n INNER JOIN #__ninjaboard_forums_auth AS a ON a.id_group = gu.id_group"
				. "\n WHERE gu.id_user = ". $this->get('id')
				. "\n AND a.id_forum = ". $id_forum
				;		
		$db->setQuery($query);
		$extendedRole = $db->loadResult();
		
		if ($extendedRole == null) {
			$extendedRole = 0;
		}

		// we want to return the dominating role
		if ($mainRole > $extendedRole) {
			$role = $mainRole;
		} else {
			$role = $extendedRole;
		}
	
		return $role;
	}
	
	/**
	 * set activation
	 *
	 * @access 	public
	 */	
	function setActivation() {
		jimport('joomla.user.helper');
		$this->set('activation', md5(JUserHelper::genRandomPassword()));
		$this->set('block', '1');
		$this->save();		
	}
	
	/**
	 * save guest user
	 *
	 * @access 	public
	 */	
	function saveGuestUser($idPost, $guestName) {
		$db				=& JFactory::getDBO();
		$messageQueue	=& NinjaboardMessageQueue::getInstance();

		if ($guestName != '') {
			$query = "SELECT pg.id_post"
					. "\n FROM  #__ninjaboard_posts_guests AS pg"
					. "\n WHERE pg.id_post = ". $idPost
					;
			$db->setQuery($query);

			if (!$db->loadResult()) {
				$query = "INSERT INTO #__ninjaboard_posts_guests"
						. "\n SET id_post = ". $idPost .", guest_name = ". $db->Quote($guestName)
						;
			} else {
				$query = "UPDATE #__ninjaboard_posts_guests"
						. "\n SET guest_name = ". $db->Quote($guestName)
						. "\n WHERE id_post = " . $idPost
						;
			}
			
			$db->setQuery($query);

			if (!$db->query()) {
				$messageQueue->addMessage($db->getErrorMsg());
			}
		}	
	}
	
}
