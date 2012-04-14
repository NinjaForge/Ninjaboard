<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<?= @template('com://site/ninjaboard.view.default.head') ?>

<link rel="stylesheet" href="/reveal.css" />
<link rel="stylesheet" href="/messages.css" />

<script type="text/javascript" src="/jquery/reveal.js"></script>
<script type="text/javascript" src="/jquery/messages.js"></script>
<script type="text/javascript">
ninja(function($){
    $('#ninjaboard-message-form').appendTo(document.body);
    var messages = $('#<?= @id('messages') ?>'), children = messages.children(), setHeight = function(){
        messages.children().height($(window).height() - 100);
        
    };
    messages.bind('loaded', setHeight);
    $(window).load(setHeight);
    $(window).resize(setHeight);
    
    var create = $('#<?= @id('new-message') ?>'),
        reply = $('#<?= @id('reply-message') ?>'),
        messageform = $('#ninjaboard-message-form'),
        title = messageform.find('.reply-to'),
        input = messageform.find('input[name=to]');

    messages.one('select', function(){
        reply.show();
    });
    messages.bind('select', function(event, data){
        reply.data('item', data);
        $('[data-conversation_id='+data.conversation_id+']').find('.message-unread').removeClass('message-unread');
    });
    
    create.click(function(){
        messageform.removeClass('replying').addClass('creating');
        input.val('');
    });
    reply.click(function(){

        messageform.addClass('replying').removeClass('creating');
        
        var data = reply.data('item');
        title.text(<?= json_encode(@text('Send {PERSON} a message:')) ?>.replace('{PERSON}', data.conversation_with.join(', ')));
        input.val(data.conversation_id);
    });


    messages.bind('loaded', function(){
        messages.find('.splitview-list').children().each(function(i, item){
            var row = $(item), max = row.find('.message-right').width(), from = row.find('.message-from'), date = row.find('.message-header-date');
            from.width(max - date.outerWidth());
        });
    });
});
</script>

<div id="ninjaboard" class="ninjaboard forums <?= $params['pageclass_sfx'] ?> <?= $params['style']['type'] ?> <?= $params['style']['border'] ?> <?= $params['style']['separators'] ?>">
    <a id="<?= @id('new-message') ?>" href="#" data-reveal-id="ninjaboard-message-form" class="ninjaboard-button ninjaboard-button-primary ninjaboard-button-new-message">
        <?= @text('New Message') ?>
    </a>
    <a id="<?= @id('reply-message') ?>" href="#" data-reveal-id="ninjaboard-message-form" class="ninjaboard-button ninjaboard-button-secondary ninjaboard-button-reply-message" style="display: none">
        <?= @text('Reply') ?>
    </a>
    <?= @helper('template.space') ?>
    <div id="ninjaboard-message-form" class="reveal-modal">
        <?= @template('com://site/ninjaboard.view.message.form') ?>
        <a class="close-reveal-modal">&#215;</a>
    </div>
    <?= @helper('splitview.messages', array(
        'id' => @id('messages'),
        'options' => array(
            'primary_key' => 'conversation_id',
            'detail_url'  => '?option=com_ninjaboard&view=messages&format=raw&layout=conversation'
        )
    )) ?>
</div>