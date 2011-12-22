<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if(@$length > 0) : ?>
	<?= @template('admin::com.ninja.view.search.filter_thead') ?>
<? endif ?>

<? @ninja('grid.sortables') ?>
<form action="<?= @route() ?>" method="post" id="<?= @id() ?>">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<th width="32px" class="hasHint" title="<?= @text('Drag here to reorder&hellip;') ?>"></th>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check"><?= @ninja('grid.checkall') ?></th>
				<th colspan="2"><?= @ninja('grid.sort', array('title' => 'Title')) ?></th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => @$total, 'display' => 4, 'colspan' => 5, 'ajax' => true)) ?>
		<tbody class="sortable">
		<?= @template('default_items') ?>
		</tbody>
	</table>
</form>