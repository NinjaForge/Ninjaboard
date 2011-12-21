<?php
// no direct access
defined('_JEXEC') or die('Restricted access'); 

if ($this->showPagination) : ?>
<div class="jbMarginBottom10"><?php
	echo $this->loadTemplate('pagination'); ?>
	<br clear="all" />
</div><?php
endif; ?>
<div class="jbBoxTopLeft"><div class="jbBoxTopRight"><div class="jbBoxTop">
	<div class="jbWidth30 jbTextHeader"><?php echo JText::_('NB_NICKNAME'); ?></div>
	<div class="jbWidth20 jbTextHeader" style="text-align: center;"><?php echo JText::_('NB_USERMAINROLE'); ?></div>
	<div class="jbWidth10 jbTextHeader" style="text-align: center;"><?php echo JText::_('NB_POSTS'); ?></div>
	<div class="jbWidth30 jbTextHeader"><?php echo JText::_('NB_JOINEDBOARD'); ?></div>
</div></div></div>
<div class="jbBoxOuter"><div class="jbBoxInner"><?php
if ($this->total > 0) :
	$ninjaboardUsersCount = count($this->ninjaboardUsers);
	for ($z = 0; $z < $ninjaboardUsersCount; $z++) :
		$this->ninjaboardUser =& $this->getNinjaboardUser($z);
		$even = (($z % 2) == 0) ? '1' : '0';
		echo '<div even="'. $even .'">';
		echo $this->loadTemplate('user');
		echo '</div>';
	endfor;
endif; ?>
</div></div>
<div class="jbBoxBottomLeft"><div class="jbBoxBottomRight"><div class="jbBoxBottom"></div></div></div>
<div class="jbMarginBottom10"></div><?php
if ($this->showPagination) : ?>
<div class="jbMarginBottom10"><?php
	echo $this->loadTemplate('pagination'); ?>
	<br clear="all" />
</div><?php
endif; ?>