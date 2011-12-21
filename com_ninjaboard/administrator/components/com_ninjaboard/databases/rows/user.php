<?php defined( 'KOOWA' ) or die( 'Restricted access' );
 /**
 * @version		$Id: user.php 2460 2011-10-11 21:21:19Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseRowUser
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseRowUser extends KDatabaseRowDefault
{
	/**
	 * Cached rowset of this users Ninjaboard usergroups
	 *
	 * @var KDatabaseRowsetInterface
	 */
	protected $_usergroups = false;
	
	/**
	 * Reveals wether this user inherits usergroup assignment from the global or local map
	 *
	 * @var boolean
	 */
	protected $_inherits;

	/**
     * Saves the row to the database.
     *
     * This performs an intelligent insert/update and reloads the properties 
     * with fresh data from the table on success.
     *
     * @return boolean	If successfull return TRUE, otherwise FALSE
     */
    public function save()
    {
    	$result = false;
    	
    	if($this->_new) {
    		$result = $this->getTable()->insert($this);
       	} else {
        	$result = $this->getTable()->update($this);
        }

        return $result;
    }
    
    /**
	* Returns an associative array of the raw data
	*
	* @param   boolean 	If TRUE, only return the modified data. Default FALSE
	* @return  array
	*/
	public function getData($modified = false)
	{
		$result = parent::getData($modified);
	
		//Gets the usergroups
		$result['usergroups'] = $this->usergroups->getData();
		
		return $result;
	}

	/**
     * Retrieve row field value
     *
     * @param  	string 	The column name.
     * @return 	string 	The corresponding column value.
     */
    public function __get($column)
    {
    	$result = parent::__get($column);
    
    	if(!in_array($column, array('usergroups', 'inherits'))) return $result;


		if($column == 'usergroups')
		{
			if(!$this->_usergroups)
			{
				$ids = explode('|', $this->ninjaboard_usergroup_id);
				$this->_usergroups = $this->getService('com://admin/ninjaboard.model.usergroups')
											->id($ids)
											->visible(JFactory::getApplication()->isSite())
											->limit(0)
											->getList();
			}

			return $this->_usergroups;
		}

		if($column == 'inherits')
		{
			if(!isset($this->_inherits))
			{
				//@TODO this should be optimized
				$this->_inherits = $this->getService('com://admin/ninjaboard.model.usergroupmaps')
					->id($this->id)
					->getTotal() < 1;
			}

			return $this->_inherits;
		}
    }
}