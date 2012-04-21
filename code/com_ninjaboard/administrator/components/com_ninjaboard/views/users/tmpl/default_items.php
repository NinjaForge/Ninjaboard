<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach($users as $i => $user) : ?>
<tr>
	<?= @ninja('grid.count', array('total' => @$total)) ?>
	<td class="grid-check">
		<?= @helper('grid.checkbox', array('row' => $user)) ?>
	</td>
	<td width="1" style="text-align: right">
		<?= $user->id ?>
	</td>
	<td>
		<?= @ninja('grid.edit', array('row' => $user, 'column' => 'name')) ?>
	</td>
	<td>
		<?= $user->username ?>
	</td>
	<td>
		<?= @text($user->usertype) ?>
	</td>
	<td class="groups">
		<? $n = 1 ?><? foreach($user->usergroups as $group) : ?>
			<a href="<?= @route('view=usergroup&id='.$group->id) ?>">
				<?= $group->title ?> 
			</a>
			<? if($n < count($user->usergroups)) echo '| ' ?>
		<? $n++ ?><? endforeach ?>
	</td>
	<td>
		<a href="mailto:<?= $user->email ?>">
			<?= $user->email ?>
		</a>
	</td>
	<td>
		<?= $user->lastvisitDate == '0000-00-00 00:00:00' ? @text('COM_NINJABOARD_NO_VISIT_SINCE_REGISTRATION') : @ninja('date.html', array('date' => $user->lastvisitDate)) ?>
	</td>
	<td width="1" style="text-align:center">
		<?= $user->posts ?>
	</td>
	<td width="1">
		<img src="<?= @$img('/rank/'.$user->rank_icon) ?>" alt="<?= @text('COM_NINJABOARD_RANK_LOGO') ?>" />
	</td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => @$total, 'colspan' => 11)) ?>