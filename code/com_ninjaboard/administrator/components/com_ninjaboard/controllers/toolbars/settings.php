<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

 /**
 * Settings Toolbar Clsas
 *
 * @category	Ninjaboard
 * @package		Ninjaboard_Controller
 * @subpackage	Toolbar
 */
class ComNinjaboardControllerToolbarSettings extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
        $this->addDefault(array('attribs' => array('data-data' => '{default:1}')));

        return parent::getCommands();
    }
}