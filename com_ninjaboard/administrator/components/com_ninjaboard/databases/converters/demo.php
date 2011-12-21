<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: demo.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersDemo
 *
 * Imports sample data.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersDemo extends ComNinjaboardDatabaseConvertersAbstract
{
	/**
	 * Import the sample content
	 *
	 * @return $this
	 */
	public function convert()
	{
		$this->data = json_decode(file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/data/demo.json'), true);

		parent::convert();
		
		return $this;
	}

	/**
	 * Sets another label than the default "Demo"
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return 'Sample content';
	}

	/**
	 * Checks if Sample Content is available
	 *
	 * @return boolean
	 */
	public function canConvert()
	{
		return file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/data/demo.json');
	}
}