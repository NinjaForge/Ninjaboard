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

class NinjaboardHelperAmbra extends JObject
{
    /**
     * Determines if Ambra is installed
     * and registers necessary classes with the autoloader
     * 
     * @return boolean
     */
    function isInstalled()
    {
        $success = false;

        jimport( 'joomla.filesystem.file' );
        $filePath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ambra'.DS.'defines.php';
        if (JFile::exists($filePath))
        {
            JLoader::register('Ambra', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ambra'.DS.'defines.php');
            JLoader::register('AmbraConfig', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ambra'.DS.'defines.php');
            JLoader::register('AmbraQuery', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ambra'.DS.'library'.DS.'query.php');
            JLoader::register('AmbraHelperBase', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ambra'.DS.'helpers'.DS.'_base.php');
            $success = true;
        }           
        return $success;
    }
    
    /**
     * Returns the src url to a user's avatar
     * 
     * @param int $user_id
     * @return boolean false if no avatar, src url if true
     */
    function getUserAvatar( $user_id ) 
    {
        $success = false;
        if (!$user_id) 
        {
            return $success;
        }
        
        if (!$this->isInstalled())
        {
            return false;
        }
        
        if ( $pic = Ambra::get( "AmbraHelperUser", 'helpers.user' )->getAvatarFilename( $user_id ) ) 
        {
            $success = Ambra::get( "AmbraHelperUser", 'helpers.user' )->getAvatar( $user_id );
        }
        
        return $success;
    }
    
    /**
     * Gives a user Ambra Points for activity within NinjaBoard
     * such as reading a forum post, responding to a forum post, etc
     * 
     * @param int $user_id
     * @param str $action       the action performed by the user.  corresponds to rules created in Ambra's Points Program
     * @return void 
     */
    function logPoints( $user_id, $action='onAfterResponse' )
    {
        if (!$this->isInstalled())
        {
            return null;
        }
        
        $helper = Ambra::get( "AmbraHelperPoint", 'helpers.point' );
        if ($helper->createLogEntry( $user_id, 'com_ninjaboard', $action ))
        {
            // if points were successfully awarded, enqueue a message 
            JFactory::getApplication()->enqueueMessage( $helper->getError() );
        }
        
        return null;
    }
}