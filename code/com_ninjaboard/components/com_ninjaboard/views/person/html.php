<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewPersonHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$this->assign('params', $params);
		$person = $this->getService($this->getModel())->getItem();

        if(JFactory::getUser()->guest && (!$person->id || $this->getlayout() == 'form'))
        {
        	$this->mixin($this->getService('ninja:view.user.mixin'));
        	
        	$this->setLoginLayout();
        	
        	return parent::display();
        }

		if($this->getLayout() != 'form') {
		    $controller = $this->getService('com://site/ninjaboard.controller.post');
			$controller->getModel()->reset(); // @TODO - alpha 2 workaround - update/remove for alpha 3
		    	//@TODO Figure out why the singular view is used instead of the plural one
		    	$controller->setView($this->getService('com://site/ninjaboard.view.posts.html'))
				->layout('list');
		    	
		    	

			$controller->getView()->assign('collapse_content', 1);
			$controller->getView()->assign('module_id', "");
			$controller->getView()->assign('display_avatar', 0);
			$controller->getView()->assign('latest_style', 1);
			$this->assign('posts', $this->render(
				'<div id="mod-ninjaboard-latest-posts" class="mod-ninjaboard-latest-posts '.$params['style']['type'].' '.$params['style']['border'].' '.$params['style']['separators'].'">'.
			    $controller
					->direction('desc')
			    	->sort('created_time')
			    	->limit(KRequest::get('get.limit', 'int', 10))
			    	->offset(KRequest::get('get.offset', 'int', 0))
			    	->at($this->getModel()->getItem()->id)
					->display().
					'</div>', 
				JText::_('Latest Posts'), 
				$params['module'])
			);
			
			$model = $controller->getModel();
		    $state = $model->getState();

			$this->assign('pagination', 
				$this->getService('com://site/ninjaboard.template.helper.paginator', array('name' => 'posts'))
					->pagination($model->getTotal(), $state->offset, $state->limit, 4, false)
			);
			
			$me		= $this->getService($this->getModel())->getMe();
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
			
			$this->message_button = $me->id && $this->params['messaging_settings']['enable_messaging'];
			$this->watch_button   = $me->id && $this->params['email_notification_settings']['enable_email_notification'];
			
			$title = sprintf(JText::_("%s's profile"), $person->display_name);
		} else {
			$title = sprintf(JText::_("%s's settings"), $person->display_name);
		
			$this->save_button = str_replace('$title', JText::_('Save'), $this->params['tmpl']['create_topic_button']);
		}

		$this->_subtitle = $title;
		$this->assign('title', $title);		
		
		return parent::display();
	}
}