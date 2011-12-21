<? /** $Id: form.php 1662 2011-03-22 00:56:30Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div id="ninjaboard" class="ninjaboard post-form<? if(!$topic->id || $topic->first_post_id == $post->id) : ?> new-topic-form<? endif ?>">
	<h2><?= $title ?></h2>
	<?= @render(@template('form_inner'), false, (array)$params['module']) ?>

	<? if($topicreview) : ?>
		<div class="ninjaboard topicreview">
			<script type="text/javascript">
				jQuery(function($){
					$('.ninjaboard.topicreview a[href]').removeAttr('href');
				});
			</script>
			<h3><?= sprintf(@text('Topic review %s'), '('.@text('newest first').')') ?></h3>
			<?= $topicreview ?>
		</div>
	<? endif ?>
</div>