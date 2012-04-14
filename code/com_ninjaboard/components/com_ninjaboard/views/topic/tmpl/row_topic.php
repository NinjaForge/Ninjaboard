<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? $img = isset(@$topic->params['customization']['icon']) ? @$topic->params['customization']['icon'] : '32__default.png' ?>
<? $iconclass = 'topic-icon-'.KInflector::underscore(str_replace('.png', '', $img)) ?>

<style type="text/css">.ninjaboard .unread-indicator {background-image: url(<?= @$img('/unread.png') ?>);}</style>

<li class="row topic <?= $topic->sticky ? 'sticky' : '' ?> <?= $topic->moved_to_forum_title ? 'moved' : '' ?> <?= $topic->new && $topic->unread ? 'unread' : '' ?> <?= !$topic->unread ? 'read' : '' ?>">
		
		<style type="text/css">.row .<?= $iconclass ?> {background-image: url(<?= @$img('/topic/'.$img) ?>);}</style>
		<dl class="icon <?= $iconclass ?>">
			<dt>
			    <span class="unread-indicator"></span>
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
				<span>
				<? if (@$topic->last_post_id) : ?>
					<dfn>Last post</dfn>
					<a href="<?= @route('view=topic&id=' . @$topic->id . '&post='.@$topic->last_post_id.'#p' . @$topic->last_post_id) ?>">
						<img src="<?= @$img('/16/page.png') ?>" alt="<?= @text('View the latest post') ?>" title="<?= @escape(@$topic->last_post_subject) ?>" />
					</a>
					 <?= @text('by') ?>  
					 <? if($topic->created_user_id) : ?>
					 <a href="<?= @route('view=person&id=' . $topic->created_user_id) ?>" class="username-coloured">
					 	<?= @escape($topic->last_post_username) ?>
					 </a>
					 <? else : ?>
					 	<?= @escape($topic->last_post_username) ?>
					 <? endif ?>
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