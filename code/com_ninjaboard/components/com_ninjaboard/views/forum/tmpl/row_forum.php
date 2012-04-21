<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<style type="text/css">.ninjaboard .unread-indicator {background-image: url(<?= @$img('/unread.png') ?>)}</style>

<? foreach (@$forums as $forum) : ?>
	<li class="row forum <?= $forum->new && $forum->unread ? 'unread' : '' ?> <?= !$forum->unread ? 'read' : '' ?>">
		<? $icon = isset($forum->params['customization']['icon']) ? $forum->params['customization']['icon'] : 'default.png' ?>
		<? $iconclass = 'forum-icon-'.KInflector::underscore(str_replace('.', '', $icon)) ?>
		<style type="text/css">.row .<?= $iconclass ?> {background-image: url(<?= @$img('/forums/'.$icon) ?>);}</style>
		
		<dl class="icon <?= $iconclass ?>">
			<dt>
			    <span class="unread-indicator" title="<?= @text('COM_NINJABOARD_THERE_ARE_UNREAD_TOPICS_IN_THIS_FORUM') ?>"></span>
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
							? @text('COM_NINJABOARD_SUBFORUMS') 
							: @text('COM_NINJABOARD_SUBFORUM')
						?>
					</strong>
					<? foreach($forum->subforums as $subforum) : ?>
					<a href="<?= @route('view=forum&id=' . $subforum->id) ?>" class="subforum"><img src="<?= @$img('/16/page.png') ?>" alt="forum icon" class="icon" /><?= $subforum->title ?></a>
					<? endforeach ?>
				<? endif ?>
			</dt>
			<dd class="topics"><a href="#" class="separator"></a><?= $forum->topics ?> <dfn><?= @text('COM_NINJABOARD_TOPICS') ?></dfn></dd>
			<dd class="posts"><a href="#" class="separator"></a><?= $forum->posts ?> <dfn><?= @text('COM_NINJABOARD_POSTS') ?></dfn></dd>
			<dd class="lastpost"><a href="#" class="separator"></a><span class="lastpost">
			<? if ($forum->last_post_id) : ?>
				<dfn>Last post</dfn>
					<a class="ninjaboard-lastpost" href="<?= @route('view=topic&id=' . $forum->last_topic_id . '&post='.$forum->last_post_id) ?>#p<?= $forum->last_post_id ?>" title="<?= @escape($forum->subject) ?>"><?= $forum->subject ?></a><br />
					 <?= @text('COM_NINJABOARD_BY') ?>
					 <? if($forum->created_user_id) : ?>
					 <a href="<?= @route('view=person&id=' . $forum->created_user_id) ?>" class="username-coloured"><?= @escape($forum->last_post_username) ?></a>
					 <? else : ?>
					 <span class="username-coloured"><?= @escape($forum->last_post_username) ?></span>
					 <? endif ?>
					 <?= @ninja('date.html', array('date' => $forum->last_post_date)) ?>
			<? else : ?>
				<?= @text('COM_NINJABOARD_NO_POSTS') ?><br />&#160;
			<? endif ?>
			</span></dd>
		</dl>
	</li>
<? endforeach ?>