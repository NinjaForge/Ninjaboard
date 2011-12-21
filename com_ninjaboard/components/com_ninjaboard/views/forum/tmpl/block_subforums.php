<? /** $Id: block_subforums.php 1246 2010-12-20 01:18:11Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="category">
	<ul class="topiclist">
		<li class="header">
			<dl class="icon">
				<dt>
					<? if(KRequest::get('get.view', 'cmd') == 'forums' && $params['view_settings']['forums_title'] == 'permalink' && isset($forum)) : ?>
						<a href="<?= @route('view=forum&id='.$forum->id) ?>">[<?= @text('permalink') ?>]</a>
					<? endif ?>
				</dt>
				<dd class="topics"><?= @text('Topics') ?></dd>
				<dd class="posts"><?= @text('Posts') ?></dd>
				<dd class="lastpost"><span><?= @text('Last post') ?></span></dd>
			</dl>
		</li>
	</ul>
	<ul class="topiclist forums">
		<?= @template('site::com.ninjaboard.view.forum.row_forum') ?>
	</ul>
</div>