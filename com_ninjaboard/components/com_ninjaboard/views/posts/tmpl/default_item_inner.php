<? /** $Id: default_item_inner.php 1374 2011-01-11 02:51:22Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="wrap">
	<div class="section">
		<a href="<?= @route('view=topic&id='.$topic->id.'&post='.$post->id) ?>#p<?= $post->id ?>">
			<img src="<?= @$img('/16/page.png') ?>" width="16" height="16" alt="<?= @text('Link to this post') ?>" />
			<?= @ninja('date.html', array('date' => $post->created_on)) ?>
		</a>
		<div class="text"><?= @ninja('bbcode.parse', array('text' => $post->text)) ?></div>
		<? $images = KFactory::tmp('admin::com.ninjaboard.model.attachments')->post($post->id)->getImages() ?>
		<? $files  = KFactory::tmp('admin::com.ninjaboard.model.attachments')->post($post->id)->getFiles() ?>
		<? if($topic->attachment_permissions > 0) : ?>
			<ol class="images">
				<? foreach ($images as $attachment) : ?>
				<li>
					<a href="<?= @route('view=attachment&id='.$attachment->id.'&post='.$post->id.'&format=file') ?>" title="<?= @escape($attachment->name) ?>" target="_blank" rel="lightbox[p<?= $post->id ?>]">
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
			<h5 class="notice attachments-no-access"><?= @text("You don't have access to view the attachments in this post.") ?></h5>
		<? endif ?>
		<div style="clear: both; display: block;"></div>
		<div class="footer signature">
			<?= @ninja('bbcode.parse', array('text' => @$post->signature)) ?>
			<? if( ( @$forum->post_permissions == 3 ) or ( @$forum->post_permissions == 2 && @$post->created_by == @$user->id ) ) : ?>
			<div class="footer-wrap">
				<div class="actions toolbar">
					<?= @edit_post_button($post->id) ?>
					<? if($topic->first_post_id != $post->id) echo $delete_post_button ?>
				</div>
			</div>
			<? endif ?>
		</div>		
	</div>
	<div class="user sidebar">
		<a href="<?= @route('view=person&id='.$post->created_by) ?>"><h6 class="username"><?= $post->display_name ?></h6></a>

		<? if($params['avatar_settings']['enable_avatar']) : ?>
		<?= @helper('site::com.ninjaboard.template.helper.avatar.image', array(
			'id'		=> $post->created_by
		)) ?>
		<? else : ?>
		<style type="text/css">
			#<?= @id('post-' . $post->id) ?> .wrap {
				min-height: 130px;
			}
		</style>
		<? endif ?>
		
		<? if($params['view_settings']['show_usergroups']) : ?>
		<?= @template('site::com.ninjaboard.view.person.usergroups', array('usergroups' => $post->usergroups)) ?>
		<? endif ?>
		
		<? if($post->rank_title && $post->rank_icon && @$img('/rank/'.$post->rank_icon)) : ?>
		<strong class="rank"><?= @$post->rank_title ?></strong>
		<div class="rank_icon"><img src="<?= @$img('/rank/'.@$post->rank_icon) ?>" /></div>
		<? endif ?>
		<p class="posts"><strong><?= @text('Posts:') ?></strong> <?= $post->person_posts ?></p>
		
		<!--<img src="<?= @$img('/usergroup/'.KInflector::underscore($post->usertype).'.png') ?>" />
		<img src="<?= @$img('/usergroup/'.KInflector::underscore($post->usertype).'.png') ?>" />-->
	</div>
</div>