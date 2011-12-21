<?php defined('_JEXEC') or die('Restricted access');
 
$this->isUser = NinjaboardHelper::isUserLoggedIn() ? TRUE : FALSE;

// What do we actually have !?
//print_r($this);
?>
	<div id="nbHeader">
		<?php if ($this->enableFeeds) echo $this->loadTemplate('boardfeed'); ?>
		<a id="nbBoardName" href="<?php echo JURI::base();?>index.php?option=<?php echo $option?>&amp;view=board&amp;Itemid=<?php echo JRequest::getInt('Itemid')?>"><?php echo $this->boardName; ?></a>
 
<?php if ($this->isUser) : ?>
		<span id="nbBoardNavi">
			<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=editprofile&Itemid='.$this->Itemid); ?>"><?php echo JText::_('NB_MYPROFILE'); ?></a>
			<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=userposts&id='.$this->ninjaboardUser->get('id').'&Itemid='.$this->Itemid); ?>"><?php echo JText::_('NB_MYPOSTS'); ?></a>
			<?php if ($this->footerShowLatestPosts) : ?>
			<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=latestposts&Itemid='.$this->Itemid); ?>"><?php echo JText::_('NB_LATESTPOSTS');
			?></a><?php endif; ?>
			<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&task=ninjaboardlogout&Itemid='.$this->Itemid); ?>"><?php echo JText::_('NB_LOGOUT'); ?></a>
		</span>
<?php else : ?>
		<span id="nbLoginMsg">
			<?php echo JText::_('NB_PLEASE'); ?>
			<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=login&Itemid='.$this->Itemid); ?>"><?php echo strtolower(JText::_('NB_LOGIN')); ?></a>
			<?php if ($this->allowUserRegistration) : echo ' '.JText::_('NB_OR'); ?>
			<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=register&Itemid='.$this->Itemid); ?>"><?php echo strtolower(JText::_('NB_REGISTER')); ?></a>.
			<?php endif; ?>
			<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=requestlogin&Itemid='.$this->Itemid); ?>"><?php echo JText::_('NB_FORGOTYOURLOGIN'); ?></a>
		</span>
 
<?php endif; ?>
 
	</div>
	<?php echo $this->loadTemplate('welcomebox'); ?>
	<?php echo $this->loadTemplate('breadcrumb'); ?>
