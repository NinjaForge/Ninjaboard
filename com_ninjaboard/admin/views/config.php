<?php
/**
 * @version $Id: config.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Config View
 *
 * @package Ninjaboard
 */
class ViewConfig {

	/**
	 * show configs
	 */	
	function showConfigs(&$rows, $pageNav, &$lists) {
		global $mainframe;
		
		// initialize variables
		$user	=& JFactory::getUser();
				
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
						<?php $class = ($task == 'ninjaboard_config_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_config_view"><?php echo JText::_('NB_CONFIG'); ?></a>
					</li>				
					<li>
						<?php $class = ($task == 'ninjaboard_timezone_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_timezone_view"><?php echo JText::_('NB_TIMEZONES'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_timeformat_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_timeformat_view"><?php echo JText::_('NB_TIMEFORMATS'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_terms_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_terms_view"><?php echo JText::_('NB_TERMS'); ?></a>
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
		<form action="index.php?option=com_ninjaboard" method="post" name="adminForm">
			<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th nowrap="nowrap" width="5">
						<?php echo JText::_('Num'); ?>
					</th>
					<th nowrap="nowrap" width="5">
						<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
					</th>
					<th nowrap="nowrap" width="30%">
						<?php echo JHTML::_('grid.sort', 'NB_NAME', 'c.name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_config_view'); ?>
					</th>
					<th width="20%">
						<?php echo JHTML::_('grid.sort', 'NB_DESIGN', 'design_name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_config_view'); ?>
					</th>
					<th width="20%">
						<?php echo JHTML::_('grid.sort', 'NB_TIMEZONE', 'timezone_name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_config_view'); ?>
					</th>
					<th width="20%">
						<?php echo JHTML::_('grid.sort', 'NB_TIMEFORMAT', 'timeformat_name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_config_view'); ?>
					</th>														
					<th width="5%">
						<?php echo JText::_('NB_DEFAULT'); ?>
					</th>																																		
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 'c.id', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_config_view'); ?>
					</th>			
				</tr>
			</thead>
			<tfoot>
				<td colspan="20">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tfoot>			
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row 	=& $rows[$i];
				
				$img_default = $row->default_config ? NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'icon-16-default.png' : NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'spacer.png';
				$alt_default = $row->default_config ? JText::_('NB_DEFAULT') :  JText::_('NB_NOTDEFAULT');
								
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_config_edit&cid[]='. $row->id .'&hidemainmenu=1';
				
				$checked = JHTML::_('grid.checkedout', $row, $i);																																		
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $i+1 ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<?php
							if (JTable::isCheckedOut($user->get ('id'), $row->checked_out)) {
								echo $row->name;
							} else {
								?>
								<a href="<?php echo JRoute::_($link); ?>">
									<?php echo htmlspecialchars($row->name, ENT_QUOTES); ?>
								</a>
								<?php
							}
						?>				
					</td>
					<td>
						<?php echo $row->design_name; ?>				
					</td>
					<td>
						<?php echo $row->timezone_name; ?>				
					</td>
					<td>
						<?php echo $row->timeformat_name; ?>				
					</td>															
					<td align="center">
						<img src="<?php echo $img_default; ?>" width="16" height="16" border="0" alt="<?php echo $alt_default; ?>" />
					</td>																																																												
					<td>
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="ninjaboard_config_view" />
			<input type="hidden" name="hidemainmenu" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="" />
		</form>
		<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));


	}
	
	/**
	 * edit config
	 */
	function editConfig(&$row, &$lists) {
		global $mainframe;
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(NB_ADMINCSS_LIVE.DL.'icon.css');
		
		
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
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'ninjaboard_config_cancel') {
				submitform(pressbutton);
				return;
			}
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

			// do field validation
			if (trim(form.name.value) == "") {
				alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_CONFIG')); ?>");
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_CONFIGDETAILS'); ?>
					</legend>		
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name" class="hasTip" title="<?php echo JText::_('NB_NAME') .'::'. JText::_('NB_CONFIGNAMEDESC'); ?>">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="50" value="<?php echo $row->name; ?>" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="default_config" class="hasTip" title="<?php echo JText::_('NB_DEFAULT') .'::'. JText::_('NB_CONFIGDEFAULTDESC'); ?>">
									<?php echo JText::_('NB_DEFAULT'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['defaultconfig']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="id_design" class="hasTip" title="<?php echo JText::_('NB_DESIGN') .'::'. JText::_('NB_CONFIGDESIGNDESC'); ?>">
									<?php echo JText::_('NB_DESIGN'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['designs']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="id_timezone" class="hasTip" title="<?php echo JText::_('NB_TIMEZONE') .'::'. JText::_('NB_CONFIGTIMEZONEDESC'); ?>">
									<?php echo JText::_('NB_TIMEZONE'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['timezones']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="id_timeformat" class="hasTip" title="<?php echo JText::_('NB_TIMEFORMAT') .'::'. JText::_('NB_CONFIGTIMEFORMATDESC'); ?>">
									<?php echo JText::_('NB_TIMEFORMAT'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['timeformats']; ?>
							</td>
						</tr>
						<?php //We have hidden the editor selection for now as we want to default to punymce. 
							/*<tr>
							<td class="key">
								<label for="editor" class="hasTip" title="<php echo JText::_('NB_EDITOR') .'::'. JText::_('NB_CONFIGEDITORDESC'); >">
									<php echo JText::_('NB_EDITOR'); >
								</label>
							</td>
							<td>
								<php echo $lists['editors']; >
							</td>
						</tr> */ ?>
						<tr>
							<td class="key">
								<label for="id_topic_icon" class="hasTip" title="<?php echo JText::_('NB_DEFAULTTOPICICON') .'::'. JText::_('NB_CONFIGDEFAULTTOPICICONDESC'); ?>">
									<?php echo JText::_('NB_DEFAULTTOPICICON'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['topicicons']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="id_post_icon" class="hasTip" title="<?php echo JText::_('NB_DEFAULTPOSTICON') .'::'. JText::_('NB_CONFIGDEFAULTPOSTICONDESC'); ?>">
									<?php echo JText::_('NB_DEFAULTPOSTICON'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['posticons']; ?>
							</td>
						</tr>																				
					</table>		
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_BOARDSETTINGS'); ?>
					</legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $lists['board_settings']->render('board_settings'); ?>
						</td>
					</tr>
					</table>		
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_LATESTPOSTSETTINGS'); ?>
					</legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $lists['latestpost_settings']->render('latestpost_settings'); ?>
						</td>
					</tr>
					</table>		
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_FEEDSETTINGS'); ?>
					</legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $lists['feed_settings']->render('feed_settings'); ?>
						</td>
					</tr>
					</table>		
				</fieldset>
			</div>
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_ATTACHMENTS'); ?>
					</legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $lists['attachment_settings']->render('attachment_settings'); ?>
						</td>
					</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_VIEWSETTINGS'); ?>
					</legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $lists['view_settings']->render('view_settings'); ?>
						</td>
					</tr>
					</table>		
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_VIEWFOOTERSETTINGS'); ?>
					</legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $lists['view_footer_settings']->render('view_footer_settings'); ?>
						</td>
					</tr>
					</table>		
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_REGISTRATIONUSERSETTINGSDEFAULTS'); ?>
					</legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $lists['user_settings_defaults']->render('user_settings_defaults'); ?>
						</td>
					</tr>
					</table>		
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_AVATARSETTINGS'); ?>
					</legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $lists['avatar_settings']->render('avatar_settings'); ?>
						</td>
					</tr>
					</table>		
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_CAPTCHASETTINGS'); ?>
					</legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $lists['captcha_settings']->render('captcha_settings'); ?>
						</td>
					</tr>
					</table>		
				</fieldset>
			</div>
			<div class="clr"></div>				
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="" />
			<?php echo JHTML::_('form.token'); ?>
		</form>
	<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));


	}

}
?>
