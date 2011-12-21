<?php
/**
 * @version $Id: dashboard.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class NinjaBoardControllerDashboard extends JController
{
			
	function display()
	{
		$view = JRequest::getVar('view');
		
		if (!$view) {
			JRequest::setVar('view', 'dashboard');
		}
		
		parent::display();
	}
	
}

?>
