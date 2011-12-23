<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersInterface
 *
 * All converters needs to implement this interface.
 * Converters are not required to extend the abstract ComNinjaboardDatabaseConvertersAbstract class however.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
interface ComNinjaboardDatabaseConvertersInterface
{
	/**
	 * Execute the convertion
	 *
	 * @return $this
	 */
	public function convert();

	/**
	 * Checks if the converter can convert
	 *
	 * Usually a check for wether the component is installed or not
	 * Example: JComponentHelper::getComponent( 'com_kunena', true )->enabled
	 *
	 * @return boolean
	 */
	public function canConvert();

	/**
	 * Gets the name of the converter
	 *
	 * Is used as an identifier for the JS and controller
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Gets a more descriptive name for the converter
	 *
	 * Is used for the button label, "Importing from XYZ" messages and like
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * HTML attributes for the buttons
	 *
	 * @return array
	 */
	public function getAttributes();
}