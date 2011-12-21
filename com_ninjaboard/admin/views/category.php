<?php
/**
 * @version $Id: category.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Category View
 *
 * @package Ninjaboard
 */
class ViewCategory {

	/**
	 * shows categories
	 */	
	function showCategories(&$rows, $pageNav, &$lists) {
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
						<?php $class = ($task == 'ninjaboard_forum_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_forum_view"><?php echo JText::_('NB_FORUMS'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_category_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_category_view"><?php echo JText::_('NB_CATEGORIES'); ?></a>
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
					<th nowrap="nowrap" width="90%">
						<?php echo JHTML::_('grid.sort', 'NB_CATEGORY', 'c.name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_category_view'); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_PUBLISHED', 'c.published', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_category_view'); ?>
					</th>										
					<th nowrap="nowrap" width="8%">
						<?php echo JHTML::_('grid.sort', 'NB_ORDER', 'c.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_category_view'); ?>
						<?php echo JHTML::_('grid.order', $rows, 'filesave.png', 'ninjaboard_category_saveorder' ); ?>
					</th>																												
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 'c.id', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_category_view'); ?>
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
				$task_published = $row->published ? 'ninjaboard_category_unpublish' : 'ninjaboard_category_publish';
				$alt_published = $row->published ? JText::_('NB_PUBLISHED') : JText::_('NB_UNPUBLISHED');
								
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_category_edit&hidemainmenu=1&cid[]='. $row->id;
				
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
					<td align="center">
						<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published;?>')">
						<img src="images/<?php echo $img_published;?>" width="16" height="16" border="0" alt="<?php echo $alt_published; ?>" />
						</a>
					</td>						
					<td class="order">
						<span><?php echo $pageNav->orderUpIcon($i, true, 'ninjaboard_category_orderup', 'NB_ORDERUP', true); ?></span>
						<span><?php echo $pageNav->orderDownIcon($i, $n, true, 'ninjaboard_category_orderdown', 'NB_ORDERDOWN', true); ?></span>
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
			<input type="hidden" name="task" value="ninjaboard_category_view" />
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
	 * edit category
	 */
	function editCategory(&$row, &$lists) {
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
				if (pressbutton == 'ninjaboard_category_cancel') {
					submitform(pressbutton);
					return;
				}

				// do field validation
				if (trim(form.name.value) == "") {
					alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_CATEGORY')); ?>");
				} else {
					submitform(pressbutton);
				}
			}
		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_CATEGORYDETAILS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name" class="hasTip" title="<?php echo JText::_('NB_NAME') .'::'. JText::_('NB_CATEGORYNAMEDESC'); ?>">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="50" value="<?php echo $row->name; ?>" maxlength="255" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="published" class="hasTip" title="<?php echo JText::_('NB_PUBLISHED') .'::'. JText::_('NB_CATEGORYPUBLISHEDDESC'); ?>">
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
