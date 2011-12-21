<? /** $Id: default.php 1402 2011-01-13 00:41:26Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/site.css" />

<? $img = isset($forum->params['customization']['icon']) ? $forum->params['customization']['icon'] : 'default.png' ?>
<? $img = @$img('/forums-large/' . $img) ? @$img('/forums-large/' . $img) : @$img('/forums/' . $img) ?>

<style type="text/css">
	#ninjaboard .title a {
		display: inline-block;
		background-image: url(<?= $img ?>); 
		background-repeat: no-repeat;
		padding-left: 37px;
		min-height: 32px;
		line-height: 1.3em;
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
				<?= @helper('site::com.ninjaboard.template.helper.behavior.watch') ?>
			</div>
		<? endif ?>
		<div class="clearfix"></div>
		
		<?= $block_subforums ?>
	
		<? if($forum->topic_permissions > 0) : ?>
			<?= $pagination ?>
			<div style="clear:both"></div>
		<? endif ?>
	
	</div>
	
	<? if($forum->topic_permissions > 0) : ?>
		<?= @render($topics, false, $forum->params['module']) ?>
		<div class="footer relative">
			<?= $pagination ?>
		</div>
	<? endif ?>
</div>