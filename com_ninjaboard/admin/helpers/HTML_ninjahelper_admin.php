<?php
defined ('_JEXEC') or die('Restricted access');

class HTML_ninjahelper_admin {
    
    /*
    * @showFooter
    * Display the footer, and close the open outer divs for our component pages
    * 
    * @extnName - name of the component
    * @buttonList - a comma seperated list of which optional buttons to show
    */ 
   function showFooter($extnName, $buttonList) { 
   
   $option = JRequest::getCmd('option');
   
   //we can use slashes instead of DS here because this url is for the browser's sake not PHPs
   $extnDir = JURI::base().'components/'.$option;
   
   ?>
     
  <div class="nfFooter">
  
  <div class="nfFoot"><?php echo $extnName; ?> - Copyright &copy; 2008 - <a href="http://ninjaforge.com" title="<?php echo JText::_('Visit Ninja Forge home of the Joomla Ninjas'); ?>">Ninja Forge</a></div>

  <a href="http://ninjaforge.com" target="_self"><img  class="buttons" src="<?php echo $extnDir; ?>/images/ninjaforge.png" alt="<?php echo JText::_('Ninja Forge Icon'); ?>" title="<?php echo JText::_('Visit Ninja Forge home of the Joomla Ninjas'); ?>"/></a>
  
  <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/us/"><img class="buttons" alt="<?php echo JText::_('Creative Commons bynCsa License Icon'); ?>" title="<?php echo JText::_('Extension information and instructions pages are licensed Creative Commons byncsa'); ?>" src="<?php echo $extnDir; ?>/images/byncsa.png" /></a>
  
  <?php 
  
  //Explode our button list parameter into an array so we can check the values inside it.
  $buttonArray = explode(',',$buttonList);

  //Count the array so we know how many times to loop
	$loopCount = count($buttonArray);
		 	
	for ($i=0; $i<$loopCount; $i++) {
		switch (trim($buttonArray[$i])) {
			case 'css':
			    ?>
			     
			    	<img  class="buttons" src="<?php echo $extnDir; ?>/images/validation_css.png" alt="<?php echo JText::_('This Component Contains Only Valid CSS Code'); ?>" title="<?php echo JText::_('This Component Contains Only Valid CSS Code'); ?>"/>
				    
	          <?php break;
			  case 'xhtml':
				     ?>
				     
				    <img  class="buttons" src="<?php echo $extnDir; ?>/images/validation_xhtml.png" alt="<?php echo JText::_('This Component Contains Only Valid XHTML Transitional Code'); ?>" title="<?php echo JText::_('This Component Contains Only Valid XHTML Transitional Code'); ?>"/>
				    
	          <?php break;		
			  case 'lgpl':
				    ?>
	          
	          <a href="http://creativecommons.org/licenses/LGPL/2.1/"><img class="buttons" alt="<?php echo JText::_('CC GNU LGPL License Icon'); ?>" title="<?php echo JText::_('This component is licensed GNU LGPL'); ?>" src="<?php echo $extnDir; ?>/images/gnulgpl.png" /></a>  
	
	          <?php break;
	       	  case 'gpl':
				    ?>
	          
	          <a href="http://creativecommons.org/licenses/GPL/2.0/"><img  class="buttons" alt="<?php echo JText::_('CC GNU GPL Icon'); ?>" title="<?php echo JText::_('This component is licensed GNU GPL'); ?>" src="<?php echo $extnDir; ?>/images/gnugpl.png" /></a>  
	
	          <?php break; 
		}
	}
	
	
  ?>
  
  <div class="nfFoot"><?php echo JText::_('Ninja Forge staff use and recommend the following software'); ?></div>
  
  <a href="http://www.spreadfirefox.com/node&amp;id=221592&amp;t=218" target="_self"><img class="buttons" src="<?php echo $extnDir; ?>/images/firefox2.png" alt="<?php echo JText::_('Get Firefox for a better internet experience'); ?>" title="<?php echo JText::_('Get Firefox for a better internet experience'); ?>"/></a>
  
  <a href="http://www.getfirebug.com/?link=3" target="_self" title="<?php echo JText::_('Firebug is a free and essential tool for all website developers'); ?>"><img class="buttons" src="<?php echo $extnDir; ?>/images/firebug.png" alt="<?php echo JText::_('Firebug is a free and essential tool for all website developers'); ?>" /></a>
  
  
    </div>

  <?php
   
   }//function showFooter
    
   function showButs($butInfo) {     
   
		$option = JRequest::getCmd('option');
   
		//we can use slashes instead of DS here because this url is for the browser's sake not PHPs
		$extnDir = JURI::base().'components/'.$option; 
   
		//process the array of button info into buttons
    
		for ($i=0; $i<count($butInfo); $i++) {
		
			?>
			<div class="infoPgBut">
				<a class="infoPgButA" href="index.php?option=<?php echo $option.$butInfo[$i][0];?>">
					<img class="infoPgButImg" src="<?php echo $extnDir; ?>/images/<?php echo $butInfo[$i][1];?>"/>
					<span class="infoPgButSpan"><?php echo $butInfo[$i][2];?></span>
				</a>
			</div>
			<?php
		
		}
    
   } 
    
}
?>
