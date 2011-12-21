<?php
/**
 * @version $Id: profilefield.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Profile Field View
 *
 * @package Ninjaboard
 */
class ViewProfileField {

	/**
	 * show profile fields
	 */	
	function showProfileFields(&$rows, $pageNav, &$lists) {
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
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_profilefieldset_view"><?php echo JText::_('NB_PROFILEFIELDSETS'); ?></a>
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
						<?php echo JHTML::_('grid.sort', 'NB_NAME', 'p.name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
					</th>
					<th nowrap="nowrap" width="20%">
						<?php echo JHTML::_('grid.sort', 'NB_TITLE', 'p.title', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
					</th>					
					<th nowrap="nowrap" width="20%">
						<?php echo JHTML::_('grid.sort', 'NB_TYPE', 'p.type', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
					</th>
					<th nowrap="nowrap" width="20%">
						<?php echo JHTML::_('grid.sort', 'NB_PROFILEFIELDSET', 'profile_field_set_name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
					</th>				
					<th nowrap="nowrap" width="5%">
						<?php echo JHTML::_('grid.sort', 'NB_SIZE', 'p.size', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
					</th>
					<th nowrap="nowrap" width="5%">
						<?php echo JHTML::_('grid.sort', 'NB_LENGTH', 'p.length', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
					</th>															
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_PUBLISHED', 'p.published', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_REQUIRED', 'p.required', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
					</th>															
					<th nowrap="nowrap" width="8%">
						<?php echo JHTML::_('grid.sort', 'NB_ORDER', 's.ordering, p.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
						<?php echo JHTML::_('grid.order', $rows, 'filesave.png', 'ninjaboard_profilefield_saveorder' ); ?>
					</th>																													
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 'p.id', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_profilefield_view'); ?>
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
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row 	=& $rows[$i];
				
				$img_published = $row->published ? 'tick.png' : 'publish_x.png';
				$task_published = $row->published ? 'ninjaboard_profilefield_unpublish' : 'ninjaboard_profilefield_publish';
				$alt_published = $row->published ? JText::_('NB_PUBLISHED') : JText::_('NB_UNPUBLISHED');
				
				$img_required = $row->required ? 'tick.png' : 'publish_x.png';
				$alt_required = $row->required ? JText::_('NB_REQUIRED') : JText::_('NB_NOTREQUIRED');
								
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_profilefield_edit&hidemainmenu=1&cid[]='. $row->id;																																		
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
						<?php echo $row->title; ?>				
					</td>					
					<td>
						<?php echo $row->type; ?>				
					</td>
					<td>
						<?php echo $row->profile_field_set_name; ?>				
					</td>					
					<td align="center">
						<?php echo $row->size; ?>				
					</td>
					<td align="center">
						<?php echo $row->length; ?>				
					</td>														
					<td align="center">
						<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
							<img src="images/<?php echo $img_published;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
						</a>
					</td>
					<td align="center">
						<img src="images/<?php echo $img_required;?>" width="16" height="16" border="0" alt="<?php echo $alt_required; ?>" />
					</td>											
					<td class="order">
						<span><?php echo $pageNav->orderUpIcon($i, ($row->id_profile_field_set == @$rows[$i-1]->id_profile_field_set), 'ninjaboard_profilefield_orderup', 'NB_ORDERUP', true); ?></span>
						<span><?php echo $pageNav->orderDownIcon($i, $n, ($row->id_profile_field_set == @$rows[$i+1]->id_profile_field_set), 'ninjaboard_profilefield_orderdown', 'NB_ORDERDOWN', true); ?></span>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
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
			<input type="hidden" name="task" value="ninjaboard_profilefield_view" />
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
	 * edit profile field
	 */
	function editProfileField(&$row, &$lists) {
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
				if (pressbutton == 'ninjaboard_profilefield_cancel') {
					submitform( pressbutton );
					return;
				}
				var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
	
				// do field validation
				if (trim(form.name.value) == "") {
					alert( "You must provide a name." );
				} else {
					submitform( pressbutton );
				}
			}
	
			function setFieldPrefix(o){
				if(o.value!='') {
					o.value=o.value.replace('p_','');
					o.value=o.value.replace(/[^a-zA-Z]+/g,'');
					o.value='p_' + o.value;
				}
			}
					
			function selElement(selectedElement) {
				switch (selectedElement) {
					case '0':
						setFieldActivation(true, false, false, false);
					break;
					case '1':
						setFieldActivation(true, true, true, true);
					break;
					case '2':
						setFieldActivation(false, true, true, true);
					break;
					case '3':
						setFieldActivation(false, true, true, true);
					break;
					case '4':
						setFieldActivation(false, true, false, true);
					break;
					case '5':
						setFieldActivation(false, true, true, true);
					break;																			
					default:
						setFieldActivation(true, true, true, true);
				}
				
				function setFieldActivation(field1, field2, field3, field4) {
					var form = document.adminForm;
					form.id_profile_field_list.disabled=field1;
					form.type.disabled=field2;
					form.size.disabled=field3; 
					form.max_length.disabled=field4;				
				}
			}		
		</script>
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_PROFILEFIELDDETAILS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="40" value="<?php echo $row->name; ?>" maxlength="100" onchange="setFieldPrefix(this);" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="title">
									<?php echo JText::_('NB_TITLE'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="title" id="title" class="inputbox" size="40" value="<?php echo $row->title; ?>" maxlength="100" />
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
								<label for="profilefieldsets">
									<?php echo JText::_('NB_PROFILEFIELDSET'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['profilefieldsets']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="elements">
									<?php echo JText::_('NB_ELEMENT'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['elements']; ?>			
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
									<?php echo JText::_('NB_TYPE'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['types']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="size">
									<?php echo JText::_('NB_SIZE'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="size" class="inputbox" size="40" value="<?php echo $row->size; ?>" maxlength="50" />
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
						<tr>
							<td class="key">
								<label for="required">
									<?php echo JText::_('NB_REQUIRED'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['required']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="disabled">
									<?php echo JText::_('NB_DISABLED'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['disabled']; ?>			
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="show_on_registration">
									<?php echo JText::_('NB_SHOWONREGISTRATION'); ?>
								</label>
							</td>
							<td>
								<?php echo $lists['show_on_registration']; ?>			
							</td>
						</tr>																																																		
					</table>
				</fieldset>
			</div>
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_VISUALAPPEREANCE'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="length">
									<?php echo JText::_('NB_LENGTH'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="length" class="inputbox" size="40" value="<?php echo $row->length; ?>" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="columns">
									<?php echo JText::_('NB_COLUMNS'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="columns" class="inputbox" size="40" value="<?php echo $row->columns; ?>" maxlength="50" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="rows">
									<?php echo JText::_('NB_ROWS'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="rows" class="inputbox" size="40" value="<?php echo $row->rows; ?>" maxlength="50" />
							</td>
						</tr>																																																																	
					</table>
				</fieldset>
			</div>								
			<div class="clr"></div>
			<script language="javascript" type="text/javascript">	
				selElement(document.adminForm.element.options[document.adminForm.element.selectedIndex].value);
			</script>			
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