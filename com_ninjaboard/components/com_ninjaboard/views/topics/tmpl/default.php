<? /** $Id: default.php 1214 2010-12-13 02:38:37Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/site.css" />

<div id="ninjaboard" class="ninjaboard <?= $params['pageclass_sfx'] ?> <?= $params['style']['type'] ?> <?= $params['style']['border'] ?> <?= $params['style']['separators'] ?>">
	<?= @render(@template('paginated'), false, (array)@$params['module']) ?>
</div>