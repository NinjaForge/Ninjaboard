<?php defined('_JEXEC') or die('Restricted access');
/**
 * !!! WTF :: BOAHR EY !!!
 *
 * TODO: We are in the forum (topics) view and have no public category name !?
 */
	#print '<pre>';
	#print_r($this);
	#print '</pre>';

	#die($this->_models[forum]->_forum->new_posts_time);

	// Load jeeded JavaScripts.
	$this->document->addScript($this->templatePathLive.'/js/jquery.js');
	$this->document->addScript($this->templatePathLive.'/js/ninjaboard.core.js');
	$this->document->addScript($this->templatePathLive.'/js/ninjaboard.jquery.js');
	#$this->document->addScript($this->templatePathLive.'/js/jquery.easing.js');
	#$this->document->addScript($this->templatePathLive.'/js/jquery.slidingBar.js');

	#$javaScript = '$j(document).ready(function(){ $j(\'.slidingBar\').slidingBar(); });';
	#$this->document->addScriptDeclaration($javaScript);

	// Set loadvar for category header switcher.
	$this->loadbar = 'topics';
?>

				<?php echo $this->loadTemplate('category'); ?>

				<div id="nbTopicsWrapper">

					<?php echo $this->loadTemplate('pagination'); ?>
					<?php
						$count = count($this->announcements);
						if ($count) :
					?>
					<div class="nbCategoryTitles">
<!--
						<div class="nbPostsHeader"><?php echo JText::_('NB_LASTPOSTS'); ?></div>
						<div class="nbTopicsHeader"><span><?php echo JText::_('NB_REPLIES'), '/', JText::_('NB_VIEWS'); ?></span></div>
						<div class="nbForumsHeader"><?php echo JText::_('NB_ANNOUNCEMENTS'); ?></div>
-->
						<div class="nbPostsHeader"><?php echo JText::_('NB_LASTPOST'); ?></div>
						<div class="nbTopicsHeader"><span><?php echo JText::_('NB_REPLIES'); ?></span></div>
						<div class="nbForumsHeader"><?php echo JText::_('NB_ANNOUNCEMENTS'); ?></div>
					</div>
					<div class="nbForumsWrapper">
					<?php
						echo $this->loadTemplate('pagination');

						//$this->topicType = 'announcements';
							for ($i = 0; $i < $count; $i++) {
								$this->topic =& $this->getAnnouncement($i);
								echo $this->loadTemplate('topic');
							}
					?>
					</div>
					<div class="nbSeparator nbClr"></div>
					<?php
						endif;
					?>

					<div class="nbCategoryTitles">
<!--
						<div class="nbPostsHeader"><?php echo JText::_('NB_LASTPOSTS'); ?></div>
						<div class="nbTopicsHeader"><span><?php echo JText::_('NB_REPLIES'), '/', JText::_('NB_VIEWS'); ?></span></div>
						<div class="nbForumsHeader"><?php echo JText::_('NB_ANNOUNCEMENTS'); ?></div>
-->
						<div class="nbPostsHeader"><?php echo JText::_('NB_LASTPOST'); ?></div>
						<div class="nbTopicsHeader"><span><?php echo JText::_('NB_REPLIES'); ?></span></div>
						<div class="nbForumsHeader"><?php echo JText::_('NB_TOPIC'); ?></div>
					</div>
					<?php

						//$this->topicType = 'topics';
						$count = count($this->topics);
						if ($count) {
							for ($i = 0; $i < $count; $i++) {
								$this->topic =& $this->getTopic($i);
								echo $this->loadTemplate('topic');
							}
						}

						echo $this->loadTemplate('pagination');
					?>
				</div>

				<div class="nbSeparator nbClr"></div>
				<div class="nbCategoryFooter">
				<?php
					if ($this->buttonNewTopic->href != '') {
						echo 
							'<a class="nb-buttons buttonNewTopic" href="', $this->buttonNewTopic->href, '">',
								'<span class="buttonNewTopic">', $this->buttonNewTopic->title,
							'</span></a>';
					}
					echo $this->loadTemplate('searchbox');
				?>
				</div>
				<?php if (!$this->showBoxFooter && !$this->showBoxLegend) : ?>
				<div class="nbSeparator nbClr"></div>
				<?php endif; ?>
				<?php # TODO: echo $this->loadTemplate('jumpbox'); ?>

