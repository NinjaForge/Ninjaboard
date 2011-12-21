<? /** $Id: row_forum.php 1907 2011-05-22 23:12:43Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach (@$forums as $forum) : ?>
	<li class="row forum <?= $forum->new && $forum->unread ? 'unread' : '' ?> <?= !$forum->unread ? 'read' : '' ?>">
		<? $icon = isset($forum->params['customization']['icon']) ? $forum->params['customization']['icon'] : 'default.png' ?>
		<? $iconclass = 'forum-icon-'.KInflector::underscore(str_replace('.png', '', $icon)) ?>
		<style type="text/css">.row .<?= $iconclass ?> {background-image: url(<?= @$img('/forums/'.$icon) ?>);}</style>
		<style type="text/css">li.unread dl.icon dt {background-image: url(<?= @$img('/32/unread_badge.png') ?>);}</style>
		
		<dl class="icon <?= $iconclass ?>">
			<dt>
				<a href="<?= @route('view=forum&id=' . $forum->id) ?>" class="forumtitle <? if(!$forum->description) echo 'no-description' ?>" title="<?= @escape($forum->title) ?>">
					<?= @escape($forum->title) ?>
				</a>
				<? if($forum->description) : ?>
					<div class="description"><?= @ninja('bbcode.parse', array('text' => $forum->description)) ?></div>
				<? endif ?>
				<? if(count($forum->subforums) > 0) : ?>
					<strong class="label">
						<?= 
							count($forum->subforums) > 1 
							? @text('Subforums:') 
							: @text('Subforum:')
						?>
					</strong>
					<? foreach($forum->subforums as $subforum) : ?>
					<a href="<?= @route('view=forum&id=' . $subforum->id) ?>" class="subforum"><img src="<?= @$img('/16/page.png') ?>" alt="forum icon" class="icon" /><?= $subforum->title ?></a>
					<? endforeach ?>
				<? endif ?>
			</dt>
			<dd class="topics"><a href="#" class="separator"></a><?= $forum->topics ?> <dfn><?= @text('Topics') ?></dfn></dd>
			<dd class="posts"><a href="#" class="separator"></a><?= $forum->posts ?> <dfn><?= @text('Posts') ?></dfn></dd>
			<dd class="lastpost"><a href="#" class="separator"></a><span class="lastpost">
			<? if ($forum->last_post_id) : ?>
				<dfn>Last post</dfn>
					<a href="<?= @route('view=topic&id=' . $forum->last_topic_id . '&post='.$forum->last_post_id) ?>#p<?= $forum->last_post_id ?>"><?= $forum->subject ?></a><br />
					 <?= @text('by') ?>  
					 <a href="<?= @route('view=person&id=' . $forum->created_user_id) ?>" class="username-coloured"><?= @escape($forum->last_post_username) ?></a>
					 <?= @ninja('date.html', array('date' => $forum->last_post_date)) ?>
			<? else : ?>
				<?= @text('No Posts') ?><br />&#160;
			<? endif ?>
			</span></dd>
		</dl>
	</li>
<? endforeach ?>