<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="nbSeparator nbClr"></div>
<div id="nbBoardLegend">
	<h1><?php echo JText::_('NB_BOARDLEGEND'); ?></h1>
	<div>
	<?php 
		$iconsBoard = $this->ninjaboardIconSet->getIconsByGroup('iconBoard');
		for ($i = 0, $n = count($iconsBoard); $i < $n; $i++) :
			$icon = $iconsBoard[$i];
		?>
	<span><img src="<?php echo $icon->fileName; ?>" alt="<?php echo $icon->title; ?>" /> <?php echo $icon->title; ?></span>
	<?php 
		endfor;
	?>
	</div>
</div>
<div class="nbClr"></div>
