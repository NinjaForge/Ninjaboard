<?php defined('_JEXEC') or die('Restricted access'); ?>

	<div class="nbWelcome"><?php
		echo JText::_('NB_WELCOME').JText::_('NB_COMMA').' ', $this->isUser ? $this->ninjaboardUser->get('name') : JText::_('NB_GUEST');
	?></div>
	<div class="nbClr"></div>
