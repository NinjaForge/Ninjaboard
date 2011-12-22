<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.grid.head') ?>

<style type="text/css">
	table.adminlist tbody.ranks tr td {height: 32px;}
</style>

<? if($length > 0) : ?>
	<?= @template('ninja:view.search.filter_search_enabled') ?>
<? endif ?>

<form action="<?= @route() ?>" method="post" id="<?= @id() ?>" class="-koowa-grid">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check"><?= @helper('grid.checkall') ?></th>
				<th><?= @helper('grid.sort', array('column' => 'title')) ?></th>				
				<th><?= @helper('grid.sort', array('column' => 'enabled')) ?></th>
				<th width="1px"><?= @helper('grid.sort', array('title' => 'Min Posts', 'column' => 'min')) ?></th>
				<th>&#160;</th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => @$total, 'colspan' => 7)) ?>
		<tbody class="ranks">
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>