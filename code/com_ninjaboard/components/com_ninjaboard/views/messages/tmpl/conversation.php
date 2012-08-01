<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? $me = $this->getService('com://admin/ninjaboard.model.people')->getMe() ?>
<? foreach($messages as $message) : ?>
    <? $class = $message->created_by == $me->id ? 'me' : 'other' ?>
    <div class="message by-<?= $class ?>" data-id="<?= $message->id ?>" data-conversation_id="<?= $message->conversation_id ?>">
        <div class="message-from">
            <a href="<?= @route('index.php?option=com_ninjaboard&view=person&format=html&id='.$message->created_by) ?>"><?= implode(', ', (array)$message->conversation_with) ?></a>
        </div> 
        <span class="message-date"><?= @ninja('date.html', array('date' => $message->created_on)) ?></span>
        <div class="message-content">
            <div class="message-text"><?= @ninja('bbcode.parse', array('text' => $message->text)) ?></div>
            <div class="message-footer">
            	<div class="ninjaboard-signature">
    			    <?= @helper('ninja:helper.bbcode.parse', array('text' => $message->signature)) ?>
    			</div>
            </div>

        </div>
    </div>
<? endforeach ?>