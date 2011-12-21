<?php defined('_JEXEC') or die('Restricted access');

if ($this->params->get('show_page_title')) : ?>
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>">
		<?php echo $this->params->get('page_title'); ?>
	</div>
<?php endif; ?>
<div id="nbWrapper">
<?php 
	echo $this->loadTemplate('header');
	echo $this->loadTemplate('message');
	echo $this->loadTemplate('board');

	if ($this->showBoxLatestItems) echo $this->loadTemplate('latestitemsbox');

	if ($this->showBoxStatistic && $this->showBoxWhosOnline) {
		 echo $this->loadTemplate('onlinestats');
	} else {
		if ($this->showBoxStatistic)
			echo $this->loadTemplate('boardstatistic');
		if ($this->showBoxWhosOnline)
			echo $this->loadTemplate('whosonlinebox');
	}
	if ($this->showBoxLegend)      echo $this->loadTemplate('boardfooter');
	if ($this->showBoxFooter)      echo $this->loadTemplate('footer');
?>
</div>
