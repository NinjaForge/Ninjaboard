<?php
/**
 * @version $Id: design.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Designs View
 *
 * @package Ninjaboard
 */
class ViewDesign {

	/**
	 * show designs
	 */	
	function showDesigns(&$rows, $pageNav, &$lists) {
		global $mainframe;
		
		// Initialize variables
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
				<ul id="submenu2">
					<li>
						<a class="active" href="index.php?option=com_ninjaboard&task=ninjaboard_design_view"><?php echo JText::_('NB_DESIGNS'); ?></a>
					</li>
					<li>
						<a href="index.php?option=com_ninjaboard&task=display&controller=template"><?php echo JText::_('NB_TEMPLATES'); ?></a>
					</li>
					<li>
						<a href="index.php?option=com_ninjaboard&task=display&controller=style"><?php echo JText::_('NB_STYLES'); ?></a>
					</li>					
					<!--<li>
						<a href="index.php?option=com_ninjaboard&task=display&controller=emoticonset"><?php echo JText::_('NB_EMOTICONSETS'); ?></a>
					</li>-->
					<li>
						<a href="index.php?option=com_ninjaboard&task=display&controller=buttonset"><?php echo JText::_('NB_BUTTONSETS'); ?></a>
					</li>
					<li>
						<a href="index.php?option=com_ninjaboard&task=display&controller=iconset"><?php echo JText::_('NB_ICONSETS'); ?></a>
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
					<th nowrap="nowrap" width="20%">
						<?php echo JHTML::_('grid.sort', 'NB_NAME', 'd.name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_design_view'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('NB_TEMPLATE'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('NB_STYLE'); ?>
					</th>					
					<?php /*<th width="15%">
						<php echo JText::_('NB_EMOTICONSET'); >
					</th> */ ?>
					<th width="15%">
						<?php echo JText::_('NB_BUTTONSET'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('NB_ICONSET'); ?>
					</th>															
					<th width="5%">
						<?php echo JText::_('NB_DEFAULT'); ?>
					</th>																																		
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 'd.id', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_design_view'); ?>
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
			for ( $i=0, $n=count( $rows ); $i < $n; $i++ ) {
				$row 	=& $rows[$i];
				
				$img_default = $row->default_design ? NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'icon-16-default.png' : NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'spacer.png';
				$alt_default = $row->default_design ? JText::_('NB_DEFAULT') :  JText::_('NB_NOTDEFAULT');				
				
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_design_edit&cid[]='. $row->id .'&hidemainmenu=1';
				
				$checked 	= JHTML::_('grid.checkedout', $row, $i);																																		
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
							if ( JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) {
								echo $row->name;
							} else {
								?>
								<a href="<?php echo JRoute::_( $link ); ?>">
									<?php echo htmlspecialchars($row->name, ENT_QUOTES); ?>
								</a>
								<?php
							}
						?>				
					</td>
					<td>
						<?php echo $row->template_name; ?>				
					</td>
					<td>
						<?php echo $row->style_name; ?>				
					</td>					
					<?php /*<td>
						<php echo $row->emoticon_set_name; >				
					</td> */ ?>
					<td>
						<?php echo $row->button_set_name; ?>				
					</td>
					<td>
						<?php echo $row->icon_set_name; ?>				
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
			<input type="hidden" name="task" value="ninjaboard_design_view" />
			<input type="hidden" name="hidemainmenu" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="" />
		</form>
		<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));

	}
	
	/**
	 * edit design
	 */
	function editDesign(&$row, &$lists) {
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
			if (pressbutton == 'ninjaboard_design_cancel') {
				submitform(pressbutton);
				return;
			}
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

			// do field validation
			if (trim(form.name.value) == "") {
				alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_DESIGN')); ?>");
			} else {
				submitform(pressbutton);
			}
		}
		</script>
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_DESIGNDETAILS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="50" value="<?php echo $row->name; ?>" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="template">
									<?php echo JText::_('NB_TEMPLATE'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['templates']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="style">
									<?php echo JText::_('NB_STYLE'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['styles']; ?>
							</td>
						</tr>					
						<?php /*<tr>
							<td class="key">
								<label for="emoticon_set">
									<php echo JText::_('NB_EMOTICONSET'); >
								</label>
							</td>
							<td>
								<php echo $lists['emoticonsets']; >
							</td>
						</tr> */ ?>
						<tr>
							<td class="key">
								<label for="button_set">
									<?php echo JText::_('NB_BUTTONSET'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['buttonsets']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="id_icon_set">
									<?php echo JText::_('NB_ICONSET'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['iconsets']; ?>
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
