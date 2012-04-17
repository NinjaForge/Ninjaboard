<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<form action="<?= @route() ?>" method="post" id="<?= @ninja('default.formid') ?>">
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<?= @ninja('grid.count', @$state->offset, @$total, true) ?>
				<th width="1px"><?= @ninja('grid.checkall') ?></th>
				<th><?= @text('COM_NINJABOARD_NAME') ?></th>
				<th><?= @text('COM_NINJABOARD_VERSION') ?></th>
				<th><?= @text('COM_NINJABOARD_DATE') ?></th>
				<th><?= @text('COM_NINJABOARD_AUTHOR') ?></th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', @$total, @$state->offset, @$state->limit, 4, 6) ?>
		<tbody>
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>