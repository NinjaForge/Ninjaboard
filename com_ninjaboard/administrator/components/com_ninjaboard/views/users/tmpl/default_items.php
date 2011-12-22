<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach($users as $i => $user) : ?>
<tr class="<?= @ninja('grid.zebra') ?>">
	<?= @ninja('grid.count', array('total' => @$total)) ?>
	<td class="grid-check">
		<?= @ninja('grid.id', array('value' => $user->id)) ?>
	</td>
	<td width="1" style="text-align: right">
		<?= $user->id ?>
	</td>
	<td>
		<a href="<?= @route('view=user&id='.$user->id) ?>" class="hasHint"<? /* title="&lt;img src=&quot;<?= KRequest::root().$user->avatar ?>&quot; /&gt;" */ ?>>
			<?= $user->name ?>
		</a>
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
		<?= $user->lastvisitDate == '0000-00-00 00:00:00' ? @text('No visit since registration') : @ninja('date.html', array('date' => $user->lastvisitDate)) ?>
	</td>
	<td width="1" style="text-align:center">
		<?= $user->posts ?>
	</td>
	<td width="1">
		<img src="<?= @$img('/rank/'.$user->rank_icon) ?>" alt="<?= @text('Rank logo') ?>" />
	</td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => @$total, 'colspan' => 11)) ?>