<?php
/**
 * @version $Id: profilefieldlistvalue.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Profile Field List Value View
 *
 * @package Ninjaboard
 */
class ViewProfileFieldListValue {

	/**
	 * show profile field list values
	 */	
	function showProfileFieldListValues(&$rows, &$pageNav, &$lists) {
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
						<?php $class = ($task == 'ninjaboard_profilefield_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_profilefield_view"><?php echo JText::_('NB_PROFILEFIELDS'); ?></a>
					</li>								
					<li>
						<?php $class = ($task == 'ninjaboard_profilefieldset_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_view"><?php echo JText::_('NB_PROFILEFIELDSET'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_profilefieldlist_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlist_view"><?php echo JText::_('NB_PROFILEFIELDLISTS'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_profilefieldlistvalue_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_view"><?php echo JText::_('NB_PROFILEFIELDLISTVALUES'); ?></a>
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
			<table>
				<tr>
					<td width="100%">
					</td>
					<td nowrap="nowrap">
						<?php echo $lists['profilefieldlists'];?>
					</td>
				</tr>
			</table>
			<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th nowrap="nowrap" width="5">
						<?php echo JText::_('Num'); ?>
					</th>
					<th nowrap="nowrap" width="5">
						<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
					</th>
					<th nowrap="nowrap" width="40%">
						<?php echo JHTML::_('grid.sort', 'NB_NAME', 'v.name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefieldlistvalue_view'); ?>
					</th>					
					<th nowrap="nowrap" width="20%">
						<?php echo JHTML::_('grid.sort', 'NB_VALUE', 'v.value', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefieldlistvalue_view'); ?>
					</th>
					<th nowrap="nowrap" width="30%">
						<?php echo JText::_('NB_PROFILEFIELDLIST'); ?>
					</th>
					<th nowrap="nowrap" width="8%">
						<?php echo JHTML::_('grid.sort', 'NB_ORDER', 'v.id_profile_field_list, v.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefieldlistvalue_view'); ?>
						<?php echo JHTML::_('grid.order', $rows, 'filesave.png', 'ninjaboard_profilefieldlistvalue_saveorder'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_PUBLISHED', 'v.published', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefieldlistvalue_view'); ?>
					</th>																													
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 'v.id', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefieldlistvalue_view'); ?>
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
				
				$img_published = $row->published ? 'tick.png' : 'publish_x.png';
				$task_published = $row->published ? 'ninjaboard_profilefieldlistvalue_unpublish' : 'ninjaboard_profilefieldlistvalue_publish';
				$alt_published = $row->published ? JText::_('NB_PUBLISHED') :  JText::_('NB_UNPUBLISHED');
								
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefieldlistvalue_edit&hidemainmenu=1&cid[]='. $row->id;																																		
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $i+1 ?>
					</td>
					<td>
						<?php echo JHTML::_('grid.id', $i, $row->id); ?>
					</td>
					<td>
						<?php
							if (JTable::isCheckedOut($user->get('id'), $row->id)) {
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
						<?php echo $row->value; ?>				
					</td>
					<td>
						<?php echo $row->profile_field_list_name; ?>				
					</td>
					<td class="order">
						<span><?php echo $pageNav->orderUpIcon($i, true, 'ninjaboard_profilefieldlistvalue_orderup', 'NB_ORDERUP', true); ?></span>
						<span><?php echo $pageNav->orderDownIcon($i, $n, true, 'ninjaboard_profilefieldlistvalue_orderdown', 'NB_ORDERDOWN', true); ?></span>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
					</td>					
					<td align="center">
						<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
						<img src="images/<?php echo $img_published;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
						</a>
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
			<input type="hidden" name="task" value="ninjaboard_profilefieldlistvalue_view" />
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
	 * edit profile field list value
	 */
	function editProfileFieldListValue(&$row, &$lists) {
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
				if (pressbutton == 'ninjaboard_profilefieldlistvalue_cancel') {
					submitform(pressbutton);
					return;
				}
				var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
	
				// do field validation
				if (trim(form.name.name) == "") {
					alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_PROFILEFIELDLISTVALUE')); ?>");
				} else {
					submitform(pressbutton);
				}
			}
		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_FIELDLISTVALUESDETAILS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="50" value="<?php echo $row->name; ?>" maxlength="255" />
							</td>
						</tr>				
						<tr>
							<td class="key">
								<label for="value">
									<?php echo JText::_('NB_VALUE'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="value" id="value" class="inputbox" size="50" value="<?php echo $row->value; ?>" maxlength="255" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="profilefieldlists">
									<?php echo JText::_('NB_PROFILEFIELDLIST'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['profilefieldlists']; ?>			
							</td>
						</tr>					
						<tr>
							<td class="key">
								<label for="type">
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