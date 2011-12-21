<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

	//I removed this because there are no preferences parameters at this point
	//JToolBarHelper::preferences('com_ninjaboard','600');

	//include our helper file to create the footer and buttons
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'HTML_ninjahelper_admin.php');
	
//set our page header and menu
	JToolBarHelper::title(JText::_('NB_STYLEMANAGER') .' - <span>'. JText::_('NB_EDIT') .'</span>', 'ninjahdr');
	JToolBarHelper::apply();				
	JToolBarHelper::save();		
	JToolBarHelper::cancel();

		
//Include our stylesheet for our Ninja Component Styling
	$document =& JFactory::getDocument();
	$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	$cssFile = JURI::base(true).'/components/com_ninjaboard/css/icon.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());

//add ie6 css if needed
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (strstr($ua, "MSIE")&& strstr($ua, "6")) {
		  
		$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin_ie6.css';
		$document->addStyleSheet($cssFile, 'text/css', null, array());
		  
	}
	
	
//Add our validation script

	$script='	function submitbutton(pressbutton) {
					var form = document.adminForm;
					if (pressbutton == "cancel") {
						submitform(pressbutton);
						return;
					}
					submitform(pressbutton);
				}';
		
	$document->addScriptDeclaration($script);
	
//add tooltips javascript
	$script = "window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });";
	$document->addScriptDeclaration($script);
	
?>
		<form action="index.php" method="post" name="adminForm">
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_STYLEDETAILS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
					<tr>
						<td class="key">
							<label>
								<?php echo JText::_('NB_NAME'); ?>
							</label>
						</td>
						<td>
							<strong>
								<?php echo JText::_($this->row->name); ?>
							</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label>
								<?php echo JText::_('NB_DEFAULT'); ?>
							</label>
						</td>
						<td>
							<?php echo $this->lists['defaultstyle']; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label>
								<?php echo JText::_('NB_DESCRIPTION'); ?>
							</label>
						</td>
						<td>
							<?php echo JText::_($this->row->description); ?>
						</td>											
					</tr>										
				</table>
				</fieldset>
			</div>
			<div class="clr"></div>				
			<input type="hidden" name="file_name" value="<?php echo $this->row->file_name; ?>" />
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="controller" value="style" />
			<input type="hidden" name="task" value="" />
		</form>

		<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));
?>
