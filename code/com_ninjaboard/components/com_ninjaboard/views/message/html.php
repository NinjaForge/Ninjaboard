<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewMessageHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$this->assign('params', $params);
		
		$me		= $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$this->assign('me', $me);

		if(JFactory::getUser()->guest)
		{
			$this->mixin($this->getService('ninja:view.user.mixin'));
			
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