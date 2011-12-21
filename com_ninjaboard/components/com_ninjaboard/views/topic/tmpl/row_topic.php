<? /** $Id: row_topic.php 1663 2011-03-22 00:57:27Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? $img = isset(@$topic->params['customization']['icon']) ? @$topic->params['customization']['icon'] : '32__default.png' ?>
<? $iconclass = 'topic-icon-'.KInflector::underscore(str_replace('.png', '', $img)) ?>

<li class="row topic <?= $topic->moved_to_forum_title ? 'moved' : '' ?>">
		
		<style type="text/css">.row .<?= $iconclass ?> {background-image: url(<?= @$img('/topic/'.$img) ?>);}</style>
		<dl class="icon <?= $iconclass ?>">
			<dt>
				<a href="<?= @route('view=topic&id=' . @$topic->id) ?>" class="forumtitle subject" title="<?= @escape(@$topic->subject) ?>">
					<?= @escape(@$topic->subject) ?>
					<? if($topic->moved_to_forum_title) : ?>
					<em>
						<?= sprintf(@text('Topic moved to %s'), '<strong>'.$topic->moved_to_forum_title.'</strong>') ?>
					</em>
					<? endif ?>
				</a>
			</dt>
			<dd class="topics">
				<a href="#" class="separator"></a><?= @$topic->replies ?><dfn><?= @text('Replies') ?></dfn>
			</dd>
			<dd class="posts">
				<a href="#" class="separator"></a><?= @$topic->hits ?><dfn><?= @text('Views') ?></dfn>
			</dd>
			<dd class="lastpost">
				<a href="#" class="separator"></a>
				<span class="lastpost">
				<? if (@$topic->last_post_id) : ?>
					<dfn>Last post</dfn>
					<a href="<?= @route('view=topic&id=' . @$topic->id . '&post='.@$topic->last_post_id.'#p' . @$topic->last_post_id) ?>">
						<img src="<?= @$img('/16/page.png') ?>" alt="<?= @text('View the latest post') ?>" title="<?= @escape(@$topic->last_post_subject) ?>" />
					</a>
					 <?= @text('by') ?>  
					 <a href="<?= @route('view=person&id=' . @$topic->created_user_id) ?>" class="username-coloured">
					 	<?= @escape(@$topic->last_post_username) ?>
					 </a>
					 <br />
					 <em>
					 	<?= @ninja('date.html', array('date' => @$topic->last_post_date)) ?>
					 </em>
				<? else : ?>
					<? @text('No posts') ?><br />&#160;
				<? endif ?>
				</span>
			</dd>
		</dl>
		
</li>