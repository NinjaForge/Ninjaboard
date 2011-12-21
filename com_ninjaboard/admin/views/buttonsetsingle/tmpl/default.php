<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

	//I removed this because there are no preferences parameters at this point
	//JToolBarHelper::preferences('com_ninjaboard','600');

	//include our helper file to create the footer and buttons
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'HTML_ninjahelper_admin.php');
	
//set our page header and menu
	$cid = JRequest::getVar('cid', array() );
	$text = intval($cid[0] != '') ? JText::_('NB_EDIT') : JText::_('NB_ADD');

	JToolBarHelper::title(JText::_('NB_BUTTONSETMANAGER') .' - <span>'. $text.'</span>', 'ninjahdr');	
	JToolBarHelper::cancel( 'cancel', JText::_( 'Close' ) );
		
//	No save and apply yet. What would you do with it?
//		JToolBarHelper::custom('ninjaboard_buttonset_apply', 'apply.png', 'apply_f2.png', 'NB_APPLY', false);				
//		JToolBarHelper::custom('ninjaboard_buttonset_save', 'save.png', 'save_f2.png', 'NB_SAVE', false);	
		
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


		$document->addStyleSheet('#dummy {pipo:popo;}', 'text/css', null, array());
		  
	}
	
	
//Add our validation script

	$script='	function submitbutton(pressbutton) {
						var form = document.adminForm;
						if (pressbutton == "ninjaboard_buttonset_cancel") {
							submitform(pressbutton);
							return;
						}
						var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
			
						// do field validation
						if (trim(form.name.value) == "") {
							alert("'.JText::sprintf('NB_MSGFIELDREQUIRED', JText::_('NB_NAME'), JText::_('NB_BUTTONSET')).'");
						} else {
							submitform(pressbutton);
						}
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
						<?php echo JText::_('NB_BUTTONSETDETAILS'); ?>
					</legend>
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<label for="name">
									<?php echo JText::_('NB_NAME'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="name" id="name" class="inputbox" size="40" value="<?php echo $this->ninjaboardButtonSet->name; ?>" maxlength="50" readonly="true" />
							</td>						
						</tr>					
					</table>
				</fieldset>
			</div>
			<div class="col width-50">
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('NB_BUTTONSET'); ?>
					</legend>
					<table class="admintable" cellspacing="1"><?php
					$k = 0;
					for ($i=0, $n=count($this->ninjaboardButtonSet->buttons); $i < $n; $i++) {
						$button =& $this->ninjaboardButtonSet->buttons[$i];
					?>

					<tr>
						<td class="key"><?php echo JText::_($button->title); ?></td>
						<td><?php echo '<div class="nb-buttons ', $button->function , '"><span class="', $button->function , '"></span>'.$button->title.'</div>';?></td>
					</tr><?php
					}
					?>						
					</table>
				</fieldset>
			</div>
			<div class="clr"></div>				
			<input type="hidden" name="id" value="<?php echo $this->ninjaboardButtonSet->xmlFile; ?>" />
			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="controller" value="buttonset" />
			<input type="hidden" name="task" value="" />
		</form>
		<?php

	//add our Ninja footer in
		HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));
?>
