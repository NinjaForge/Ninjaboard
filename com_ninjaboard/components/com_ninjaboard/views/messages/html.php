<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1814 2011-04-20 23:46:01Z stian $
 * @forum	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewMessagesHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->assign('params', KFactory::get('admin::com.ninjaboard.model.settings')->getParams());

        if(KFactory::tmp('lib.joomla.user')->guest)
        {
        	$this->mixin(KFactory::get('admin::com.ninja.view.user.mixin'));
        	
        	$this->setLoginLayout();
        	
        	return parent::display();
        }

		return parent::display();
	}
}