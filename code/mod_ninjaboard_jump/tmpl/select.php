<? defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div id="ninjaboard-quickjump">
	<select class="jumplist" onchange="window.open(this.options[this.selectedIndex].value,'_top')">
		<option value=""><?= @text('COM_NINJABOARD_JUMP_JUMP_TO_FORUM') ?></option>
		<? foreach ($forums as $forum) : ?>
			<option value="<?= @route('index.php?option=com_ninjaboard&view=forum&id='.$forum->id) ?>"><?= $forum->title ?></option>
		<? endforeach ?>
	</select>
</div>