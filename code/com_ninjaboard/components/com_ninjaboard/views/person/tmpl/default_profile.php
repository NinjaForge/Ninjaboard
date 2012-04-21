<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<!--<h2>
	<?= $person->display_name ?>
</h2>-->


<? if($person->usergroups) : ?>
<?= @template('com://site/ninjaboard.view.person.usergroups', array('usergroups' => $person->usergroups)) ?>
<? endif ?>

<ul>
	<li>
		<strong><?= @text('COM_NINJABOARD_POSTS') ?></strong> <?= $person->posts ?>
	</li>
	<li>
		<strong><?= @text('COM_NINJABOARD_MEMBER_SINCE') ?></strong> <?= @ninja('date.html', array('date' => $person->registerDate)) ?>
	</li>
	<li>
		<strong><?= @text('COM_NINJABOARD_LAST_LOGGED_IN') ?></strong> <?= @ninja('date.html', array('date' => $person->lastvisitDate)) ?>
	</li>
</ul>
