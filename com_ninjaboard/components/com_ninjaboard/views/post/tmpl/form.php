<? /** $Id: form.php 1358 2011-01-10 19:23:59Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div id="ninjaboard" class="ninjaboard post-form<? if(!$topic->id || $topic->first_post_id == $post->id) : ?> new-topic-form<? endif ?>">
	<h2><?= $title ?></h2>
	<?= @render(@template('form_inner'), false, $params['module']) ?>

	<? if($topic->id && !isset($notopicreview)) : ?>
		<div class="ninjaboard topicreview">
			<script type="text/javascript">
				jQuery.noConflict();
				jQuery(function($){
					$('.ninjaboard.topicreview a[href]').removeAttr('href');
				});
			</script>
			<h3><?= sprintf(@text('Topic review %s'), '('.@text('newest first').')') ?></h3>
			<?= KFactory::tmp('site::com.ninjaboard.controller.post')
				->sort('created_on')
				->direction('desc')
				->limit(5)
				->offset(0)
				->post(false)
				->topic($topic->id)
				->layout('default')
				->browse() ?>
		</div>
	<? endif ?>
</div>