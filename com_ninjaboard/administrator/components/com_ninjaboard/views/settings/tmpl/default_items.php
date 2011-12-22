<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach ($settings as $i => $setting) : ?>
<tr class="<?= @ninja('grid.zebra') ?> sortable">
	<td class="handle"></td>
	<?= @ninja('grid.count', array('total' => $total)) ?>
	<td class="grid-check"><?= @ninja('grid.checkedout', array('row' => $setting)) ?></td>
	<td width="50%"><?= @$edit($setting, $i, 'title', 'id') ?></td>
	<td><?= $setting->theme ?></td>
	<td class="grid-center">
	<? if ($setting->default) : ?>
		<img src="<?= @$img('/16/star.png') ?>" alt="<?= @text('Default') ?>" />
	<? else : ?>
		<img src="<?= @$img('/16/star_off.png') ?>" alt="<?= @text('Default') ?>" />
	<? endif ?>
	</td>
	<td class="grid-center"><?= @ninja('grid.toggle', array('enabled' => $setting->enabled)) ?></td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => $total, 'colspan' => 7)) ?>