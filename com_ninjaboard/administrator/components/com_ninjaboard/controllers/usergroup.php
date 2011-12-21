<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: usergroup.php 1696 2011-03-25 01:24:34Z stian $
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

		$showReminder = KFactory::get('admin::com.ninja.helper.default')->formid('show-reminder');
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
		$button = 'toolbar-usergroups-modal';
		$id		= KFactory::get('admin::com.ninja.helper.default')->formid('show-maps-button');
		$close	= KFactory::get('admin::com.ninja.helper.default')->formid('close-show-maps-button');
		$cookie	= KFactory::get('admin::com.ninja.helper.default')->formid('show-reminder');
		JError::raiseNotice(0, 
			sprintf(
				JText::_('Remember to map your Joomla! groups to Ninjaboard. Hover %s to see where. %s'),
				'<a href="#" id="' . $id . '" style="text-decoration: underline">' .
					JText::_('here') .
				'</a>',
				'<a href="#" id="' . $close . '">[' .
					JText::_('close') .
				']</a>'
			)
		);
		KFactory::get('admin::com.ninja.helper.default')->js(
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
		
		KFactory::get('admin::com.ninja.helper.default')->css(
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