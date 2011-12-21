<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: tools.php 2460 2011-10-11 21:21:19Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Tools model
 *
 * @TODO move converters into their own model 
 *
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelTools extends KModelAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		//Get a list over the default converters shipping with Ninjaboard
		$exclude	= array('abstract.php', 'exception.php', 'interface.php', '.DS_Store');
		$converters = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.'/databases/converters/', '.', false, false, $exclude);
		foreach($converters as $name)
		{
			$name		= str_replace('.php', '', $name);
			
			//Prevent exceptions caused by files like .DS_Store
			try
			{
				$converter	= $this->getService('com://admin/ninjaboard.database.converters.'.$name);
			}
			catch(KFactoryException $e)
			{
				continue;
			}
			
			if($converter->canConvert()) $this->_list[$name] = $converter;
		}

		$this->_total = count($this->_list);


		$this->_state
					->insert('import', 'cmd', 'demo')
					->insert('limit', 'int');
	}
	
	/**
	 * Add converter to the list
	 *
	 * @param  string	 Name of the converter
	 * @param  interface ComNinjaboardDatabaseConvertersInterface
	 * @return $this
	 */
	public function addConverter($name, $converter)
	{
		$this->_list[$name] = $converter;
		
		return $this;
	}

	/**
	 * Get a list over converters
	 *
	 * @return array
	 */
	public function getList()
	{
		//Sort list by key
		//@TODO maybe sort by title instead
		ksort($this->_list);
	
		return $this->_list;
	}

	/**
	 * Get a single converter
	 *
	 * @return interface ComNinjaboardDatabaseConvertersInterface
	 */
	public function getItem()
	{
		if(!isset($this->_list[$this->_state->import])) return false;
	
		return $this->_list[$this->_state->import];
	}
}