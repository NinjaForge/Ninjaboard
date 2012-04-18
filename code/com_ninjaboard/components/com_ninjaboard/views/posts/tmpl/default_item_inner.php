<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="wrap">
	<div class="section">
		<a class="ninjaboard-post-permalink" href="<?= @route('view=topic&id='.$topic->id.'&post='.$post->id) ?>#p<?= $post->id ?>">
			<img src="<?= @$img('/16/page.png') ?>" width="16" height="16" alt="<?= @text('COM_NINJABOARD_LINK_TO_THIS_POST') ?>" />
			<?= @ninja('date.html', array('date' => $post->created_on)) ?>
		</a>
		<div class="text"><?= @helper('ninja:helper.bbcode.parse', array('text' => $post->text)) ?></div>
		<? $images = $this->getService('com://admin/ninjaboard.model.attachments')->post($post->id)->getImages() ?>
		<? $files  = $this->getService('com://admin/ninjaboard.model.attachments')->post($post->id)->getFiles() ?>
		<? if($topic->attachment_permissions > 0) : ?>
			<ol class="images">
				<? foreach ($images as $attachment) : ?>
				<li>
					<a href="<?= @route('view=attachment&id='.$attachment->id.'&post='.$post->id.'&format=file') ?>" title="<?= @escape($attachment->name) ?>" target="_blank" rel="<?= $params['view_settings']['lightbox'] ?>[p<?= $post->id ?>]">
						<img src="<?= @route('view=attachment&id='.$attachment->id.'&post='.$post->id.'&format=file') ?>" alt="<?= $attachment->type ?>" style="max-width: 128px;" />
					</a>
				</li>
				<? endforeach ?>
			</ol>
			<ol class="attachments">
				<? foreach ($files as $attachment) : ?>
				<li style="display:block">
					<a href="<?= @route('view=attachment&id='.$attachment->id.'&post='.$post->id.'&format=file') ?>" class="<?= $attachment->type ?>" title="<?= @escape($attachment->type) ?>">
						<?= @escape($attachment->name) ?>
					</a>
				</li>
				<? endforeach ?>
			</ol>
		<? elseif(count($images) || count($files)) : ?>
			<h5 class="notice attachments-no-access"><?= @text('COM_NINJABOARD_YOU_DONT_HAVE_ACCESS_TO_VIEW_THE_ATTACHMENTS_IN_THIS_POST') ?></h5>
		<? endif ?>
		<div style="clear: both; display: block;"></div>
		<!-- .signature.footer still present because of browser cache. Browser cache is solved permanently in Ninjaboard 1.2 -->
		<div class="ninjaboard-post-footer signature footer">
		    <div class="ninjaboard-signature">
			    <?= @helper('ninja:helper.bbcode.parse', array('text' => @$post->signature)) ?>
			</div>
			
			<div class="ninjaboard-buttons actions toolbar">
			    <?= @quote_post_button($post->id) ?>
			    <? if( ( @$forum->post_permissions == 3 ) or ( @$forum->post_permissions == 2 && @$post->created_by == @$user->id ) ) : ?>
				    <?= @edit_post_button($post->id) ?>
				    <? if($topic->first_post_id != $post->id) echo $delete_post_button ?>
				<? endif ?>
			</div>
		</div>		
	</div>
	<div class="user sidebar">
		<? if($post->created_by) : ?>
		<a href="<?= @route('view=person&id='.$post->created_by) ?>"><h6 class="username"><?= $post->display_name ?></h6></a>
		<? else : ?>
		<h6 class="username"><?= $post->display_name ?></h6>
		<? endif ?>

		<? if($params['avatar_settings']['enable_avatar']) : ?>
		<?= @helper('com://site/ninjaboard.template.helper.avatar.image', array(
			'id'		=> $post->created_by
		)) ?>
		<? else : ?>
		<style type="text/css">
			#<?= @id('post-' . $post->id) ?> .wrap {
				min-height: 130px;
			}
		</style>
		<? endif ?>
		
		<? if($post->usergroups) : ?>
		<?= @template('com://site/ninjaboard.view.person.usergroups', array('usergroups' => $post->usergroups)) ?>
		<? endif ?>
		
		<? if($post->rank_title && $post->rank_icon && @$img('/rank/'.$post->rank_icon)) : ?>
		<strong class="rank"><?= @$post->rank_title ?></strong>
		<div class="rank_icon"><img src="<?= @$img('/rank/'.@$post->rank_icon) ?>" /></div>
		<? endif ?>
		<p class="posts"><strong><?= @text('COM_NINJABOARD_POSTS') ?></strong> <?= $post->person_posts ?></p>
		
		<!--<img src="<?= @$img('/usergroup/'.KInflector::underscore($post->usertype).'.png') ?>" />
		<img src="<?= @$img('/usergroup/'.KInflector::underscore($post->usertype).'.png') ?>" />-->
	</div>
</div>