<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 2461 2011-10-11 22:32:21Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewIconsetsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{	
		// Display the toolbar
		$this->_createToolbar()
			->reset();
		
		return parent::display();
	}
}