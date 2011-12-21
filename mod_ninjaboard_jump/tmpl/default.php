<div id="ninjaboard-quickjump">
	<a href="forum" class="quickjump">
		<span><?= @text('Jump to Forum') ?></span>
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