<?php defined('_JEXEC') or die('Restricted access'); ?>

				<div class="nbForumsWrapper">
					<div class="nbPostsText"><?php
						if ($this->forum->posts) {
							$_subject	= strlen($this->forum->subject_last_post) > 23
										? substr($this->forum->subject_last_post, 0, 20).'...'
										: $this->forum->subject_last_post;
							echo '<a href="', $this->forum->lastPostLink, '" title="', JTEXT::sprintf('NB_GOTOLATESTPOST', $this->forum->subject_last_post), '">', $_subject, '</a>';
							echo JText::_('NB_ON'), ' ', $this->forum->date_post;

							echo '<br/>', JText::_('NB_BY');
							if ($this->forum->authorLink)
								echo ' <a class="nbAuthor" href="', $this->forum->authorLink, '">', $this->forum->author, '</a>';
							else
								echo ' <span class="nbAuthor">', $this->forum->author, '</span>';
						}
						else
							echo '<div>', JText::_('NB_NOPOSTS'), '</div>';
					?></div>
					<div class="nbTopicsText"><span><?php echo $this->forum->topics; ?></span></div>
					<div class="nbForumsText">
						<img class="nbForumIcon" src="<?php echo $this->forum->forumIcon->fileName; ?>" alt="<?php echo $this->forum->forumIcon->title; ?>" />
						<a href="<?php echo $this->forum->forum_link; ?>"><span><?php echo $this->forum->name; ?></span><?php echo $this->forum->description; ?></a>
					</div>
				</div>
