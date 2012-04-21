<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<style type="text/css">
	#<?= @id('cancel') ?> {
		display: none;
	}
</style>

<div class="ninjaboard post-form<? if(!$topic->id || $topic->first_post_id == $post->id) : ?> new-topic-form<? endif ?>">
	<?= @render(@template('form_inner'), @text('COM_NINJABOARD_POST_A_REPLY'), $params['module']) ?>
</div>