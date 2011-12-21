<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1573 2011-02-17 22:51:43Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewPersonHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$params = KFactory::get('admin::com.ninjaboard.model.settings')->getParams();
		$this->assign('params', $params);
		$person = KFactory::get($this->getModel())->getItem();

		if($this->getLayout() != 'form') {
			$this->assign('posts', $this->render(
				KFactory::get('site::com.ninjaboard.controller.topic')
					
					//@TODO Figure out why the singular view is used instead of the plural one
					->setView(KFactory::get('site::com.ninjaboard.view.topics.html'))
					
					->direction('desc')
					->sort('first_post.created_time')
					->limit(KRequest::get('get.limit', 'int', 10))
					->offset(KRequest::get('get.offset', 'int', 0))
					->at($this->getModel()->getItem()->id)
					->layout('list')
					->display(), 
				JText::_('Latest Posts'), 
				$params['module'])
			);
			
			$me		= KFactory::get($this->getModel())->getMe();
			$this->me = $me;
			if($me->id === $person->id) {
				$this->edit_button = str_replace(
					array('$title', '$link'), 
					array(JText::_('Edit Profile'), $this->createRoute('view=person&id='.$person->id.'&layout=form')),
					$this->params['tmpl']['new_topic_button']
				);
			} else {
				$this->edit_button = false;
			}
			
			$this->watch_button = $me->id && $this->params['email_notification_settings']['enable_email_notification'];
			
			$title = sprintf(JText::_("%s's profile"), $person->display_name);
		} else {
			if(KFactory::get('lib.joomla.user')->guest)
			{
				$this->mixin(KFactory::get('admin::com.ninja.view.user.mixin'));
				
				$this->setLoginLayout();
				
				return parent::display();
			}
			
			$title = sprintf(JText::_("%s's settings"), $person->display_name);
		
			$this->save_button = str_replace('$title', JText::_('Save'), $this->params['tmpl']['create_topic_button']);
		}

		$this->_subtitle = $title;
		$this->assign('title', $title);		
		
		return parent::display();
	}
}