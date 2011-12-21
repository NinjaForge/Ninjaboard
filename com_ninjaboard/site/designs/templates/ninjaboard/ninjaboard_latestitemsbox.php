<?php defined('_JEXEC') or die('Restricted access'); $this->loadbar = 'latest'; ?>

<div class="nbCategoryWrapper slidingBar">
	<?php echo $this->loadTemplate('category'); ?>
	<div class="slidingBar-content">
		<div class="nbCategoryTitles">
			<div class="nbPostsHeader"><?php echo JText::_('NB_AUTHOR'); ?></div>
			<div class="nbTopicsHeader"><span><?php echo JText::_('NB_DATE'); ?></span></div>
			<div class="nbForumsHeader"><?php echo JText::_('NB_TOPIC'); ?></div>
		</div>
		<div id="nbLatestBlock">
		<?php
			$latestItemsCount = count($this->latestItems);
			for ($i=0; $i < $latestItemsCount; $i ++) :
				$item =& $this->getLatestItem($i);
		?>
			<div class="nbForumsWrapper">
				<div class="nbAuthorsText">
				<?php if ($item->authorLink) : ?>
					<a href="<?php echo $item->authorLink; ?>"><?php echo $item->author; ?></a>
				<?php else : ?>
					<span><?php echo $item->author; ?></span>
				<?php endif; ?>
				</div>
				<div class="nbDatesText">
					<span><?php echo preg_replace('/^(.+?)\s(\d{2}:\d{2})$/', '$1<br />$2', $item->date_post); ?></span>
				</div>
				<div class="nbForumsText">
					<img src="<?php echo $item->itemIcon->fileName; ?>" alt="<?php echo $item->itemIcon->title; ?>" />
					<a href="<?php echo $item->itemLink; ?>"><b><?php
						echo strlen($item->subject) > 83 ? substr($item->subject, 0, 80).'...' : $item->subject
					?></b><?php
						echo $item->category_name .' / '. $item->forum_name;
					?></a>
				</div>
			</div>
		<?php endfor; ?></div>
	</div>
</div>
