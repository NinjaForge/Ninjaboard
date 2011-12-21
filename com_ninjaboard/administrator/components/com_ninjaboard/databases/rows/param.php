<?php defined( 'KOOWA' ) or die( 'Restricted access' );
 /**
 * @version		$Id: param.php 1666 2011-03-22 02:06:32Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseRowParam
 *
 * Table row with a params column. This makes sure global settings are inherited on read, but removed during save
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseRowParam extends KDatabaseRowDefault
{
	/**
	 * Params KConfig object, merged with the global params
	 *
	 * @var KConfig
	 */
	protected $_params;

	/**
     * Retrieve row field value
     *
     * @param  	string 	The column name.
     * @return 	string 	The corresponding column value.
     */
    public function __get($column)
    {
    	$result = parent::__get($column);
    
    	if($column != 'params') return $result;
    	
    	if(!isset($this->_params))
		{
			$identifier	= clone $this->getIdentifier();
			$identifier->path = array('model');
			$identifier->name = 'settings';
			$defaults	= KFactory::get($identifier)->getParams()->toArray();
	
			$params = is_string($result) ? json_decode($result, true) : $result;
	
			if($params !== null) {
				$params = new KConfig($params);
				//@TODO Make this configurable, instead of hardcoding the defaults to only apply when JSite
				$params->append($defaults);
			} else {
				$params = $defaults;
			}

			$this->_params = $params;
		}
    	
    	return $this->_params;
    }
}