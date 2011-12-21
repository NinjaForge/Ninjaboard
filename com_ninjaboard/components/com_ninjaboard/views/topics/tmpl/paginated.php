<? /** $Id: paginated.php 959 2010-09-21 14:33:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="header relative">
	<?= @$pagination ?>
</div>
<?= @template('list') ?>
<div class="footer relative">
	<?= @$pagination ?>
</div>