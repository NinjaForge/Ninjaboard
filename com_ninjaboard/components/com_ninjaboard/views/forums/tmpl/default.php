<? /** $Id: default.php 1267 2010-12-22 10:50:54Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/site.css" />

<div id="ninjaboard" class="ninjaboard forums <?= $params['pageclass_sfx'] ?> <?= $params['style']['type'] ?> <?= $params['style']['border'] ?> <?= $params['style']['separators'] ?>">

	<? /* Render Forums without child forums */ ?>
	<? $flats = array() ?>
	<? foreach($forums as $forum) : ?>
		<? if(count($forum->subforums)) continue ?>
		<? $flats[] = $forum ?>
	<? endforeach ?>

	<? if($flats) : ?>
		<?= @render(@template('site::com.ninjaboard.view.forum.block_subforums', array(
			'forums'			=> $flats,
			'forums_total'		=> count($flats),
			'params'			=> $params
		)), false, $params['module']) ?>
		
		<?= @helper('site::com.ninjaboard.template.helper.template.space') ?>
	<? endif ?>

	<? /* Render Forums with children forums */ ?>
	<? foreach($forums as $i => $forum) : ?>
		<? if(@$params['view_settings']['forums_title'] == 'linkable') $forum->title = '<a href="'.@route('view=forum&id='.$forum->id).'">'. $forum->title . '</a>' ?>
		<? $prepared = $this->getView()->prepare($forum) ?>
		<? /*if(@$params['view_settings']['forums_title'] == 'linkable') $prepared = @renderLinkTitle($forum->title, @route('view=forum&id='.$forum->id), $prepared, array('class' => 'inherit-color'))*/ ?>
		<?= $prepared ?>
		 
		<?= @helper('site::com.ninjaboard.template.helper.template.space') ?>
	<? endforeach ?>

	<? /* Render Latest Topics */ ?>
	<? if($showtopics) : ?>
		<div class="header relative">
			<h2 class="title"><?= @text('Latest Topics') ?></h2>
			<?= $pagination ?>
			<div style="clear:both"></div>
		</div>
		<?= @render($topics, false, $params['module']) ?>
		<div class="footer relative">
			<?= $pagination ?>
		</div>
	<? endif ?>
</div>