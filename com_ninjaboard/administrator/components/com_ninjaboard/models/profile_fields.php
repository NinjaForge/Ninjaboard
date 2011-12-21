<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: profile_fields.php 1611 2011-02-27 01:07:31Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Profile Fields model
 *
 * Stores the custom columns definitions
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelProfile_fields extends ComDefaultModelDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
				
		// Set the state
		$this->_state
			->insert('user', 'int', false);
	}
}