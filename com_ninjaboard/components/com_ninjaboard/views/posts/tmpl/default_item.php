<? /** $Id: default_item.php 1577 2011-02-18 00:17:09Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? /* Prepare the batch of css classes we wrap our posts with */ ?>
<? 
	$class[] = @id();
	$class[] = 'poster-rank-'.@escape(KInflector::underscore($post->rank_title));
	foreach($post->usergroups as $usergroup)
	{
		$class[] = 'poster-usergroup-'.@escape(KInflector::underscore($usergroup->title));
	}
	if($topic->created_user_id == $post->created_by)
	{
		$class[] = 'poster-is-topic-creator';
	}
?>

<div class="<?= implode(' ', $class) ?>" id="<?= @id('post-' . $post->id) ?>">
	<a name="p<?= @$post->id ?>"></a>
	<?= @render(@template('site::com.ninjaboard.views.posts.default_item_inner', array('post' => $post, 'topic' => $topic, 'user' => $user, 'forum' => $forum, 'delete_post_button' => $delete_post_button, 'params' => $forum->params)), false, $forum->params['module']) ?>
</div>