<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="nbSeparator nbClr"></div>
<div id="nbBoardFooter">
<?php 
if (NinjaboardHelper::isUserLoggedIn()) :
	if ($this->footerShowMyProfile) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=editprofile&Itemid='.$this->Itemid); ?>"><?php echo JText::_('NB_MYPROFILE'); ?></a><?php 
	endif;
	if ($this->footerShowLogout) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&task=ninjaboardlogout&Itemid='.$this->Itemid); ?>" class="jbPaddingLeft5"><?php echo JText::_('NB_LOGOUT'); ?></a><?php 
	endif;
else :
	if ($this->footerShowLogin) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=login&Itemid='.$this->Itemid); ?>"><?php echo JText::_('NB_LOGIN'); ?></a><?php
	endif;
	if ($this->footerShowRegister && $this->allowUserRegistration) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=register&Itemid='.$this->Itemid); ?>" class="jbPaddingLeft5"><?php echo JText::_('NB_REGISTER'); ?></a><?php
	endif;
endif;
if ($this->footerShowSearch) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=search&Itemid='.$this->Itemid); ?>" class="jbPaddingLeft5"><?php echo JText::_('NB_SEARCH'); ?></a><?php
	endif;
	if ($this->footerShowLatestPosts) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=latestposts&Itemid='.$this->Itemid); ?>" class="jbPaddingLeft5"><?php echo JText::_('NB_LATESTPOSTS'); ?></a><?php
	endif;
	if ($this->footerShowUserList) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=userlist&Itemid='.$this->Itemid); ?>" class="jbPaddingLeft5"><?php echo JText::_('NB_USERLIST'); ?></a><?php
	endif;
	if ($this->footerShowTerms) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_ninjaboard&view=terms&Itemid='.$this->Itemid); ?>" class="jbPaddingLeft5"><?php echo JText::_('NB_TERMS'); ?></a><?php
endif; ?>
</div>
