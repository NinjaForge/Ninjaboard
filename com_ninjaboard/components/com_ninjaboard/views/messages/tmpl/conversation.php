<? /** $Id: conversation.php 2460 2011-10-11 21:21:19Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? $me = $this->getService('com://admin/ninjaboard.model.people')->getMe() ?>

<? foreach($messages as $message) : ?>
<? $class = $message->created_by == $me->id ? 'me' : 'other' ?>
<div class="message by-<?= $class ?>" data-id="<?= $message->id ?>" data-conversation_id="<?= $message->conversation_id ?>">
    <div class="message-from">
        <?= implode(', ', (array)$message->conversation_with) ?>
    </div> 
    <div class="message-content">
        <div class="message-text"><?= @ninja('bbcode.parse', array('text' => $message->text)) ?></div>
        <div class="message-footer">
            <span class="message-date"><?= @ninja('date.html', array('date' => $message->created_on)) ?></span>
        </div>
    </div>
</div>
<? endforeach ?>