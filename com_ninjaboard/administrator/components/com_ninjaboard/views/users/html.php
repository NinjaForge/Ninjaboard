<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewUsersHtml extends ComNinjaboardViewHtml
{
	public function display()
	{        	
		$this->_createToolbar()
			->reset()
			->append(KFactory::get('admin::com.ninja.toolbar.button.edit'));			
				
		return parent::display();
	}
}