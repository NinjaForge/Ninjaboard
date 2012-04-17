<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.grid.head') ?>

<? if(@$length > 0) : ?>
	<?= @template('ninja:view.search.filter_thead') ?>
<? endif ?>

<?= @ninja('behavior.sortable') ?>
<form action="<?= @route() ?>" method="post" id="<?= @id() ?>" class="-koowa-grid">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<th class="hasHint" title="<?= @text('COM_NINJABOARD_DRAG_HERE_TO_REORDERHELLIP;') ?>"></th>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check"><?= @helper('grid.checkall') ?></th>
				<th width="100%"><?= @text('COM_NINJABOARD_TITLE') ?></th>
				<? foreach ($columns as $column) : ?>
					<th><?= @text($column) ?></th>
				<? endforeach ?>
			</tr>
		</thead>
			<?= @ninja('paginator.tfoot', array('total' => @$total, 'colspan' => $colspan, 'display' => 4)) ?>
		<tbody class="sortable">
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>