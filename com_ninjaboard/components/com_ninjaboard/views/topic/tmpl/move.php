<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/site.css" />
<link rel="stylesheet" href="/form.css" />
<link rel="stylesheet" href="/site.form.css" />

<div id="ninjaboard" class="ninjaboard topic move <?= $topic->params['pageclass_sfx'] ?> <?= $forum->params['style']['type'] ?> <?= $forum->params['style']['border'] ?> <?= $forum->params['style']['separators'] ?>">
	<h1 class="title"><?= sprintf(@text('Moving topic «%s»'), $topic->subject) ?></a></h1>
	<form action="<?= @route('id='.@$topic->id) ?>" method="post" id="<?= @id() ?>" class="ninjaboard">
		<fieldset class="adminform ninja-form">
			<div class="element subject">
				<label class="key" for="forum_id"><?= @text('Forum') ?></label>
				<?
					$list = KFactory::tmp('admin::com.ninjaboard.model.forums')->limit(0)->indent(1)->getList();
					$id = $topic->forum_id;
					foreach($list as $forum)
					{
						if($forum->id === $id && $id > 0) $forum->disable = true;
						$forums[]	 = $forum->getData();
					}
				?>
				<?= JHTML::_('select.genericlist', $forums, 'forum_id', array('class' => 'value required'), 'id', 'title', $topic->forum_id) ?>
			</div>
			<div class="element">
				<div class="key">
					<input type="hidden" name="moved_from_forum_id" value="<?= $topic->forum_id ?>" />
					<input type="hidden" name="show_symlinks" value="0" />
					<input type="checkbox" name="show_symlinks" id="show_symlinks" <? if($topic->show_symlinks) echo 'checked' ?> value="1" />
				</div>
				<label for="show_symlinks">
					<?= @text('Leave ghost topic behind') ?>
				</label>
			</div>
			<div class="element footer">
				<div class="inner">
					<div id="<?= @id('save') ?>">
						<button name="action" value="save" type="submit"><?= @text('Save') ?></button>
					</div>
					&#160;
					<button name="action" value="cancel" type="submit"><?= @text('Cancel') ?></button>
				</div>
			</div>
		</fieldset>
	</form>
</div>