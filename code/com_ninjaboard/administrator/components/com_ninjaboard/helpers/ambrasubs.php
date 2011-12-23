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

class NinjaboardHelperAmbrasubs extends JObject
{
    /**
     * Determines if AmbraSubscriptions is installed
     * and registers necessary classes with the autoloader
     * 
     * @return boolean
     */
    function isInstalled()
    {
        $success = false;

        jimport( 'joomla.filesystem.file' );
        $filePath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ambrasubs'.DS.'defines.php';
        if (JFile::exists($filePath))
        {
            $success = true;
        }           
        return $success;
    }

    /**
     * Determines if a user has an active subscription 
     * of the specified type
     * 
     * @param int $user_id
     * @param int $sub_type_id
     * @return boolean
     */
    function hasActiveSub( $user_id, $sub_type_id )
    {
        if (!$this->isInstalled())
        {
            return null;
        }
        
        JLoader::import( 'com_ambrasubs.helpers.subscription', JPATH_ADMINISTRATOR.DS.'components' );
        if (AmbrasubsHelperSubscription::isUser( $user_id, $sub_type_id, '0', '1' ))
        {
            return true;
        }
        return false;
    }
}