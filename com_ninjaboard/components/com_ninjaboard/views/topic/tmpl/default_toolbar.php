<? /** $Id: default_toolbar.php 1351 2011-01-07 14:32:45Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if ($reply_topic_button or $move_topic_button or $delete_topic_button) : ?>
	<div class="start">
		<div class="action reply-topic"><?= $reply_topic_button ?></div>
		<div class="action"><?= $move_topic_button ?></div>
		<div class="action"><?= $delete_topic_button ?></div>
	</div>
<? endif ?>
<? if($watch_button) : ?>
	<div class="end">
		<?= @helper('site::com.ninjaboard.template.helper.behavior.watch') ?>
	</div>
<? endif ?>
<?= @helper('site::com.ninjaboard.template.helper.template.space', array('type' => 'toolbar')) ?>