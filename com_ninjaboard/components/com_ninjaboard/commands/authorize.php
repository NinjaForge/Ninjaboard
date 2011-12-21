<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: authorize.php 1802 2011-04-14 20:00:34Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Default Authorization Command, overriden to avoid the acl checks to interfere
 *
 * @author      Stian Didriksen <stian@ninjaforge.com>
 * @category    Ninjaboard
 */
class ComNinjaboardCommandAuthorize extends ComDefaultCommandAuthorize
{
 	/**
     * Command handler
     * 
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Can return both true or false.  
     */
    public function execute( $name, KCommandContext $context) 
    { 
        $parts = explode('.', $name); 
        
        //Check the token
        if($parts[0] == 'before' && $context->caller->isDispatched()) 
        {
            if(!$this->checkToken()) {
                throw new KControllerException('Invalid token or session time-out', KHttpResponse::FORBIDDEN);
            }
        }
        
        return true; 
    }
}