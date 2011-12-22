<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewIconsetsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{	
		$this->assign('date', KFactory::get('lib.joomla.utilities.date'));
	
		// Display the toolbar
		$this->_createToolbar()
			->reset();
		
		return parent::display();
	}
}