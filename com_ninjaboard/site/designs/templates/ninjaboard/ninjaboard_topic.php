<?php defined('_JEXEC') or die('Restricted access'); ?>

<!--
<img src="<?php echo $this->styleSheetPathLive.DL.'images'.DL.'latestPost.png'; ?>" alt="<?php echo JTEXT::sprintf('NB_GOTOLATESTPOST', $this->topic->subject_last_post); ?>" title="<?php echo JTEXT::sprintf('NB_GOTOLATESTPOST', $this->topic->subject_last_post); ?>" />
-->
					<div class="nbTopicsWrapper">
						<div class="nbPostsText"><?php
							if ($this->topic->poster && $this->topic->id_first_post != $this->topic->id_last_post) {
								$_subject	= strlen($this->topic->subject_last_post) > 23
											? substr($this->topic->subject_last_post, 0, 20).'...'
											: $this->topic->subject_last_post;
								echo '<a href="', $this->topic->lastPostLink, '" title="', JTEXT::sprintf('NB_GOTOLATESTPOST', $this->topic->subject_last_post), '">', $_subject, '</a>';
								echo JText::_('NB_ON'), ' ', $this->topic->date_last_post;

								echo '<br/>', JText::_('NB_BY');
								if ($this->topic->posterLink)
									echo ' <a class="nbAuthor" href="', $this->topic->posterLink, '">', $this->topic->poster, '</a>';
								else
									echo ' <span class="nbAuthor">', $this->topic->poster, '</span>';
							}
							else
								echo '<div>', JText::_('NB_NOPOSTS'), '</div>';
						#echo $this->topic->views;
						?></div>
						<div class="nbTopicsText"><span><?php echo $this->topic->replies; ?></span></div>
						<div class="nbForumsText">
							<img src="<?php echo $this->topic->postIcon->fileName; ?>" alt="<?php echo $this->topic->postIcon->title; ?>" />
							<a href="<?php echo $this->topic->href; ?>"><span><?php
								$k = count($this->topic->topicInfoIcons);
								for ($i = 0; $i < $k; $i++) :
								?><img src="<?php echo $this->topic->topicInfoIcons[$i]->fileName; ?>" alt="<?php echo $this->topic->topicInfoIcons[$i]->title; ?>" title="<?php echo $this->topic->topicInfoIcons[$i]->title; ?>" /> <?php
								endfor;
								echo ' ', strlen($this->topic->subject) > 73 ? substr($this->topic->subject, 0, 70).'...' : $this->topic->subject;
							?></span><?php 
								echo '<em class="nbAuthor">', JText::_('NB_BY'), ' ', $this->topic->author, '</em>';
							?></a>
						</div>
					</div>

