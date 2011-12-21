<?php defined('_JEXEC') or die('Restricted access');

if ($this->enableFilter) : ?>
<div class="jbBoxTopLeft"><div class="jbBoxTopRight"><div class="jbBoxTop">
	<div class="jbTextHeader"><?php echo JText::_('NB_LATESTPOSTS'); ?></div>
</div></div></div>
<div class="jbBoxOuter"><div class="jbBoxInner"><center><?php
	echo JText::_('NB_TOTAL') .' '. $this->total; ?><br /><?php
	$latestPostLinksCount = count($this->latestPostLinks);
	for ($i = 0; $i < $latestPostLinksCount; $i++) : ?>
		<a href="<?php echo $this->latestPostLinks[$i]->href; ?>" style="margin-left: 5px;"><?php echo $this->latestPostLinks[$i]->text; ?></a><?php
	endfor; ?>
</center></div></div>
<div class="jbBoxBottomLeft"><div class="jbBoxBottomRight"><div class="jbBoxBottom"></div></div></div><?php
endif; ?>
<div class="jbMarginBottom10"></div><?php
if ($this->showPagination) : ?>
<div class="jbMarginBottom10"><?php
	echo $this->loadTemplate('pagination'); ?>
	<br clear="all" />
</div><?php
endif; ?>
<div class="jbBoxTopLeft"><div class="jbBoxTopRight"><div class="jbBoxTop">
	<div class="jbTextHeader"><?php echo JText::_('NB_LATESTPOSTS'); ?></div>
</div></div></div>
<div class="ninjaboard_box_outer"><div class="jbBoxInner"><?php
if ($this->total > 0) :
	$limitend = $this->pagination->limitstart + $this->pagination->limit;
	for ($z = $this->pagination->limitstart; $z < $limitend; $z++) :
		if ($z < $this->total) :
			$this->post =& $this->posts->getPost($z);
			$even = (($z % 2) == 0) ? '1' : '0';
			echo '<div even="'. $even .'">';
			echo $this->loadTemplate('post');
			echo '</div>';
		endif;
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
