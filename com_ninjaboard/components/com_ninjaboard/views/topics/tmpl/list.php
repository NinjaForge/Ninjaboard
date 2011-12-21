<? /** $Id: list.php 1399 2011-01-12 23:36:25Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="category topics">
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
		<?= @template('site::com.ninjaboard.view.topic.row_topic', array('topic' => $topic)) ?>
	<? endforeach ?>
	</ul>
</div>