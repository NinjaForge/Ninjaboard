<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 

if ($this->showPagination) : ?>
	<div class="jbPagination" align="right">
		<div class="jbPages jbCounter">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</div>
		<div class="jbPages jbCounts">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	</div><?php
endif; ?>