<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<h3><?php echo JText::_('NB_DEARUSER'); ?></h3>
<br />
<p style="font-size: 15px;">
	<?php echo JText::_('NB_INFORESETFAILURE'); ?>
</p>
<ul>
	<li><a href="<?php echo $this->boardIndexLink; ?>"><?php echo JText::_('NB_RETURNBOARDINDEX'); ?></a></li>
	<li><a href="<?php echo $this->requestLoginLink; ?>"><?php echo JText::_('NB_REQUESTLOGIN'); ?></a></li>
</ul>