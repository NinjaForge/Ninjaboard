<?php defined('KOOWA') or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Render the quickpanel
 *   
 * @author   	Stian Didriksen <stian@timble.net>
 * @category	Ninjaboard
 */
class ModNinjaboard_quickpanelHtml extends ModDefaultHtml
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->getTemplate()->addFilters(array(KFactory::get('admin::com.ninja.template.filter.document')));
	}

	public function display()
	{
		// get module parameters
		
		$this->assign('messages' , $this->params->get('messages', '1'));
		$this->assign('watches'  , $this->params->get('watches', '1'));
		$this->assign('profile'  , $this->params->get('profile', '1'));
		$this->assign('logout'   , $this->params->get('logout', '1'));
		
		$this->me			= KFactory::get('site::com.ninjaboard.model.people')->getMe();
		$this->profileurl	= JRoute::_('index.php?option=com_ninjaboard&view=person&id='.$this->me->id);
		$this->unread       = (int)KFactory::tmp('site::com.ninjaboard.model.messages')->unread(1)->getTotal();
		
		parent::display();
	}
}