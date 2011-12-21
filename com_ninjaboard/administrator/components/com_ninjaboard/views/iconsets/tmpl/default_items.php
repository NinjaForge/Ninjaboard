<? /** $Id: default_items.php 2390 2011-08-13 15:45:34Z richie $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach (@$iconsets as $i => $iconset) : ?>
<tr>
	<?= @ninja('grid.count', @$state->offset, @$total) ?>
	<td><?= @helper('grid.checkbox', array('row' => $iconset)) ?></td>
	<td><?= $iconset->name ?></td>
	<td align="center"><?= $iconset->version ?></td>
	<td><?= @$date->_strftime(@text('DATE_FORMAT_LC'), @$date->toUnix($iconset->creationdate)) ?></td>
	<td><a href="http://<?= $iconset->authorurl ?>"><?= $iconset->author ?></a></td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', count(@$iconsets), 6, @$total < 10) ?>