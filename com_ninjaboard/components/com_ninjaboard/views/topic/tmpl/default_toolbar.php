<? /** $Id: default_toolbar.php 1579 2011-02-18 00:32:38Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if ($reply_topic_button or $move_topic_button or $delete_topic_button) : ?>
	<div class="start">
		<? if(isset($new_topic_button)) : ?><div class="action"><?= $new_topic_button ?> </div><? endif ?>
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