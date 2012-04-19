<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Usergroup Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerUsergroup extends ComNinjaboardControllerDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		$this
				->registerCallback('before.add', array($this, 'setTitle'))
				->registerCallback('before.edit', array($this, 'setTitle'))
				->registerCallback('after.add', array($this, 'setPermissions'))
				->registerCallback('after.edit', array($this, 'setPermissions'));

		$showReminder = $this->getService('ninja:template.helper.document')->formid('show-reminder');
		if(!KRequest::has('cookie.'.$showReminder) && $this->getModel()->getTotal() > 0)
		{
			$this->registerCallback('after.browse', array($this, 'showReminder'));
		}
	}
	
	/**
	 * Remind users to map their usergroups
	 *
	 * @return return type
	 */
	public function showReminder()
	{	
		$button = 'toolbar-modal';
		$id		= $this->getService('ninja:template.helper.document')->formid('show-maps-button');
		$close	= $this->getService('ninja:template.helper.document')->formid('close-show-maps-button');
		$cookie	= $this->getService('ninja:template.helper.document')->formid('show-reminder');
		JError::raiseNotice(0, 
			sprintf(
				JText::_('COM_NINJABOARD_REMEMBER_TO_MAP_YOUR_JOOMLA_GROUPS_TO_NINJABOARD_HOVER_TO_SEE_WHERE'),
				'<a href="#" id="' . $id . '" style="text-decoration: underline">' .
					JText::_('COM_NINJABOARD_HERE') .
				'</a>',
				'<a href="#" id="' . $close . '">[' .
					JText::_('COM_NINJABOARD_CLOSE') .
				']</a>'
			)
		);
		$this->getService('ninja:template.helper.document')->load('js', 
			"window.addEvent('domready', function(){
				var button = $('$button'), toggler = $('$id'), mask = new Mask, reveal = button.getElement('span'), 
					Reminder = new Hash({
						'mouseover': (function(){
							mask.show();
							mask.element.tween('opacity', 0.4);
							button.addClass('hilite');
						}).bind(button),
						'mouseleave': (function(){
							mask.element.tween('opacity', 0);
							button.removeClass('hilite');
						}).bind(button),
						'click': function(event){
							(new Event(event)).stop();
						},
						'close': function(event){
							(new Event(event)).stop();
							var parent = this.getParent('li');
							if(!parent.getSiblings().length) {
								parent = parent.getParent().getParent();
							}
							parent.dissolve({onComplete: function(){
								this.element.remove();
							}});
							Cookie.write('$cookie', true, {duration: 365});
						}
					});
				
				mask.element.setStyle('opacity', 0);
				button.getElement('a').setStyles({position: 'relative', 'z-index': 1000});
				toggler.addEvents(Reminder);
				$('$close').addEvent('click', Reminder.close);
			});"
		);
		
		$this->getService('ninja:template.helper.document')->load('css', 
			".mask {
				position: absolute;
				opacity: 0.4;
				-ms-filter:\"progid:DXImageTransform.Microsoft.Alpha(Opacity=40)\";
				filter: alpha(opacity=40);
				z-index: 999;
				background: #000;
				top: 0;
				bottom: 0;
				left: 0;
				right: 0;
			}
			#$id {
				position: relative;
				z-index: 1000;
				color: inherit;
			}
			#$button a {
				-webkit-transition: background 400ms ease-in-out;
				-moz-transition: background 400ms ease-in-out;
				transition: background 400ms ease-in-out;
			}
			#$button.hilite a {
				background: white;
				-webkit-box-shadow: white 0px 0px 10px;
				-moz-box-shadow: white 0px 0px 10px;
				box-shadow: white 0px 0px 10px;
			}
			#$close {
				display: block;
				color: inherit;
				float: right;
			}"
		);
	}
}