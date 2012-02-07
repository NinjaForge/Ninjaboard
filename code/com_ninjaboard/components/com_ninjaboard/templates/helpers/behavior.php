<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Behavior Helper
 *
 * @package Ninjaboard
 */
class ComNinjaboardTemplateHelperBehavior extends ComDefaultTemplateHelperBehavior
{
	/**
	 * Email Updates button
	 *
	 * @author Stian Didriksen
	 */
	public function watch($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'view'		=> KRequest::get('get.view', 'cmd'),
			'id'		=> false,
			'active'	=> 'watching',
			'hover'		=> 'unwatch',
			'class'		=> 'watch',
			'lang'		=> array(
				'subscribe'		=> JText::_('Subscribe'),
				'subscribed'	=> JText::_('Subscribed'),
				'unsubscribe'	=> JText::_('Unsubscribe'),
			),
			
		));
		
		$table = $this->getService('com://admin/ninjaboard.database.table.watches');

		$config->append(array(
			'type'		=> $table->getTypeIdFromName($config->view),
			'type_id'	=> $config->id
		));
		
		$url = '?option=com_ninjaboard&view=watches&format=json';
		$selector = $this->getService('ninja:template.helper.document')->formid('watch');
		
		
		static $loaded;
		if(!$loaded) $loaded = array();

		if(!isset($loaded[$selector]))
		{
			$loaded[$selector] = true;
			$this->getService('ninja:template.helper.document')->load('/jquery/jquery.js');
			$this->getService('ninja:template.helper.document')->load('/watch.js');
			$this->getService('ninja:template.helper.document')->load('js', '
				ninja(function($){
					$(\'.'.$selector.'\').ninjaboardWatch('.json_encode(array(
						'active'	=> $config->active,
						'hover'		=> $config->hover,
						'lang'		=> $config->lang->toArray(),
						'watch'		=> array(
							'url'	=> JRoute::_($url, false),
							'data'	=> array(
								'_token'		=> JUtility::getToken(),
								'subscription_type'		=> $config->type,
								'subscription_type_id'	=> $config->type_id,
								'action' => 'add'
							)
						),
						'unwatch'	=> array(
							'url'	=> $url.'&type='.$config->type.'&type_id='.$config->type_id,
							'data'	=> array(
								'_token' => JUtility::getToken(),
								'action' => 'delete'
							)
						)
					)).');
				});');
		}



		$me			= $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$params		= $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$watching	= (bool)$this->getService('com://admin/ninjaboard.model.watches')
																		->by($me->id)
																		->type($config->type)
																		->type_id($config->type_id)
																		->getTotal();

		//@TODO make this a model method that fetches just the id
		$id			= $this->getService('com://admin/ninjaboard.model.watches')
																		->by($me->id)
																		->type($config->type)
																		->type_id($config->type_id)
																		->limit(0)
																		->getList()
																		->getData();
																		
		if(!$id) $id = false;
		if(is_array($id)) $id = key($id);
		
		$attr   = KHelperArray::toString(array(
			'class'			=> $config->class.' '.$selector.($watching ? ' '.$config->active : ''),
			'data-watching'	=> (int)$watching,
			'data-id'		=> $id
		));
		$html[] = '<div '.$attr.'>';
		$html[] = '<a>'.($watching ? $config->lang->subscribed : $config->lang->subscribe).'</a>';
		$html[] = '</div>';
		
		return implode($html);
	}
	
	/**
	 * Email Updates button
	 *
	 * @author Stian Didriksen
	 */
	public function message($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'message_to_id'           => false,
			'message_to_display_name' => false,
			'header' => JText::_('Send %s a message:')
		));
		
		$this->getService('ninja:template.helper.document')->load('/reveal.js');
		$this->getService('ninja:template.helper.document')->load('/reveal.css');
		$this->getService('ninja:template.helper.document')->load('js', "
		jQuery(function($){
		    var messageform = $('#ninjaboard-message-form'), title = messageform.find('.reply-to'), input = messageform.find('input[name=to]');

		    messageform.appendTo(document.body).addClass('replying');
		    
		    title.text(".json_encode(sprintf($config->header, $config->message_to_display_name)).");
		    input.val(".json_encode($config->message_to_id).");
		});
		");
		
		$html[] = '<a class="ninjaboard-button ninjaboard-button-secondary ninjaboard-button-message" href="#" data-reveal-id="ninjaboard-message-form">'.JText::_('Message').'</a>';
		
		$template = $this->getService('com://site/ninjaboard.view.message.html')->getTemplate();
		$params   = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$form     = $template->loadIdentifier('com://site/ninjaboard.view.message.form', array('params' => $params));
		
		$html[] = '<div id="ninjaboard-message-form" class="reveal-modal">';
		$html[] = $form;
		$html[] = '<a class="close-reveal-modal">&#215;</a>';
		$html[] = '</div>';
		
		return implode($html);
	}

	/**
	 * Renders the editor
	 *
	 * @author Stian Didriksen
	 */
	public function editor($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'name'            => false,
			'value'           => false,
			'placeholder'     => 'Write something…'
		))->append(array(
			'element'         => $config->name
		))->append(array(
			'element_preview' => $config->element.'_preview'
		));

		$html[] = '<textarea name="'.$config->name.'" id="'.$config->element.'" placeholder="'.JText::_($config->placeholder).'">';
		$html[] = htmlspecialchars($config->value);
		$html[] = '</textarea>';

		$html[] = '<div id="'.$config->element_preview.'"></div>';

		return implode($html);
	}

	/**
	 * jQuery version of keepalive
	 *
	 * @author Stian Didriksen
	 */
	public function keepalive()
	{
		$html[] = '<script type="text/javascript">';
		$html[] = "\n";
		$html[] = 'if(window.ninja){';
		$html[] =	 'setInterval(function(){';
		$html[] = 		'ninja.get(' . json_encode(KRequest::url()->get(KHttpUrl::BASE ^ KHttpUrl::PATH)) . ');';
		$html[] = 	'}, ' . (60000 * max(1, (int)JFactory::getApplication()->getCfg('lifetime'))) . ');';
		$html[] = "}";
		$html[] = '</script>';

		return implode($html);
	}
}