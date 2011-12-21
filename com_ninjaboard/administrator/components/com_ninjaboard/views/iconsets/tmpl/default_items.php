<? /** $Id: default_items.php 959 2010-09-21 14:33:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach (@$iconsets as $i => $iconset) : ?>
<tr class="<?= @ninja('grid.zebra') ?>">
	<?= @ninja('grid.count', @$state->offset, @$total) ?>
	<td><?= @ninja('grid.id', $iconset->id) ?></td>
	<td><?= $iconset->name ?></td>
	<td align="center"><?= $iconset->version ?></td>
	<td><?= @$date->_strftime(@text('DATE_FORMAT_LC'), @$date->toUnix($iconset->creationdate)) ?></td>
	<td><a href="http://<?= $iconset->authorurl ?>"><?= $iconset->author ?></a></td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', count(@$iconsets), 6, @$total < 10) ?>