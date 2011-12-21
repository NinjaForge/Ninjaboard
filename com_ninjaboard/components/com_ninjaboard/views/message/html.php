<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1626 2011-03-02 02:18:55Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewMessageHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$params = KFactory::get('admin::com.ninjaboard.model.settings')->getParams();
		$this->assign('params', $params);
		
		$me		= KFactory::get('admin::com.ninjaboard.model.people')->getMe();
		$this->assign('me', $me);

		if(KFactory::get('lib.joomla.user')->guest)
		{
			$this->mixin(KFactory::get('admin::com.ninja.view.user.mixin'));
			
			$this->setLoginLayout();
			
			return parent::display();
		}

		$this->send_button = str_replace('$title', JText::_('Send'), $this->params['tmpl']['create_topic_button']);

		$title = JText::_("Compose");
		$this->_subtitle = $title;
		$this->assign('title', $title);		
		
		return parent::display();
	}
}