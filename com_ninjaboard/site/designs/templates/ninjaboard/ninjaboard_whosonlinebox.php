<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="nbSeparator nbClr"></div>
<div id="nbOnlineUsers">
	<h1><?php echo JText::_('NB_ONLINESTATS'); ?></h1>
	<p><?php
		echo JText::_('NB_MEMBERSONLINE'), ': ', $this->membersOnline, ', ';
        echo JText::_('NB_GUESTSONLINE'), ': ', $this->guestsOnline, '<br />';

		echo '<span>', JText::_('NB_CURRENTONLINE'), ':</span>';
		$onlineUsersCount = count($this->onlineUsers);
		if ($onlineUsersCount > 0) :
			for ($i = 0; $i < $onlineUsersCount; $i++) :
				$onlineUser =& $this->getOnlineUser($i); ?>
				<a href="<?php echo $onlineUser->userLink; ?>"><?php echo $onlineUser->name; ?></a><?php
				echo ($i+1 < $onlineUsersCount) ? ', ' : '';
			endfor; 
		endif;
	?></p>
</div>
