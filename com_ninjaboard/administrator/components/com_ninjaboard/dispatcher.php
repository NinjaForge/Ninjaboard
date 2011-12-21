<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: dispatcher.php 1968 2011-06-17 17:05:17Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardDispatcher extends ComDefaultDispatcher
{
	 /**
	 * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
        	'controller' => 'dashboard'
        ));

        parent::_initialize($config);
    }
}