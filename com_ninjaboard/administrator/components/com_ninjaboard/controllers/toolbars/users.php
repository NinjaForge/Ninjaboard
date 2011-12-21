<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: forums.php 2330 2011-07-31 00:17:49Z stian $
 * @category	Ninjaboard
 * @package		Ninjaboard_Controller
 * @subpackage	Toolbar
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

 /**
 * Users Toolbar Clsas
 *
 * @category	Ninjaboard
 * @package		Ninjaboard_Controller
 * @subpackage	Toolbar
 */
class ComNinjaboardControllerToolbarUsers extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {

        $this->reset();

        return parent::getCommands();
    }
}