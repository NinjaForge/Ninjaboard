<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<h3><?php echo JText::sprintf('NB_WELCOMEUSER', $this->ninjaboardUser->name); ?></h3>
<br />
<p style="font-size: 15px;">
	<?php echo JText::_('NB_INFOACCOUNTACTIVATIONUSER'); ?>
</p>
<ul>
	<li><a href="<?php echo $this->boardIndexLink; ?>"><?php echo JText::_('NB_RETURNBOARDINDEX'); ?></a></li>
</ul>