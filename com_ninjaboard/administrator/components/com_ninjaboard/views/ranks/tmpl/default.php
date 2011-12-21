<? /** $Id: default.php 1270 2010-12-22 13:14:06Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<style type="text/css">
	table.adminlist tbody.ranks tr td {height: 32px;}
</style>

<? if($length > 0) : ?>
	<?= @template('admin::com.ninja.view.search.filter_search_enabled') ?>
<? endif ?>

<form action="<?= @route() ?>" method="post" id="<?= @id() ?>">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check"><?= @ninja('grid.checkall') ?></th>
				<th><?= @ninja('grid.sort', array('title' => 'Title')) ?></th>				
				<th><?= @ninja('grid.sort', array('title' => 'Enabled')) ?></th>
				<th width="1px"><?= @ninja('grid.sort', array('title' => 'Min posts')) ?></th>
				<th>&#160;</th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => @$total, 'colspan' => 7)) ?>
		<tbody class="ranks">
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>