<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('com://site/ninjaboard.view.default.head') ?>

<? $img = isset($forum->params['customization']['icon']) ? $forum->params['customization']['icon'] : 'default.png' ?>
<? $img = @$img('/forums-large/' . $img) ? @$img('/forums-large/' . $img) : @$img('/forums/' . $img) ?>

<style type="text/css">
	#ninjaboard .title a {
		background-image: url(<?= $img ?>); 
	}
</style>

<div id="ninjaboard" class="ninjaboard forum <?= $forum->params['pageclass_sfx'] ?> <?= $forum->params['style']['type'] ?> <?= $forum->params['style']['border'] ?> <?= $forum->params['style']['separators'] ?>">
	<div class="header relative">
		<h2 class="title">
			<a href="<?= @route('id=' . @$forum->id) ?>" class="forum">
				<?= @escape(@$forum->title) ?>
			</a>
		</h2>
		<div class="description">
			<?= @ninja('bbcode.parse', array('text' => isset($forum->params['forum']['header']) && trim($forum->params['forum']['header']) ? $forum->params['forum']['header'] : $forum->description)) ?>
		</div>
		
		<? if($toolbar) : ?>
			<div class="start">
				<?= $toolbar ?>
			</div>
		<? endif ?>
		<? if($watch_button) : ?>
			<div class="end">
				<?= @helper('com://site/ninjaboard.template.helper.behavior.watch') ?>
			</div>
		<? endif ?>
		<div class="clearfix"></div>
		
		<?= $block_subforums ?>
	
		<? if($forum->topic_permissions > 0 && $topics) : ?>
			<?= $pagination ?>
			<div style="clear:both"></div>
		<? endif ?>
	
	</div>

	<? if($forum->topic_permissions > 0 && $topics) : ?>
		<?= @render($topics, false, $forum->params['module']) ?>
		<div class="footer relative">
			<?= $pagination ?>
		</div>
	<? /* If the user can't create anything or is a super admin, don't show anything */ ?>
	<? elseif($forum->topic_permissions > 1 && $me->gid != 25) : ?>
		<h2 class="message"><?= @text('No topics found here yet, go ahead and start one.') ?></h2>
	<? endif ?>
</div>