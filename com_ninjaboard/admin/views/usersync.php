<?php
/**
 * @version $Id: usersync.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard User Synchronization View
 *
 * @package Ninjaboard
 */
class ViewUserSync {

	/**
	 * show user synchronization
	 */	
	function showUserSync(&$lists) {
		global $mainframe, $my;

		// initialize variables
		$document =& JFactory::getDocument();
		$document->addStyleSheet(NB_ADMINCSS_LIVE.DL.'icon.css');
		
		$task	= JRequest::getVar('task');
		
	//include our helper file to create the footer and buttons
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'HTML_ninjahelper_admin.php');
			
	//Include our stylesheet for our Ninja Component Styling
		$document =& JFactory::getDocument();
		$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin.css';
		$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	//add ie6 css if needed
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (strstr($ua, "MSIE")&& strstr($ua, "6")) {
			  
			$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin_ie6.css';
			$document->addStyleSheet($cssFile, 'text/css', null, array());
			  
		}
		

		?>
		<div id="submenu-box2">
			<div class="t">
				<div class="t">
					<div class="t">
					</div>
				</div>
			</div>			
			<div class="m">
				<ul id="submenu">
					<li>
						<?php $class = ($task == 'ninjaboard_usersync_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_usersync_view"><?php echo JText::_('NB_USERSYNC'); ?></a>
					</li>					
				</ul>
				<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
					<div class="b">
					</div>
				</div>
			</div>
		</div>
		<form action="index.php" method="post" name="adminForm" autocomplete="off">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_JOOMLAUSERGROUP'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="role">
									<?php echo JText::_('NB_GROUP'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['joomlagroup']; ?>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_USERDETAILSNB'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="role">
									<?php echo JText::_('NB_USERROLE'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['roles']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="system_emails">
									<?php echo JText::_('NB_RECEIVESYSTEMEMAILS'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['systememails']; ?>
							</td>
						</tr>					
					</table>
				</fieldset>			
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_PREFERENCES'); ?>
					</legend>
						<fieldset class="adminform">
							<legend>
								<?php echo JText::_('NB_GENERALSETTINGS'); ?>
							</legend>
							<table class="admintable" cellspacing="1">
								<tr>
									<td class="key">
										<?php echo JText::_('NB_SHOWEMAIL'); ?>
									</td>
									<td>
										<?php echo $lists['show_email']; ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('NB_SHOWONLINESTATE'); ?>
									</td>
									<td>
										<?php echo $lists['show_online_state']; ?>
									</td>
								</tr>							
							</table>
						</fieldset>
						<fieldset class="adminform">
							<legend>
								<?php echo JText::_('NB_POSTSETTINGS'); ?>
							</legend>					
							<table class="admintable" cellspacing="1">					
								<tr>
									<td class="key">
										<?php echo JText::_('NB_ENABLEBBCODE'); ?>
									</td>
									<td>
										<?php echo $lists['enablebbcode']; ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('Enable Smilies'); ?>
									</td>
									<td>
										<?php echo $lists['enablesmilies']; ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('NB_NOTIFYONREPLY'); ?>
									</td>
									<td>
										<?php echo $lists['notify_on_reply']; ?>
									</td>
								</tr>							
							</table>
					</fieldset>
					<fieldset class="adminform">
						<legend>
							<?php echo JText::_('NB_TIMESETTINGS'); ?>
						</legend>										
						<table class="admintable" cellspacing="1">
							<tr>
								<td class="key">
									<label for="time_zone">
										<?php echo JText::_('NB_TIMEZONE'); ?>
									</label>
								</td>
								<td>
									<?php echo $lists['timezones']; ?>			
								</td>
							</tr>
							<tr>
								<td class="key">
									<label for="time_format">
										<?php echo JText::_('NB_TIMEFORMAT'); ?>
									</label>
								</td>
								<td>
									<?php echo $lists['timeformats']; ?>
								</td>
							</tr>																									
						</table>
					</fieldset>
				</fieldset>		
			</div>
			<div class="clr"></div>
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));
	}
			
}
?>