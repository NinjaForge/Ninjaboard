<? /** $Id: quick.php 1355 2011-01-10 18:33:32Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<style type="text/css">
	#<?= @id('cancel') ?> {
		display: none;
	}
</style>

<div class="ninjaboard post-form<? if(!$topic->id || $topic->first_post_id == $post->id) : ?> new-topic-form<? endif ?>">
	<?= @render(@template('form_inner'), @text('Post a reply'), $params['module']) ?>
</div>