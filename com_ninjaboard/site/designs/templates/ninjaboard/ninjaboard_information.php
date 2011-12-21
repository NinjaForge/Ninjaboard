<?php defined('_JEXEC') or die('Restricted access'); 

	
	$this->loadbar = 'information';
?>

	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>

	<div id="nbInformation">
		<?php echo $this->loadInformation(); ?>
	</div>
