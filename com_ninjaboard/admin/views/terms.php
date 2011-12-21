<?php
/**
 * @version $Id: terms.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Terms View
 *
 * @package Ninjaboard
 */
class ViewTerms {

	/**
	 * show terms
	 */	
	function showTerms(&$rows, $pageNav, &$lists) {
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
					<th nowrap="nowrap" width="88%">
						<?php echo JHTML::_('grid.sort', 'NB_TERMS', 't.terms', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_terms_view'); ?>
					</th>
					<th nowrap="nowrap" width="10%">
						<?php echo JHTML::_('grid.sort', 'NB_LOCALE', 't.locale', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_terms_view'); ?>
					</th>								
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_PUBLISHED', 't.published', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_terms_view'); ?>
					</th>																													
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_('grid.sort', 'NB_ID', 't.id', @$lists['filter_order_Dir'], @$lists['filter_order'], 'ninjaboard_terms_view'); ?>
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
				$task_published = $row->published ? 'ninjaboard_terms_unpublish' : 'ninjaboard_terms_publish';
				$alt_published = $row->published ? JText::_('NB_PUBLISHED') :  JText::_('NB_UNPUBLISHED');
								
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_terms_edit&hidemainmenu=1&cid[]='. $row->id;																																		
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
							if (JTable::isCheckedOut($user->get ('id'), $row->id)) {
								echo $row->name;
							} else {
								?>
								<a href="<?php echo JRoute::_($link); ?>">
									<?php echo htmlspecialchars($row->terms, ENT_QUOTES); ?>
								</a>
								<?php
							}
						?>				
					</td>
					<td>
						<?php echo $row->locale; ?>				
					</td>									
					<td align="center">
						<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task_published; ?>')">
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
			<input type="hidden" name="task" value="ninjaboard_terms_view" />
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
	 * edit terms
	 */
	function editTerms(&$row, &$lists) {
		global $mainframe;
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(NB_ADMINCSS_LIVE.DL.'icon.css');
		
		$editor =& JFactory::getEditor();
		
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
				if (pressbutton == 'ninjaboard_terms_cancel') {
					submitform(pressbutton);
					return;
				}
	
				// do field validation
				if (trim(form.name.value) == "") {
					alert("<?php echo JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_TERMS')); ?>");
				} else {
					<?php echo $editor->save(); ?>
					submitform(pressbutton);
				}
			}
		</script>
		<form action="index.php" method="post" name="adminForm">
			<div class="col100">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_TERMSOFAGREEMENT'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="terms">
									<?php echo JText::_('NB_TERMS'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="terms" id="terms" class="inputbox" size="40" value="<?php echo $row->terms; ?>" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="locale">
									<?php echo JText::_('NB_LOCALE'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="locale" id="locale" class="inputbox" size="40" value="<?php echo $row->locale; ?>" maxlength="5" />
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
						<tr>
							<td colspan="2">
								<?php
									// parameters : areaname, content, width, height, cols, rows
									echo $editor->display('termstext',  $row->termstext , '100%', '450', '75', '20') ;
								?>
							</td>
						</tr>																																			
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_AGREEMENT'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="agreement">
									<?php echo JText::_('NB_AGREEMENT'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="agreement" id="agreement" class="inputbox" size="40" value="<?php echo $row->agreement; ?>" maxlength="100" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php
									// parameters : areaname, content, width, height, cols, rows
									echo $editor->display('agreementtext',  $row->agreementtext , '100%', '450', '75', '20') ;
								?>
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