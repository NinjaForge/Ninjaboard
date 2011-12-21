<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 2470 2011-11-01 14:22:28Z stian $
 * @forum	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewMessagesHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->assign('params', $this->getService('com://admin/ninjaboard.model.settings')->getParams());

        if(JFactory::getUser()->guest)
        {
        	$this->mixin($this->getService('ninja:view.user.mixin'));
        	
        	$this->setLoginLayout();
        	
        	return parent::display();
        }

		return parent::display();
	}
}