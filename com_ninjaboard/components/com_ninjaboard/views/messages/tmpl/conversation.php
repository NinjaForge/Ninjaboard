<? /** $Id: conversation.php 1846 2011-04-29 22:22:23Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? $me = KFactory::get('admin::com.ninjaboard.model.people')->getMe() ?>

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