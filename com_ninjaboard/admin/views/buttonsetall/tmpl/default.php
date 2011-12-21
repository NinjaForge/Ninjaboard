<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

	//I removed this because there are no preferences parameters at this point
	//JToolBarHelper::preferences('com_ninjaboard','600');

	//include our helper file to create the footer and buttons
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'HTML_ninjahelper_admin.php');
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'file.php');
	
	JToolBarHelper::title(JText::_('NB_BUTTONSETMANAGER'), 'ninjahdr');
	JToolBarHelper::custom('ninjaboard_controlpanel', 'back.png', 'back_f2.png', 'NB_NB', false);
	JToolBarHelper::custom('setDefault', 'default.png', 'default_f2.png', 'NB_DEFAULT', false);
	JToolBarHelper::editList();
	JToolBarHelper::deleteList(JText::_('Are you sure you want to delete this buttonset').'?');

//we need the user id below to check if an item is checked out by this user or another
	$user	=& JFactory::getUser();
		
//Include our stylesheet for our Ninja Component Styling
	$document =& JFactory::getDocument();
	$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	$cssFile = JURI::base(true).'/components/com_ninjaboard/css/icon.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());
	
//
	$task = JRequest::getVar('task');

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
					<a href="index.php?option=com_ninjaboard&task=ninjaboard_design_view"><?php echo JText::_('NB_DESIGNS'); ?></a>
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
					<a class="active" href="index.php?option=com_ninjaboard&task=display&controller=buttonset"><?php echo JText::_('NB_BUTTONSETS'); ?></a>
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
		<form id="Uploadfiles" name="Uploadfiles" action="index.php" enctype="multipart/form-data" method="post">
	        
	      <p>
	            <label for="file"><?php echo JText::_( 'File to upload' ).':'; ?></label>
	            <input id="file" type="file" name="file" size="100">
	            <input id="submit" type="submit" name="submit" value="<?php echo JText::_( 'Upload' ); ?>">
	      </p>
	      
          <input type="hidden" name="option" value="com_ninjaboard" />
		  <input type="hidden" name="controller" value="files" />
		  <input type="hidden" name="task" value="uploadbuttonset" />
		  <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo NinjaboardFileHelper::getUploadMaxFilesize(); ?>" />
		  <?php echo JHTML::_( 'form.token' ); ?>
	          
    	</form> 
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
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tfoot>			
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($this->rows); $i < $n; $i++) {
				$row 	=& $this->rows[$i];
				
				if (isset($row->authorUrl) && $row->authorUrl != '') {
					if (isset($row->author) && $row->author != '') {
						$row->author = '<a href="'. $row->authorUrl .'" target="_blank">'. $row->author .'</a>';
					} else {
						$row->author = '<a href="'. $row->authorUrl .'" target="_blank">'. str_replace('http://', '', $row->authorUrl) .'</a>';
					}
				}
				
				$img_default = $row->default_button_set ? NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'icon-16-default.png' : NB_ADMINIMAGES_LIVE.DL.'menu'.DL.'spacer.png';
				$alt_default = $row->default_button_set ? JText::_('NB_DEFAULT') :  JText::_('NB_NOTDEFAULT');				
								
				$link = 'index.php?option=com_ninjaboard&task=edit&controller=buttonset&cid='. $row->file_name .'&hidemainmenu=1';																																		
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->file_name; ?>" onclick="isChecked(this.checked);" />
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
			<input type="hidden" name="controller" value="buttonset" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));
?>
