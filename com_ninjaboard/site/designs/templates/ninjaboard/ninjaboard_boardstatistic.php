<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="nbSeparator nbClr"></div>
<div id="nbBoardStats">
	<h1><?php echo JText::_('NB_BOARDSTATISTICS'); ?></h1>
	<dl>
		<dt><?php echo JText::_('NB_LATESTMEMBER'), ': '; ?></dt>
		<dd><?php echo '<a href="', $this->latestMember->userLink, '">',$this->latestMember->get('name'), '</a>'; ?></dd>
		<dt><?php echo JText::_('NB_TOTALMEMBERS'), ': ' ?></dt>
		<dd><?php echo $this->totalMembers; ?></dd>
		<dt><?php echo JText::_('NB_TOTALTOPICS'), ': '; ?></dt>
		<dd><?php echo $this->totalTopics; ?></dd>
		<dt><?php echo JText::_('NB_TOTALPOSTS'), ': '; ?></dt>
		<dd><?php echo $this->totalPosts; ?></dd>
	</dl>
</div>
<div class="nbClr"></div>
