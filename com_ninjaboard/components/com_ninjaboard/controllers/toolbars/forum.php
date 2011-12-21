<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: forum.php 2510 2011-11-22 11:44:48Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Forums Toolbar Class
 */
class ComNinjaboardControllerToolbarForum extends KControllerToolbarDefault
{
    public function getCommands()
    {
        $this->addSeparator()
             ->addEnable()
			 ->addDisable();

        return parent::getCommands();
    }
}