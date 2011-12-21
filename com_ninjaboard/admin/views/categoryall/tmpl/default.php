<?php defined( '_JEXEC' ) or die( 'Restricted access' );

	//include our helper file to create the footer and buttons
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'HTML_ninjahelper_admin.php');
	
	JToolBarHelper::title( JText::_( 'NB_CATEGORYMANAGER' ), 'ninjahdr' );
	JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::addNew();
	JToolBarHelper::editList();
	JToolBarHelper::deleteList(JText::_('Are you sure you want to delete these categories?'));
		
	//we need the user id below to check if an item is checked out by this user or another
	$user	=& JFactory::getUser();
		
	//Include our stylesheet for our Ninja Component Styling
	$document =& JFactory::getDocument();
	$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	$cssFile = JURI::base(true).'/components/com_ninjaboard/css/icon.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	$task = JRequest::getCmd('task');

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
						<a href="index.php?option=com_ninjaboard&task=ninjaboard_forum_view"><?php echo JText::_('NB_FORUMS'); ?></a>
					</li>
					<li>
						<a class="active" href="index.php?option=com_ninjaboard&task=display&controller=category"><?php echo JText::_('NB_CATEGORIES'); ?></a>
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
						<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($this->rows); ?>);" />
					</th>
					<th nowrap="nowrap" width="90%">
						<?php echo JHTML::_('grid.sort', 'NB_CATEGORY', 'c.name', @$this->lists['filter_order_Dir'], @$this->lists['filter_order']); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_PUBLISHED', 'c.published', @$this->lists['filter_order_Dir'], @$this->lists['filter_order']); ?>
					</th>										
					<th nowrap="nowrap" width="8%">
						<?php echo JHTML::_('grid.sort', 'NB_ORDER', 'c.ordering', @$this->lists['filter_order_Dir'], @$this->lists['filter_order']); ?>
						<?php echo JHTML::_('grid.order', $this->rows, 'filesave.png', 'ninjaboard_category_saveorder' ); ?>
					</th>																												
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 'c.id', @$this->lists['filter_order_Dir'], @$this->lists['filter_order']); ?>
					</th>			
				</tr>
			</thead>
			<tfoot>
				<td colspan="20">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tfoot>			
			<tbody>
			<?php
			 jimport('joomla.filter.output');
			$k = 0;
			for ($i=0, $n=count($this->rows); $i < $n; $i++) {
				$row 	=& $this->rows[$i];
				
				/*
				$img_published = $row->published ? 'tick.png' : 'publish_x.png';
				$task_published = $row->published ? 'ninjaboard_category_unpublish' : 'ninjaboard_category_publish';
				$alt_published = $row->published ? JText::_('NB_PUBLISHED') : JText::_('NB_UNPUBLISHED');
				*/
    			$published = JHTML::_('grid.published', $row, $i );
								
				$link = JFilterOutput::ampReplace('index.php?option=com_ninjaboard&task=edit&controller=category&hidemainmenu=1&cid[]='. $row->id);
				
				$checked = JHTML::_('grid.checkedout', $row, $i);																																		
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
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
						<?php echo $published;
					/* <a href="javascript: void(0);" onClick="return listItemTask('cb<php echo $i;>','<php echo $task_published;>')">
						<img src="images/<php echo $img_published;>" width="16" height="16" border="0" alt="<php echo $alt_published; >" />
						</a> */ 
						?>
					</td>						
					<td class="order">
						<span><?php echo $this->pagination->orderUpIcon($i, true, 'ninjaboard_category_orderup', 'NB_ORDERUP', true); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'ninjaboard_category_orderdown', 'NB_ORDERDOWN', true); ?></span>
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
			<?php echo JHTML::_( 'form.token' ); ?>
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="controller" value="category" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['filter_order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="" />
		</form>
		<?php

		//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));

?>
