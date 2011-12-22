<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach($ranks as $i => $rank) : ?>
<tr class="state-<?= @toggle($rank->enabled, 'enable', 'disable') ?>">
	<?= @ninja('grid.count', array('total' => @$total)) ?>
	<td class="grid-check"><?= @helper('grid.checkbox', array('row' => $rank)) ?></td>
	<td><?= @ninja('grid.edit', array('row' => $rank)) ?></td>
	<td width="160px"><img src="<?= @img('/rank/'.$rank->rank_file) ?>" alt="<?= $rank->title ?>" /></td>
	<td><?= $rank->min ?></td>
	<td align="center" width="32px"><?= @ninja('grid.toggle', array('enabled' => $rank->enabled)) ?></td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => @$total, 'colspan' => 6)) ?>