<? /** $Id: default_items.php 2382 2011-08-12 23:05:33Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach($forums as $i => $forum) : ?>
	<tr class="sortable state-<?= @toggle($forum->enabled, 'enable', 'disable') ?>">
		<?= @ninja('grid.count', array('total' => $total)) ?>
		<td class="grid-check"><?= @helper('grid.checkbox', array('row' => $forum)) ?></td>
		<td>
			<?= str_repeat('&#160;', ($forum->level - 1) * 6) ?>
			<?= @ninja('grid.edit', array('row' => $forum)) ?>
		</td>
		<td align="center" width="40px"><?= @$forum->topics ?></td>
		<td align="center" width="40px"><?= @$forum->posts ?></td>
		<td align="center" width="24px" style="white-space: nowrap">
		    <?/* @TODO properly implement locked forums */?>
			<?/*= @ninja('grid.toggle', array('toggle' => 'locked', 'locked' => $forum->locked)) */?>
			<?= @ninja('grid.toggle', array('enabled' => $forum->enabled)) ?>
		</td>
		<td align="center" width="16px" style="white-space: nowrap">
			<span class="<?= @id('order-up') ?>">&nbsp;</span>
			<span class="<?= @id('order-down') ?>">&nbsp;</span>
		</td>
	</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => $total, 'colspan' => 7)) ?>