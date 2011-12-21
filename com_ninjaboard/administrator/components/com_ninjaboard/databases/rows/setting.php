<?php defined( 'KOOWA' ) or die( 'Restricted access' );
 /**
 * @version		$Id: setting.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseRowSetting
 *
 * Overloads the default in order to set the theme column if not set already
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseRowSetting extends KDatabaseRowAbstract
{
	/**
	 * Cache of the theme title
	 *
	 * @var KConfig
	 */
	protected $_theme;

	/**
     * Retrieve row field value
     *
     * @param  	string 	The column name.
     * @return 	string 	The corresponding column value.
     */
    public function __get($column)
    {
    	$result = parent::__get($column);
    
    	if($column != 'theme' || is_string($this->params) || !isset($this->params->board_details->theme)) return $result;
    	
    	if(!isset($this->_theme))
		{
			if(!isset($this->_data['theme']) || !$this->_data['theme'])
			{
				$this->_data['theme'] = $this->params->board_details->theme;
			}
			$this->_theme		= KInflector::humanize($this->_data['theme']);
		}
    	
    	return $this->_theme;
    }
}