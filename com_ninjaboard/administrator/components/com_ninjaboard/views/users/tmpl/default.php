<? /** $Id: default.php 1298 2011-01-03 03:10:31Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<?= @template('form_filtering') ?>
<form action="<?= @route() ?>" method="post" id="<?= @id() ?>">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check"><?= @ninja('grid.checkall') ?></th>
				<th><?= @ninja('grid.sort', array('title' => 'ID', 'order' => 'id')) ?></th>
				<th><?= @ninja('grid.sort', array('title' => 'Name')) ?></th>
				<th><?= @ninja('grid.sort', array('title' => 'Username')) ?></th>
				<th><?= @ninja('grid.sort', array('title' => 'Joomla! Group', 'order' => 'usertype')) ?></th>
				<th><?= @ninja('grid.sort', array('title' => 'Ninjaboard Group', 'order' => 'ninjaboard_usergroup_id')) ?></th>
				<!--<th><?= @ninja('grid.sort', array('title' => 'NB Groups', 'order' => 'role')) ?></th>-->
				<th><?= @ninja('grid.sort', array('title' => 'Email', 'order' => 'email')) ?></th>
				<th><?= @ninja('grid.sort', array('title' => 'Last Visit Date', 'order' => 'lastvisitdate')) ?></th>
				<th><?= @text('Posts') ?></th>
				<th><?= @text('Rank') ?></th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => @$total, 'colspan' => 11)) ?>
		<tbody>	
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>