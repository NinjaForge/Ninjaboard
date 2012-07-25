<?php
/**
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_jump
 * @copyright	Copyright (C) 2010 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 defined( '_JEXEC' ) or die( 'Restricted access' );

 /**
 * Jump Module Class
 *   
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_jump
 */
class ModNinjaboard_jumpHtml extends ModDefaultHtml
{
	/**
 	* Render the jump module
 	*/
	public function display()
	{
		$this->assign('forums' , KService::get('com://admin/ninjaboard.model.forums')->getList());
		
		return parent::display();
	}
}