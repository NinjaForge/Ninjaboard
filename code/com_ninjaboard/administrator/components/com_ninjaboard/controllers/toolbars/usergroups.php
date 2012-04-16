<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @package		Ninjaboard_Controller
 * @subpackage	Toolbar
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

 /**
 * Usergroups Toolbar Class
 *
 * @category	Ninjaboard
 * @package		Ninjaboard_Controller
 * @subpackage	Toolbar
 */
class ComNinjaboardControllerToolbarUsergroups extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
        $this->addSeparator()
            ->addCommand('modal', array(
            	'label' => 'Map',
            	'href' => JRoute::_('?option=com_ninjaboard&view=joomlausergroupmaps&tmpl=component'),
            	'height'	   => 470,
            	'width'        => 760
            ));

        return parent::getCommands();
    }
}