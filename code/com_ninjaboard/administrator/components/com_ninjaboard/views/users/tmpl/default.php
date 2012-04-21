<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.grid.head') ?>

<?= @template('form_filtering') ?>
<form action="<?= @route() ?>" method="post" id="<?= @id() ?>" class="-koowa-grid">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check"><?= @helper('grid.checkall') ?></th>
				<th><?= @helper('grid.sort', array('title' => 'ID', 'column' => 'id')) ?></th>
				<th><?= @helper('grid.sort', array('column' => 'name')) ?></th>
				<th><?= @helper('grid.sort', array('column' => 'username')) ?></th>
				<th><?= @helper('grid.sort', array('title' => 'Joomla! Group', 'column' => 'usertype')) ?></th>
				<th><?= @helper('grid.sort', array('title' => 'Ninjaboard Group', 'column' => 'ninjaboard_usergroup_id')) ?></th>
				<!--<th><?= @helper('grid.sort', array('title' => 'NB Groups', 'column' => 'role')) ?></th>-->
				<th><?= @helper('grid.sort', array('title' => 'Email', 'column' => 'email')) ?></th>
				<th><?= @helper('grid.sort', array('title' => 'Last Visit Date', 'column' => 'lastvisitdate')) ?></th>
				<th><?= @text('COM_NINJABOARD_POSTS') ?></th>
				<th><?= @text('COM_NINJABOARD_RANK') ?></th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => @$total, 'colspan' => 11)) ?>
		<tbody>	
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>