<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<li class="ninjaboard-latest-post">
	
		<dl class="ninjaboard-latest-post-desc">
			<dd class="ninjaboard-latest-poster">
				<a href="<?= @route('view=person&id='.$post->created_by) ?>"><?= $post->display_name ?></a>
			</dd>
			<dd class="ninjaboard-latest-subject<? if (!$collapse_content) : ?>-long<? endif; ?>">
			    <a href="<?= @route('view=topic&id=' . $post->ninjaboard_topic_id . '&post=' . $post->id . '#p' . $post->id) ?>" title="<?= @escape($post->subject) ?>">
					<?= $post->subject ?>
				</a>
			</dd>
			<? if ($collapse_content) : ?>
			<dd class="ninjaboard-latest-preview" title="Post Preview - Click to Expand/Collapse this Post Content">
				<div class="ninjaboard-latest-preview-text<?= $module_id ?>"><?= $post->text ?></div>
			</dd>
			<? endif; ?>
			<dd class="ninjaboard-latest-date">
			    <a href="<?= @route('view=topic&id=' . $post->ninjaboard_topic_id . '&post=' . $post->id . '#p' . $post->id) ?>">
					<img src="<?= @$img('/16/page.png') ?>" width="16" height="16" alt="<?= @text('COM_NINJABOARD_LINK_TO_THIS_POST') ?>" />
					<?= @ninja('date.html', array('date' => $post->created_on)) ?>
				</a>
			</dd>
		</dl>

		<div id="ninjaboard-latest-post-content" class="ninjaboard-latest-post-content<?= $module_id ?>">
			<? if($display_avatar) : ?>
			<div class="ninjaboard-latest-post-user">
				<?= @helper('com://site/ninjaboard.template.helper.avatar.image', array(
					'class' => 'ninjaboard-latest-post-avatar',
					'thumbnail' => 'small',
					'avatarurl' => @route('index.php?option=com_ninjaboard&view=avatar&id='.$post->created_by.'&thumbnail=small'),
					'profileurl' => @route('index.php?option=com_ninjaboard&view=person&id='.$post->created_by)
				)) ?>
			</div>
			<? endif; ?>
			<div class="ninjaboard-latest-post-text">
				<?= @ninja('bbcode.parse', array('text' => $post->text)) ?>
			</div>
		</div>
		
</li>