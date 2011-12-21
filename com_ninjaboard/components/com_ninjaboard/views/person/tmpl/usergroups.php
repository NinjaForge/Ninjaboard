<? /** $Id: usergroups.php 1329 2011-01-05 01:36:01Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if($usergroups) : ?>
<ul class="person-usergroups">
	<? foreach($usergroups as $group) : ?>
		<? $alias = KInflector::underscore($group->title) ?>
		<li class="usergroup-<?= $alias ?>">
			<? $icon = @$img('/usergroup/'.$alias.'.png') ?>
			<? if($icon) : ?>
				<img src="<?= $icon ?>" title="<?= @escape($group->title) ?>" />
			<? else : ?>
				<?= $group->title ?>
			<? endif ?>
		</li>
	<? endforeach ?>
</ul>
<? endif ?>