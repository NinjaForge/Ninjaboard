<? /** $Id: list.php 2439 2011-09-01 11:53:24Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="ninjaboard-block category topics">
	<ul class="topiclist">
		<li class="header">
			<dl class="icon">
				<dt></dt>
				<dd class="topics"><?= @text('Replies') ?></dd>
				<dd class="posts"><?= @text('Views') ?></dd>
				<dd class="lastpost"><span><?= @text('Last post') ?></span></dd>
			</dl>
		</li>
	</ul>
	<ul class="topiclist forums">
	<? foreach (@$topics as $topic) : ?>
		<?= @template('com://site/ninjaboard.view.topic.row_topic', array('topic' => $topic)) ?>
	<? endforeach ?>
	</ul>
</div>