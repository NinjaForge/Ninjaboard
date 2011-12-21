<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

	//I removed this because there are no preferences parameters at this point
	//JToolBarHelper::preferences('com_ninjaboard','600');

	//include our helper file to create the footer and buttons
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'HTML_ninjahelper_admin.php');
	
	//set our page header
	JToolBarHelper::title( JText::_( 'Dashboard' ), 'ninjahdr' );
		
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
	
//add the js for our tabs
	$tabJsFile = JURI::base(true).'/components/com_ninjaboard/js/ninja.js';
	$document->addScript($tabJsFile);

//Set up the pane for our control panel in case we ever add multiplepages of information
	jimport('joomla.html.pane');	
	$pane   =& JPane::getInstance('sliders');
	
?>

	<div class="menu">
        <ul>
          <li onclick="easytabs('1', '1');" onfocus="easytabs('1', '1');" onmouseover="return false;"  title="" id="tablink1"><span id="info" class="tabtxt"><?php echo JText::_('General Information');  ?></span></li>
          <li onclick="easytabs('1', '2');" onFocus="easytabs('1', '2');" onmouseover="return false;"  title="" id="tablink2"><span id="inst" class="tabtxt"><?php echo JText::_('Instructions');  ?></span></li>
          <li onclick="easytabs('1', '3');" onFocus="easytabs('1', '3');" onmouseover="return false;"  title="" id="tablink3"><span id="change" class="tabtxt"><?php echo JText::_('Changelog and Version Information');  ?></span></li>
        </ul>
      </div>
      <div class="nfbody">
		<div id="tabcontent1" class="njtab">
          <h1><?php echo JText::_('Component Real Name');  ?></h1>
          <div class="inner">
			  <div id="buttonpanel">
			  
			  <?php echo HTML_ninjahelper_admin::showButs($this->butInfo);?> 
			  
			  <?php //added by Dan from old JooBB control panel
					$pane->startPane("content-pane");
					$pane->startPanel(JText::_('NB_QUICKOVERVIEW'), "detail-page" );
				?>
					<table class="adminlist" cellspacing="1">
					<thead>
						<tr>
							<th colspan="2" nowrap="nowrap" width="100%">
								<?php echo JText::_('NB_BOARDSTATISTIC'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$k = 0;
					for ( $i=0, $n=count( $this->rows ); $i < $n; $i++ ) {
						$row 	=& $this->rows[$i];
						?>
						<tr>
							<td width="50%">
								<?php echo $row->description; ?>
							</td>																																																							
							<td width="50%">
								<?php echo $row->value; ?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
					?>																
					</tbody>					
					</table>
				<?php	
					$pane->endPanel();
					$pane->endPane();
					
					//end of JooBB control panel
				?>				
			  
				         
			  </div>
			  <div id="infopanel">
				  <h2><?php echo JText::_('Component Catch Phrase');?></h2>
				  <img src="<?php echo JURI::base(true).'/components/com_ninjaboard/images/box.png';?>" class="logoimg" alt="<?php echo JText::_('Component Real Name');?>"><br/>
				  <p><b><?php echo JText::_('Create By Text');?></b> <?php echo JText::_('Create By Value');?></p>
				  <p><b><?php echo JText::_('Code License Text');?></b> <?php echo JText::_('Code License Value');?></p>
				  <p><b><?php echo JText::_('CSS License Text');?></b> <?php echo JText::_('CSS License Value');?></p>
				  <p><b><?php echo JText::_('Copyright Text');?></b> <?php echo JText::_('Copyright Value');?></p>
				  <p><b><?php echo JText::_('Support Link Text');?></b> <?php echo JText::_('Support Link Value');?></p>
				  <p><b><?php echo JText::_('Rate Extn Text');?></b> <?php echo JText::_('Rate Extn Value');?></p>
				  <p><b><?php echo JText::_('Description Text');?></b> <?php echo JText::_('Description Value');?></p>
			  </div>
          </div>
      </div>
      
      <div id="tabcontent2" class="njtab">
          <h1><?php echo JText::_('Usage Title');?></h1> 
          <h3><?php echo JText::_('Usage Basic Text');?></h3>    
          <div class="inner"><?php echo JText::_('Usage Basic Value');?></div>
          <h3><?php echo JText::_('Usage Adv Text');?></h3>     
          <div class="inner"><?php echo JText::_('Usage Adv Value');?></div>
          <h3><?php echo JText::_('Usage TS Text');?></h3>     
          <div class="inner"><?php echo JText::_('Usage TS Value');?></div>          
      </div>
      
      <div id="tabcontent3" class="njtab">
          <h1><?php echo JText::_('Changelog Title');?></h1>
          <div class="inner"><?php echo JText::_('Changelog Value');?></div>
      </div>

<?php

	
	HTML_ninjahelper_admin::showfooter(JText::_('Component Real Name'),JText::_('Component Footer Buttons'));

?>
