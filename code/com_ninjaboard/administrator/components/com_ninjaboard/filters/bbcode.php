<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard bbCode filter
 *
 * Mostly for cleaning up stuff from converters
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardFilterBbcode extends KFilterAbstract
{
	/**
	 * Validate a value
	 *
	 * @param	scalar	Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	protected function _validate($value)
	{
		return $this->getService('koowa:filter.string')->validate($value);
	}
	
	/**
	 * Sanitize a value
	 *
	 * @param	mixed	Value to be sanitized
	 * @return	string
	 */
	protected function _sanitize($value)
	{
		$value = preg_replace('/\[([a-z0-9=#]+)\:([a-z0-9:]+)\]/m', '[\1]', $value); 
		$value = preg_replace('/\[\/([a-z0-9]+)\:([a-z0-9:]+)\]/m', '[/\1]', $value); 
		$value = preg_replace('/\[\*\:([a-z0-9:]+)\]/m', '[*]', $value); 
		$value = preg_replace('/\[\/\*\:([a-z0-9:]+)\]/m', '[/*]', $value); 
		//$value = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)', '<a rel="nofollow" href="\\1">\\1</a>', $value);
		return $value;
	}	
}