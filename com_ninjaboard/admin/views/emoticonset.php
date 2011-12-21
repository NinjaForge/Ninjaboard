<?php
/**
 * @version $Id: emoticonset.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Emoticon Set View
 *
 * @package Ninjaboard
 */
class ViewEmoticonSet {

	/**
	 * show emoticon sets
	 */	
	function showEmoticonSets(&$rows, $pageNav) {
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
						<?php $class = ($task == 'ninjaboard_design_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_design_view"><?php echo JText::_('NB_DESIGNS'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_template_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_template_view"><?php echo JText::_('NB_TEMPLATES'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_style_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_style_view"><?php echo JText::_('NB_STYLES'); ?></a>
					</li>					
					<li>
						<?php $class = ($task == 'ninjaboard_emoticonset_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_emoticonset_view"><?php echo JText::_('NB_EMOTICONSETS'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_buttonset_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_buttonset_view"><?php echo JText::_('NB_BUTTONSETS'); ?></a>
					</li>
					<li>
						<?php $class = ($task == 'ninjaboard_iconset_view') ? 'class="active"' : ''; ?>
						<a <?php echo $class; ?> href="index.php?option=com_ninjaboard&task=ninjaboard_iconset_view"><?php echo JText::_('NB_ICONSETS'); ?></a>
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
					<th nowrap="nowrap" width="45%">
						<?php echo JText::_('NB_NAME'); ?>
					</th>					
					<th width="5%">
						<?php echo JText::_('NB_DEFAULT'); ?>
					</th>					
					<th width="10%" align="center">
						<?php echo JText::_('NB_VERSION'); ?>
					</th>
					<th width="10%" class="title">
						<?php echo JText::_('NB_DATE'); ?>
					</th>																													
					<th width="25%" class="title">
						<?php echo JText::_('NB_AUTHOR'); ?>
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
				
				if (isset($row->authorUrl) && $row->authorUrl != '') {
					if (isset($row->author) && $row->author != '') {
						$row->author = '<a href="'. $row->authorUrl .'" target="_blank">'. $row->author .'</a>';
					} else {
						$row->author = '<a href="'. $row->authorUrl .'" target="_blank">'. str_replace('http://', '', $row->authorUrl) .'</a>';
					}
				}
				
				$img_default = $row->default_emoticon_set ? NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'icon-16-default.png' : NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'spacer.png';
				$alt_default = $row->default_emoticon_set ? JText::_('NB_DEFAULT') :  JText::_('NB_NOTDEFAULT');				

				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_emoticonset_edit&cid[]='. $row->file_name .'&hidemainmenu=1';																																		
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $pageNav->getRowOffset($i); ?>
					</td>
					<td>
						<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->file_name; ?>" onclick="isChecked(this.checked);" />
					</td>
					<td>
						<?php
							if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
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
						<img src="<?php echo $img_default; ?>" width="16" height="16" border="0" alt="<?php echo $alt_default; ?>" />
					</td>
					<td align="center">
						<?php echo $row->version; ?>
					</td>
					<td>
						<?php echo $row->creationdate; ?>
					</td>																																																							
					<td>
						<?php echo $row->author; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="ninjaboard_emoticonset_view" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));

	}
	
	/**
	 * edit emoticon set
	 */
	function editEmoticonSet(&$ninjaboardEmoticonSet) {
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
				if (pressbutton == 'ninjaboard_emoticonset_cancel') {
					submitform(pressbutton);
					return;
				}
				submitform(pressbutton);
			}
		</script>
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_EMOTICONSETDETAILS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="50" value="<?php echo $ninjaboardEmoticonSet->name; ?>" maxlength="255" readonly="true" />
							</td>						
						</tr>						
					</table>
				</fieldset>
			</div>
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_EMOTICONSET'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
					<?php
					$k = 0;
					for ($i=0, $n=count($ninjaboardEmoticonSet->emoticons); $i < $n; $i++) {
						$emoticon =& $ninjaboardEmoticonSet->emoticons[$i];
					?>	
						<tr>
							<td class="key">
								<?php echo JText::_($emoticon->emoticon); ?>
							</td>
							<td>
								<img src="<?php echo $emoticon->fileName; ?>" title="<?php echo JText::_($emoticon->emoticon); ?>" class="ninjaboardEmoticon" />
							</td>						
						</tr>
						<?php
					}
					?>						
					</table>
				</fieldset>
			</div>		
			<div class="clr"></div>				
			<input type="hidden" name="id" value="<?php echo $ninjaboardEmoticonSet->xmlFile; ?>" />
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));


	}
			
}
?>
