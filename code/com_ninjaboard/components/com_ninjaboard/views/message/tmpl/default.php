<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<h3 class="message-subject"><?= $message->subject ?></h3>
<div class="message-header">

	<div class="message-header-collapsed">
		<span class="message-header-disclosure-triangle">▶</span>From <a href="#"><?= $message->from ?></a> to me <span class="message-header-date"><?= @ninja('date.html', array('date' => $message->created_on, 'html' => false)) ?></span>
	</div>
	<div class="message-header-expanded">
		<span class="message-header-disclosure-triangle">▼</span>
		<div class="message-header-from">
			<span class="message-label"><?= @text('COM_NINJABOARD_FROM') ?></span><a href="#"><?= $message->from ?></a>
		</div>
		<div class="message-header-to">
			<span class="message-label"><?= @text('COM_NINJABOARD_TO') ?></span><a href="#"><?= $me->display_name ?></a>
		</div>
		<div class="message-header-date">
			<span class="message-label"><?= @text('COM_NINJABOARD_DATE') ?></span><?= @date(array('date' => $message->created_on, 'format' => @text('DATE_FORMAT_LC2='))) ?>
		</div>
	</div>
</div>
<div class="message-body"><?= @ninja('bbcode.parse', array('text' => $message->text)) ?></div>