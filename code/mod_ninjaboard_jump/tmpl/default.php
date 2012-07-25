<? defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div id="ninjaboard-quickjump">
	<a href="forum" class="quickjump">
		<span><?= @text('COM_NINJABOARD_JUMP_JUMP_TO_FORUM') ?></span>
	</a>
	<ul class="jumplist">
		<? foreach ($forums as $forum) : ?>
			<li class="level-<?= $forum->level ?> <?= $forum->alias ?>">
				<a href="<?= @route('index.php?option=com_ninjaboard&view=forum&id='.$forum->id) ?>">
					<?= $forum->title ?>
				</a>
			</li>
		<? endforeach ?>
	</ul>
</div>