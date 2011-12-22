<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach (@$profile_fields as $i => $profile_field) : ?>
<tr class="<?= @ninja('grid.zebra') ?> sortable state-<?= @toggle($profile_field->enabled, 'enable', 'disable') ?>">
	<td class="handle"></td>
	<?= @ninja('grid.count', array('total' => @$total)) ?>
	<td class="grid-check"><?= @ninja('grid.checkedout', array('row' => $profile_field)) ?></td>
	<td><?= @$edit($profile_field, $i, 'name', 'id') ?></td>
	<td align="center" width="32px"><?= @ninja('grid.toggle', array('enabled' => $profile_field->enabled)) ?></td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => @$total, 'colspan' => 5)) ?>