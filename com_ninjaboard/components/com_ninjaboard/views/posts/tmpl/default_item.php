<? /** $Id: default_item.php 1208 2010-12-12 20:47:00Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="<?= @id() ?>" id="<?= @id('post-' . @$post->id) ?>">
	<a name="p<?= @$post->id ?>"></a>
	<?= @render(@template('site::com.ninjaboard.views.posts.default_item_inner', array('post' => $post, 'topic' => $topic, 'user' => $user, 'forum' => $forum, 'delete_post_button' => $delete_post_button, 'params' => $forum->params)), false, $forum->params['module']) ?>
</div>