<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/site.css" />

<div id="ninjaboard" class="ninjaboard <?= $params['pageclass_sfx'] ?> <?= $params['style']['type'] ?> <?= $params['style']['border'] ?> <?= $params['style']['separators'] ?>">
	<?= @render(@template('paginated'), false, (array)@$params['module']) ?>
</div>