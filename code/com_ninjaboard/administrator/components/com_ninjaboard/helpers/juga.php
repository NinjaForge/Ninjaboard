<?php
/**
 * @package Ninjaboard
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2010 Dioscouri Design. All rights reserved.
 * @license	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class NinjaboardHelperJuga extends JObject
{
    /**
     * Determines if Juga is installed
     * and registers necessary classes with the autoloader
     * 
     * @return boolean
     */
    function isInstalled()
    {
        $success = false;

        jimport( 'joomla.filesystem.file' );
        $filePath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_juga'.DS.'defines.php';
        if (JFile::exists($filePath))
        {
            $success = true;
        }           
        return $success;
    }

    
}