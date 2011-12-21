<?php defined('_JEXEC') or die('Restricted access');

	$loadbar = 'category';

	// this is the way scripts should be added to the document to be XHTML 1.0 Transitional
	$this->document->addScript($this->templatePathLive.'/js/jquery.js');
	$this->document->addScript($this->templatePathLive.'/js/ninjaboard.core.js');
	$this->document->addScript($this->templatePathLive.'/js/ninjaboard.jquery.js');
	$this->document->addScript($this->templatePathLive.'/js/jquery.easing.js');
	$this->document->addScript($this->templatePathLive.'/js/jquery.slidingBar.js');

	$javaScript = '$j(document).ready(function(){ $j(\'.slidingBar\').slidingBar(); });';
	$this->document->addScriptDeclaration($javaScript);

	for ($i = 0; $i < $this->categoriesCount; $i ++) :
		$this->category =& $this->getCategory($i);
?>

	<div class="nbCategoryWrapper slidingBar">
		<?php echo $this->loadTemplate('category'); ?>
		<div class="slidingBar-content">
			<div class="nbCategoryTitles">
				<div class="nbPostsHeader"><?php echo JText::_('NB_LASTPOST'); ?></div>
				<div class="nbTopicsHeader"><span><?php echo JText::_('NB_TOPICS'); ?></span></div>
				<div class="nbForumsHeader"><?php echo JText::_('NB_FORUMS'); ?></div>
			</div>
			<div id="nbForumsBlock_<?php echo $this->category->id; ?>">
				<?php
					$this->categoryForums = $this->getForums($this->category->id);
					$count = count($this->categoryForums);
					for ($f = 0; $f < $count; $f++) {
						$this->forum =& $this->getForum($this->categoryForums[$f]);
						echo $this->loadTemplate('forum');
					}
				?>
			</div>
		</div>
	</div>

<?php endfor; ?>
