<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<!--<h2>
	<?= $person->display_name ?>
</h2>-->


<? if($person->usergroups) : ?>
<?= @template('com://site/ninjaboard.view.person.usergroups', array('usergroups' => $person->usergroups)) ?>
<? endif ?>

<ul>
	<li>
		<strong><?= @text('Posts:') ?></strong> <?= $person->posts ?>
	</li>
	<li>
		<strong><?= @text('Member Since:') ?></strong> <?= @ninja('date.html', array('date' => $person->registerDate)) ?>
	</li>
	<li>
		<strong><?= @text('Last Logged In:') ?></strong> <?= @ninja('date.html', array('date' => $person->lastvisitDate)) ?>
	</li>
</ul>
