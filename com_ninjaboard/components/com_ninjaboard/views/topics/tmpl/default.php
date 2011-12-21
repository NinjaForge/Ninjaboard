<? /** $Id: default.php 2439 2011-09-01 11:53:24Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('com://site/ninjaboard.view.default.head') ?>

<div id="ninjaboard" class="ninjaboard <?= $params['pageclass_sfx'] ?> <?= $params['style']['type'] ?> <?= $params['style']['border'] ?> <?= $params['style']['separators'] ?>">
	<?= @render(@template('paginated'), false, (array)@$params['module']) ?>
</div>