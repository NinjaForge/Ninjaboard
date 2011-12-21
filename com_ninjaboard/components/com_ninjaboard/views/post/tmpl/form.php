<? /** $Id: form.php 1560 2011-02-16 14:21:11Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div id="ninjaboard" class="ninjaboard post-form<? if(!$topic->id || $topic->first_post_id == $post->id) : ?> new-topic-form<? endif ?>">
	<h2><?= $title ?></h2>
	<?= @render(@template('form_inner'), false, (array)$params['module']) ?>

	<? if(isset($topicreview)) : ?>
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