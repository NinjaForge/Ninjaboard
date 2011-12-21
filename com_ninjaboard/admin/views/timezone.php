<?php
/**
 * @version $Id: timezone.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Time Zone View
 *
 * @package Ninjaboard
 */
class ViewTimeZone {

	/**
	 * show time zones
	 */	
	function showTimeZones(&$rows, $pageNav, &$lists) {
		global $mainframe;
		
		// initialize variables
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();			
		$document		=& JFactory::getDocument();
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
						<?php echo JHTML::_('grid.sort', 'NB_NAME', 'z.name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_timezone_view'); ?>
					</th>
					<th nowrap="nowrap" width="40%">
						<?php echo JHTML::_('grid.sort', 'NB_DESCRIPTION', 'z.description', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_timezone_view'); ?>
					</th>
					<th nowrap="nowrap" width="20%">
						<?php echo JText::_('NB_CURRENTTIME'); ?>
					</th>					
					<th nowrap="nowrap" width="8%">
						<?php echo JHTML::_('grid.sort', 'NB_ORDER', 'z.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_timezone_view'); ?>
						<?php echo JHTML::_('grid.order', $rows, 'filesave.png', 'ninjaboard_timezone_saveorder'); ?>
					</th>
					<th width="5%">
						<?php echo JText::_('NB_DEFAULT'); ?>
					</th>																			
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_PUBLISHED', 'z.published', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_timezone_view'); ?>
					</th>
					<th nowrap="nowrap" width="5%">
						<?php echo JHTML::_('grid.sort', 'NB_OFFSET', 'z.offset', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_timezone_view'); ?>
					</th>																																							
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 'z.id', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_timezone_view'); ?>
					</th>			
				</tr>
			</thead>
			<tfoot>
				<td colspan="10">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tfoot>			
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row 	=& $rows[$i];
	
				$img_default = $row->default_timezone ? NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'icon-16-default.png' : NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'spacer.png';
				$alt_default = $row->default_timezone ? JText::_('NB_DEFAULT') :  JText::_('NB_NOTDEFAULT');				
						
				$img_published = $row->published ? 'tick.png' : 'publish_x.png';
				$task_published = $row->published ? 'ninjaboard_timezone_unpublish' : 'ninjaboard_timezone_publish';
				$alt_published = $row->published ? JText::_('NB_PUBLISHED') : JText::_('NB_UNPUBLISHED');
								
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_timezone_edit&hidemainmenu=1&cid[]='. $row->id;
				
				$timeFormat = $ninjaboardUser->get('time_format');
				if ($timeFormat == '') {
					$timeFormat = $ninjaboardConfig->getTimeFormat();
				}
				
				$checked 	= JHTML::_('grid.checkedout', $row, $i);																																		
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $pageNav->getRowOffset($i); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<?php
							if (JTable::isCheckedOut($ninjaboardUser->get('id'), $row->checked_out)) {
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
						<?php echo $row->description; ?>				
					</td>
					<td>
						<?php echo NinjaboardHelper::formatDate(gmdate("Y-m-d H:i:s"), $timeFormat, $row->offset); ?>				
					</td>					
					<td class="order">
						<span><?php echo $pageNav->orderUpIcon($i, true, 'ninjaboard_timezone_orderup', 'NB_ORDERUP', true); ?></span>
						<span><?php echo $pageNav->orderDownIcon($i, $n, true, 'ninjaboard_timezone_orderdown', 'NB_ORDERDOWN', true); ?></span>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php //echo $disabled; ?> class="text_area" style="text-align: center" />
					</td>
					<td align="center">
						<img src="<?php echo $img_default; ?>" width="16" height="16" border="0" alt="<?php echo $alt_default; ?>" />
					</td>																			
					<td align="center">
						<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
							<img src="images/<?php echo $img_published;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
						</a>
					</td>
					<td>
						<?php echo $row->offset; ?>				
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
			<input type="hidden" name="task" value="ninjaboard_timezone_view" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="" />
		</form>
		<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));

	}
	
	/**
	 * edit time zone
	 */
	function editTimeZone(&$row, &$lists) {
		global $mainframe;
		
		// initialize variables
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(NB_ADMINCSS_LIVE.DL.'icon.css');

		$timeFormat = $ninjaboardUser->get('time_format');
		if ($timeFormat == '') {
			$timeFormat = $ninjaboardConfig->getTimeFormat();
		}
		
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
				if (pressbutton == 'ninjaboard_timezone_cancel') {
					submitform(pressbutton);
					return;
				}
				var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
	
				// do field validation
				if (trim(form.name.value) == "") {
					alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_TIMEZONE')); ?>");
				} else {
					submitform(pressbutton);
				}
			}
		</script>
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_TIMEZONEDETAILS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="40" value="<?php echo $row->name; ?>" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="description">
									<?php echo JText::_('NB_DESCRIPTION'); ?>
								</label>
							</td>
							<td>
								<textarea name="description" id="description" rows="5" cols="50" class="inputbox"><?php echo $row->description; ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="offset">
									<?php echo JText::_('NB_OFFSET'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="offset" id="offset" class="inputbox" size="40" value="<?php echo $row->offset; ?>" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="published">
									<?php echo JText::_('NB_PUBLISHED'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['published']; ?>			
							</td>
						</tr>															
					</table>
				</fieldset>
			</div>
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_CURRENTTIME'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td>
								<?php echo NinjaboardHelper::formatDate(gmdate("Y-m-d H:i:s"), $timeFormat, $row->offset); ?>				
							</td>					
						</tr>															
					</table>
				</fieldset>
			</div>								
			<div class="clr"></div>				
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));


	}
			
}
?>