<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div id="ninjaboard" class="ninjaboard post-form<? if(!$topic->id || $topic->first_post_id == $post->id) : ?> new-topic-form<? endif ?>">
	<h1><?= $title ?></h1>
	<?= @render(@template('form_inner'), false, (array)$params['module']) ?>

	<? if($topicreview) : ?>
		<div class="ninjaboard topicreview">
			<script type="text/javascript">
				window.addEvent('domready', function(){
					$$('.ninjaboard.topicreview a[href]').removeProperty('href');
				});
			</script>
			<h3><?= sprintf(@text('Topic review %s'), '('.@text('newest first').')') ?></h3>
			<?= $topicreview ?>
		</div>
	<? endif ?>
</div>