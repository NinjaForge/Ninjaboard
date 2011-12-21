<?php
// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="jbRow jbPadding10 jbBorderTop jbBorderBottom">
	<div class="jbCol jbWidth20"><?php
	if ($this->onlineUser->userLink) : ?>
		<a href="<?php echo $this->onlineUser->userLink; ?>"><?php echo $this->onlineUser->name; ?></a><?php
	else : ?>
		<span class="jbGuest"><?php echo $this->onlineUser->name; ?></span><?php
	endif; ?>
	</div>
	<div class="jbCol jbWidth20 jbBorderLeft" style="text-align: center;">
		<?php echo $this->onlineUser->actionTime; ?>
	</div>
	<div class="jbCol jbWidth55 jbBorderLeft jbPaddingLeft5">
		<a href="<?php echo $this->onlineUser->action_url; ?>">
			<?php echo $this->onlineUser->current_action; ?>
		</a>
	</div>
	<br clear="all" />
</div>