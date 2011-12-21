<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<?php echo $this->loadTemplate('pagination'); ?>
<div class="jbBoxTopLeft"><div class="jbBoxTopRight"><div class="jbBoxTop">
	<div class="jbWidth20 jbTextHeader">
		<?php echo JText::_('NB_WHOSONLINE'); ?>
	</div>
	<div class="jbWidth20 jbTextHeader" style="text-align: center;">
		<?php echo JText::_('NB_TIME'); ?>
	</div>
	<div class="jbWidth60 jbTextHeader">
		<?php echo JText::_('NB_ACTION'); ?>
	</div>
</div></div></div>
<div class="jbBoxOuter"><div class="jbBoxInner">	
<?php
	$limitend = $this->pagination->limitstart + $this->pagination->limit;
	for ($z = $this->pagination->limitstart; $z < $limitend; $z ++) :
		if ($z < $this->total) :
			$this->onlineUser =& $this->getOnlineUser($z);
			$even = (($z % 2) == 0) ? '1' : '0';
			echo '<div even="'. $even .'">';
			echo $this->loadTemplate('whosonlineuser');
			echo '</div>';
		endif;
	endfor;
?>
</div></div>
<div class="jbBoxBottomLeft"><div class="jbBoxBottomRight"><div class="jbBoxBottom"></div></div></div>
<div class="jbMarginBottom10"></div>
<?php echo $this->loadTemplate('pagination'); ?>