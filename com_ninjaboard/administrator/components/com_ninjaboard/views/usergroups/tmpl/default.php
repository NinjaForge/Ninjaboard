<? /** $Id: default.php 1242 2010-12-19 15:29:36Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if(@$length > 0) : ?>
	<?= @template('admin::com.ninja.view.search.filter_thead') ?>
<? endif ?>

<? @ninja('behavior.sortable') ?>
<form action="<?= @route() ?>" method="post" id="<?= @id() ?>">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<th class="hasHint" title="<?= @text('Drag here to reorder&hellip;') ?>"></th>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check"><?= @ninja('grid.checkall') ?></th>
				<th width="100%"><?= @text('Title') ?></th>
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