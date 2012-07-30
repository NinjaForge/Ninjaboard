<?php defined('KOOWA') or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_quickpanel
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Quickpanel Module Class
 *   
 * @author   	Stian Didriksen <stian@timble.net>
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_quickpanel
 */
class ModNinjaboard_quickpanelHtml extends ModDefaultHtml
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->getTemplate()->addFilter(array($this->getService('ninja:template.filter.document')));
	}

	/**
	 * Render the quickpanel
	 */
	public function display()
	{
		// get module parameters
		$this->assign('messages' , $this->module->params->get('messages', '1'));
		$this->assign('watches'  , $this->module->params->get('watches', '1'));
		$this->assign('profile'  , $this->module->params->get('profile', '1'));
		$this->assign('logout'   , $this->module->params->get('logout', '1'));
		
		$this->me			= KService::get('com://admin/ninjaboard.model.people')->getMe();
		$this->profileurl	= JRoute::_('index.php?option=com_ninjaboard&view=person&id='.$this->me->id);
		$this->unread       = (int)KService::get('com://admin/ninjaboard.model.messages')->unread(1)->getTotal();
		
		return parent::display();
	}
}