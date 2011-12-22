<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if($length > 0) : ?>
	<?= @template('admin::com.ninja.view.search.filter_search_enabled') ?>
<? endif ?>

<? @ninja('behavior.sortable') ?>
<form action="<?= @route() ?>" method="post" id="<?= @id() ?>" class="placeholder-up-two-lines">
	<?= @$placeholder('settings', null, 'Add %s', 'You haven\'t added any settings yet.<br> But don\'t worry about stuff breaking.<br> We\'ll just use the default settings until you do.') ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<th class="hasHint" title="<?= @text('Drag here to reorder&hellip;') ?>"></th>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check"><?= @ninja('grid.checkall') ?></th>
				<th><?= @ninja('grid.sort', array('title' => 'Title')) ?></th>
				<th><?= @text('Theme') ?></th>
				<th width="1px" class="grid-center">
					<?= @ninja('grid.sort', array('title' => 'Default')) ?>
				</th>																
				<th width="1px" class="grid-center"><?= @ninja('grid.sort', array('title' => 'Enabled', 'order' => 'published')) ?></th>					
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => $total, 'colspan' => 8)) ?>
		<tbody class="sortable">
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>