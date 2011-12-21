<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<h3><?php echo JText::_('NB_ACCOUNTACTIVATIONFAILED'); ?></h3>
<br />
<p style="font-size: 15px;">
	<?php echo JText::_('NB_INFOREASONSFORFAILURE'); ?>
</p>
<ul>
	<li><?php echo JText::_('NB_INFOFAILUREREASON01'); ?></li>
	<li><?php echo JText::_('NB_INFOFAILUREREASON02'); ?></li>
	<li><?php echo JText::_('NB_INFOFAILUREREASON03'); ?></li>
</ul>
<ul>
	<li><a href="<?php echo $this->boardIndexLink; ?>"><?php echo JText::_('NB_RETURNBOARDINDEX'); ?></a></li>
</ul>