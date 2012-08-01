<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="message-left <? if(!$message->is_read) echo 'message-unread' ?>"></div>
<div class="message-right">
	<div class="message-first-row">
		<span class="message-from">
			<? if (is_array($message->conversation_with)) : ?>
			
				<span class="recipients" title="<?= implode(', ', (array)$message->conversation_with) ?>"><?= implode(', ', (array)$message->conversation_with) ?></span>
			<? else : ?>

			<? endif ?>
			
		</span>
		<span class="message-header-date"><?= @ninja('date.html', array('date' => $message->created_on)) ?></span>
	</div>
	<div class="message-second-row message-subject">
		<?= $message->subject ?>
	</div>
	<div class="message-third-row">
		<?= strip_tags(@ninja('bbcode.parse', array('text' => $message->text))) ?>
	</div>
</div>