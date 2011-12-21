<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

	//I removed this because there are no preferences parameters at this point
	//JToolBarHelper::preferences('com_ninjaboard','600');

	//include our helper file to create the footer and buttons
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'HTML_ninjahelper_admin.php');
	
	$cid = JRequest::getVar('cid', array(0) );
	$text = intval($cid[0]) ? JText::_('NB_EDIT') : JText::_('NB_ADD');
	
//set our page header and menu
	JToolBarHelper::title(JText::_('NB_CATEGORYMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');
	JToolBarHelper::apply();
    JToolBarHelper::save();	
	JToolBarHelper::cancel();
			
//Include our stylesheet for our Ninja Component Styling
	$document =& JFactory::getDocument();
	$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin.css';
	$document->addStyleSheet($cssFile, 'text/css', null, array());
	
	$document->addStyleSheet(NB_ADMINCSS_LIVE.DL.'icon.css');

//add ie6 css if needed
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (strstr($ua, "MSIE")&& strstr($ua, "6")) {
		  
		$cssFile = JURI::base(true).'/components/com_ninjaboard/css/HTML_ninjahelper_admin_ie6.css';
		$document->addStyleSheet($cssFile, 'text/css', null, array());		  
	}
		
//Add our validation script
	$script='function submitbutton(pressbutton) {
				var form = document.adminForm;
				
				//skip validation for cancel button
				if (pressbutton == "cancel") {
					submitform(pressbutton);
					return;
				}

				// do field validation (skipped for cancel button above)
				if (trim(form.name.value) == "") {
					alert("'.JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_CATEGORY')).'");
				} else {
					submitform(pressbutton);
				}
			}';
		
	$document->addScriptDeclaration($script);
	
//add tooltips javascript
	$script = "window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });";
	$document->addScriptDeclaration($script);
	
?>		
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
								<input type="text" name="name" id="name" class="inputbox" size="50" value="<?php echo $this->row->name; ?>" maxlength="255" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="published" class="hasTip" title="<?php echo JText::_('NB_PUBLISHED') .'::'. JText::_('NB_CATEGORYPUBLISHEDDESC'); ?>">
									<?php echo JText::_('NB_PUBLISHED'); ?>
								</label>
							</td>
							<td>
								<?php echo $this->published; ?>			
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div class="clr"></div>				
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="controller" value="category" />
			<input type="hidden" name="task" value="" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	<?php
	
	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));
?>
