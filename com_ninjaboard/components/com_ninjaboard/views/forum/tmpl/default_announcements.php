<? /** $Id: default_announcements.php 2126 2011-07-07 20:51:36Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<!-- replace "announcement" with class suffix -->
<? /*
<div class="ninjaboard-block category">	
	<ul class="topiclist header">
		<li class="row">
				<span><?= @text('Announcements') ?></span>
				<span class="topics"><?= @text('Topics') ?></span>
				<span class="posts"><?= @text('Posts') ?></span>
				<span class="lastpost"><?= @text('Last Post') ?></span>
		</li>
	</ul>
	<ul class="topiclist forums categories announcements">
	<? foreach (@$announcements as $announcement) : ?>
		<li class="row">
			<span class="icon" style="background-image: url(<?= @$images . '/forums/star.png' ?>); background-repeat: no-repeat;">
				<a href="<?= @route('index.php?view=topic&id=' . $announcement->id) ?>" class="announcementtitle"><?= $announcement->subject ?></a><br />
				<?= JText::sprintf('by %s', $announcement->first_username) ?> &#187; <?= @date($announcement->first_post_date, @$dateformat, @$timezone) ?>
			</span>
			<span class="topics"><span class="separator"></span><!--<?= $announcement->topics ?>-->0</span>
			<span class="posts"><span class="separator"></span><span class="separator right"></span><!--<?= $announcement->posts ?>-->0</span>
			<span class="lastpost">
				<?= $announcement->last_username ?>
				<a href="<?= @route('index.php?view=topic&id=' . $announcement->id_last_post) ?>"><img src="<?= @mediaurl ?>/napi/img/16/page.png" alt="<?= @text('View the latest post') ?>" title="<?= @text('View the latest post') ?>" /></a> <br /><?= @date($announcement->last_post_date, @$dateformat, @$timezone) ?>
			</span>
		</li>
	<? endforeach ?>
	<? @$placeholder() ?>
	</ul>
</div>
*/ ?>