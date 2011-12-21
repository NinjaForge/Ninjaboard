<? /** $Id: default.php 959 2010-09-21 14:33:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<form action="<?= @route() ?>" method="post" id="<?= @ninja('default.formid') ?>">
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<?= @ninja('grid.count', @$state->offset, @$total, true) ?>
				<th width="1px"><?= @ninja('grid.checkall') ?></th>
				<th><?= @text('Name') ?></th>
				<th><?= @text('Version') ?></th>
				<th><?= @text('Date') ?></th>
				<th><?= @text('Author') ?></th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', @$total, @$state->offset, @$state->limit, 4, 6) ?>
		<tbody>
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>