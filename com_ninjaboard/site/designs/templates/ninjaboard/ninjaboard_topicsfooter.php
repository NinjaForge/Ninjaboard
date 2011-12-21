<?php defined('_JEXEC') or die('Restricted access'); ?>

				<div class="nbSeparator nbClr"></div>
				<div id="nbForumLegend">
					<h1><?php echo JText::_('NB_FORUMLEGEND'); ?></h1>
					<div>
						<?php 
							$icons = $this->ninjaboardIconSet->getIconsByGroup('iconPost');
							$k = count($icons);
							for ($i = 0; $i < $k; $i++) :
						?>
						<span><img src="<?php echo $icons[$i]->fileName; ?>" alt="<?php echo $icons[$i]->title; ?>" /> <?php echo $icons[$i]->title; ?></span>
						<?php 
							endfor;
						?>
					</div>
				</div>
				<div class="nbClr"></div>
				<br />
				<br />
