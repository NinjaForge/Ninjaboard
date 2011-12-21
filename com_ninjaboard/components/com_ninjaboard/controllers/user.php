<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: user.php 2004 2011-06-29 17:03:02Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard user Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerUser extends ComNinjaboardControllerAbstract
{	
    /*
     * Empty add action
     *
     * Users can't be added
     *
     * @return 	void
     */
    protected function _actionAdd()
    {
    	return false;
    }

	/*
	 * Empty edit action
	 *
	 * Users can't be edited
	 *
	 * @return 	void
	 */
	protected function _actionEdit()
	{
		return false;
	}

	/*
	 * Empty delete action
	 *
	 * Users can't be deleted
	 *
	 * @return 	void
	 */
	protected function _actionDelete()
	{
		return false;
	}
}