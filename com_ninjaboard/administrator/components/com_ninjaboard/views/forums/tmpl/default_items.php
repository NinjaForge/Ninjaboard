<? /** $Id: default_items.php 1310 2011-01-03 17:52:07Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach (@$forums as @$i => @$forum) : ?>
	<tr class="<?= @ninja('grid.zebra') ?> sortable state-<?= @toggle(@$forum->enabled, 'enable', 'disable') ?>">
		<?= @ninja('grid.count', array('total' => @$total)) ?>
		<td class="grid-check"><?= @ninja('grid.checkedout', array('row' => @$forum)) ?></td>
		<td>
			<?= str_repeat('&#160;', ($forum->level - 1) * 6) ?>
			<?= @$edit(@$forum, $i, 'title', 'id') ?>
		</td>
		<td align="center" width="40px"><?= @$forum->topics ?></td>
		<td align="center" width="40px"><?= @$forum->posts ?></td>
		<td align="center" width="64px" style="white-space: nowrap">
			<?= @ninja('grid.toggle', array('toggle' => 'locked', 'locked' => $forum->locked)) ?>
			<?= @ninja('grid.toggle', array('enabled' => $forum->enabled)) ?>
		</td>
		<td align="center" width="16px" style="white-space: nowrap">
			<span class="<?= @id('order-up') ?>">&nbsp;</span>
			<span class="<?= @id('order-down') ?>">&nbsp;</span>
		</td>
	</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => @$total, 'colspan' => 7)) ?>