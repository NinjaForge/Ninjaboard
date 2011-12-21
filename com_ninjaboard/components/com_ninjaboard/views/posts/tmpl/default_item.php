<? /** $Id: default_item.php 2439 2011-09-01 11:53:24Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? /* Prepare the batch of css classes we wrap our posts with */ ?>
<? 
    $class[] = 'ninjaboard-post';
	$class[] = $params['style']['posts_wrap_style'] == 'extra' ? 'ninjaboard-block' : '';
	$class[] = @id();
	$class[] = 'poster-rank-'.@escape(KInflector::underscore($post->rank_title));
	foreach($post->usergroups as $usergroup)
	{
		$class[] = 'poster-usergroup-'.@escape(KInflector::underscore($usergroup->title));
	}
	if($topic->started_by == $post->created_by)
	{
		$class[] = 'poster-is-topic-creator';
	}
?>

<div class="<?= implode(' ', $class) ?>" id="<?= @id('post-' . $post->id) ?>">
	<a name="p<?= @$post->id ?>"></a>
	<?= @render(@template('com://site/ninjaboard.views.posts.default_item_inner', array('post' => $post, 'topic' => $topic, 'user' => $user, 'forum' => $forum, 'delete_post_button' => $delete_post_button, 'params' => $forum->params)), false, $forum->params['module']) ?>
</div>