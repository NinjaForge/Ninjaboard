<? /** $Id: default_items.php 2390 2011-08-13 15:45:34Z richie $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach (@$profile_fields as $i => $profile_field) : ?>
<tr class="<?= @ninja('grid.zebra') ?> sortable state-<?= @toggle($profile_field->enabled, 'enable', 'disable') ?>">
	<td class="handle"></td>
	<?= @ninja('grid.count', array('total' => @$total)) ?>
	<td class="grid-check"><?= @helper('grid.checkbox', array('row' => $profile_field)) ?></td>
	<td><?= @ninja('grid.edit', array('row' => $profile_field, 'column' => 'name')) ?></td>
	<td align="center" width="32px"><?= @ninja('grid.toggle', array('enabled' => $profile_field->enabled)) ?></td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => @$total, 'colspan' => 5)) ?>