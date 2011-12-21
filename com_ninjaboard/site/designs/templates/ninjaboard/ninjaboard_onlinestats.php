<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="nbSeparator nbClr"></div>
<div id="nbOnlineStats">
	<h1><?php echo JText::_('NB_BOARDSTATISTICS'); ?></h1>
	<dl>
		<dt><?php echo '<a href="', $this->whosOnlineLink, '" title="', JText::_('NB_SHOWUSERLIST'),'">', JText::_('NB_WHOSONLINE'), '</a>'; ?></dt><dd>&nbsp;</dd>
		<dt><?php echo JText::_('NB_MEMBERSONLINE'); ?>:</dt><dd><?php echo $this->membersOnline; ?></dd>
		<dt><?php echo JText::_('NB_GUESTSONLINE');  ?>:</dt><dd><?php echo $this->guestsOnline; ?>
		<dt><br /></dt>
		<dd><br /></dd>
		<dt><?php echo JText::_('NB_LATESTMEMBER'); ?>:</dt><dd><?php echo '<a href="', $this->latestMember->userLink, '">',$this->latestMember->get('name'), '</a>'; ?></dd>
		<dt><?php echo JText::_('NB_TOTALMEMBERS'); ?>:</dt><dd><?php echo $this->totalMembers; ?></dd>
		<dt><?php echo JText::_('NB_TOTALTOPICS');  ?>:</dt><dd><?php echo $this->totalTopics; ?></dd>
		<dt><?php echo JText::_('NB_TOTALPOSTS');   ?>:</dt><dd><?php echo $this->totalPosts; ?></dd>
	</dl>
	<p><?php 
		$onlineUsersCount = count($this->onlineUsers);
		if ($onlineUsersCount < 1)
			echo '<span>', JText::_('NB_NOUSERONLINE'), '.</span>';
		else {
			echo '<span>', JText::_('NB_CURRENTONLINE'), ':</span>';
			for ($i = 0; $i < $onlineUsersCount; $i++) {
				$onlineUser =& $this->getOnlineUser($i); ?>
				<a href="<?php echo $onlineUser->userLink; ?>"><?php echo $onlineUser->name; ?></a><?php
				echo ($i+1 < $onlineUsersCount) ? ', ' : '';
			} 
		}
	?></p>
</div>
<div class="nbClr"></div>
