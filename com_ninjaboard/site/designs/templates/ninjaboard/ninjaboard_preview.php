<?php defined('_JEXEC') or die('Restricted access'); 

	$this->loadbar = 'preview';
?>

	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>

	<div class="nbPostsWrapper">
		<?php echo $this->loadTemplate('post'); ?>
	</div>
