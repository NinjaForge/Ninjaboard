<?php defined('_JEXEC') or die('Restricted access');

	if (! $this->loadbar)
		$this->loadbar = 'posts';
?>

	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>

	<?php echo $this->loadTemplate('pagination'); ?>
	<?php
		if ($this->total > 0) {
			$limitend = $this->pagination->limitstart + $this->pagination->limit;
			//for ($z = $this->pagination->limitstart; $z < $limitend; $z++) {
			for ($z = 0; $z < $this->posts->getPostCount(); $z++) {
				//if ($z < $this->total) {
					$this->post =& $this->posts->getPost($z);
					echo 
						"\t", '<div class="nbPostsWrapper" id="', $this->post->pid, '">',
						"\n\t\t", $this->loadTemplate('post'), "\n\t", '</div>', "\n";
				//}
			}
		}
	?>
	<?php echo $this->loadTemplate('pagination'); ?>

	<?php if ($this->loadbar != 'review') : ?>
	<div class="nbSeparator nbClr"></div>
	<div class="nbCategoryFooter">
	<?php
		if ($this->buttonNewPost->href != '') {
			echo 
				'<a class="nb-buttons buttonPostReply" href="', $this->buttonNewPost->href, '">',
					'<span class="buttonPostReply">', $this->buttonNewPost->title,
				'</span></a>';
		}
		echo $this->loadTemplate('searchbox'); # ToDo: Replace with jumpbox.
		# echo $this->loadTemplate('jumpbox');
	?>
	</div>
	<?php endif; ?>
	<div class="nbSeparator nbClr"></div>
	<br/>
	<br/>
