<?php
// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="jbRow jbPadding10 jbBorderTop jbBorderBottom">
	<div class="jbCol jbWidth30">
		<a href="<?php echo $this->ninjaboardUser->userLink; ?>">
			<?php echo $this->ninjaboardUser->name; ?>
		</a>
	</div>
	<div class="jbCol jbWidth20 jbBorderLeft" style="text-align: center;">
		<?php echo $this->ninjaboardUser->mainRole; ?>
	</div>
	<div class="jbCol jbWidth10 jbBorderLeft" style="text-align: center;">
		<?php echo $this->ninjaboardUser->posts; ?>
	</div>
	<div class="jbCol jbWidth30 jbBorderLeft jbPaddingLeft5">
		<?php echo $this->ninjaboardUser->registerDate; ?>
	</div>
	<br clear="all" />
</div>