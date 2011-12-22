<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach($ranks as $i => $rank) : ?>
<tr class="<?= @ninja('grid.zebra') ?> state-<?= @toggle($rank->enabled, 'enable', 'disable') ?>">
	<?= @ninja('grid.count', array('total' => @$total)) ?>
	<td class="grid-check"><?= @ninja('grid.checkedout', array('row' => $rank)) ?></td>
	<td><?= @$edit($rank, $i, 'title', 'id') ?></td>
	<td width="160px"><img src="<?= @img('/rank/'.$rank->rank_file) ?>" alt="<?= $rank->title ?>" /></td>
	<td><?= $rank->min ?></td>
	<td align="center" width="32px"><?= @ninja('grid.toggle', array('enabled' => $rank->enabled)) ?></td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => @$total, 'colspan' => 6)) ?>