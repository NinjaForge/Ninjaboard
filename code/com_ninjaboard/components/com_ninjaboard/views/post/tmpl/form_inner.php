<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('com://site/ninjaboard.view.default.head') ?>
<link rel="stylesheet" href="/form.css" />
<link rel="stylesheet" href="/site.form.css" />
<link rel="stylesheet" href="/bbcode.css" />

<script type="text/javascript" src="/jquery/jquery.markitup.pack.js"></script>
<script type="text/javascript" src="/jquery/bbcode.js"></script>

<?= @helper('behavior.keepalive') ?>

<script type="text/javascript">
	ninja(function($){
		var form = $('#<?= @id() ?>');
		$('#<?= @id('cancel') ?>, #<?= @id('save') ?>').click(function(event){
			event.preventDefault();
			event.stopPropagation();
		});
		$('#<?= @id('cancel') ?>').one('click', function(event){
			event.preventDefault();
			event.stopPropagation();

            //@TODO refactor to use koowa.js
			form.append('<input type=\"hidden\" name=\"action\" value=\"cancel\" />')
			    .trigger('submit');
		});
		
		var save = function(event){
			event.preventDefault();
			event.stopPropagation();
			//Do basic forms validation for better usability
			var subject = document.getElementById('subject'), text = document.getElementById('text');

			if(subject && !subject.value && text && !text.value) {
				$('#<?= @id('save') ?>').one('click', save);
				alert(<?= json_encode(@text('COM_NINJABOARD_NB_DATA_VALIDATION_FAILED_SUBJECT_TEXT')) ?>);
				return false; 
			}

			if(subject && !subject.value) {
				$('#<?= @id('save') ?>').one('click', save);
				alert(<?= json_encode(@text('COM_NINJABOARD_NAPI_DATA_VALIDATION_FAILED_SUBJECT')) ?>);
				return false; 
			}
			
			if(text && !text.value) {
				$('#<?= @id('save') ?>').one('click', save);
				alert(<?= json_encode(@text('COM_NINJABOARD_NAPI_DATA_VALIDATION_FAILED_TEXT')) ?>);
				return false; 
			}
			
			form.append('<input type=\"hidden\" name=\"action\" value=\"save\" />')
				.trigger('submit');
		};
		$('#<?= @id('save') ?>').one('click', save);

		myBbcodeSettings.previewParserPath = '<?= @route("option=com_ninjaboard&view=post&layout=preview&format=raw&tmpl=") ?>';
		$('#text').markItUp(myBbcodeSettings);
		
		var slideDownAttachmentsHelp = function(){
			$('.attachments-extensions-help').slideDown();
		};
		$("#addFile").click(slideDownAttachmentsHelp).click(function() {
			
			var attachment = $('<li>'+
					'<a class="remove" href="#" title="<?= @text('COM_NINJABOARD_REMOVE') ?>">&#10005;</a>'+
					<?= json_encode(@helper('com://site/ninjaboard.template.helper.attachment.input')) ?>+
				'</li>')
				.hide();
			
			$("#attachments").append(attachment);

			attachment.slideDown();

			attachment.find('.remove')
				.click(function(){
					$(this).parent().slideUp(function(){
						$(this).remove();
					});
					if($("#attachments").children().length < 2) {
						$('.attachments-extensions-help').slideUp();
						$("#addFile").one('click', slideDownAttachmentsHelp);
					}
					return false;
				});
			
			attachment
				.find('input')
				.one('change', function(){
					$('.attachments-extensions-help .click-submit').slideDown();
				});
			
			return false;
		});
		
		$('.image-select input').change(function(){
    		$(this).siblings('label').removeClass('selected');
    		$(this).next().addClass('selected');
    	});
		
		//This is for IE, which don't respect the for attribute if the input isn't visible
		$('.image-select label').bind('click', function(){
			$(this).prev().attr('checked', true).trigger('change');
		});
		
		$('#<?= @id('preview') ?>').click(function(event){
			event.preventDefault();
			$('#markItUpText .button_preview').triggerHandler('mousedown');
		});
	});
</script>
<form action="<?= @route('topic='.$topic->id.'&forum='.$topic->forum_id.'&id='.$post->id.'&layout=') ?>" method="post" id="<?= @id() ?>" class="ninjaboard" enctype="multipart/form-data">
	<fieldset class="adminform ninja-form">
		<? if(!$topic->id || $topic->first_post_id == $post->id) : ?>
			<div class="element subject">
				<label class="key" for="subject"><?= @text('COM_NINJABOARD_SUBJECT') ?></label>
				<input type="text" name="subject" id="subject" class="inputbox required value" size="50" value="<?= @escape($post->subject) ?>" maxlength="100" />
			</div>
			<div class="element icons">
				<label class="key"><?= @text('COM_NINJABOARD_TOPIC_ICON') ?></label>
				<? $icon = isset($topic->params['customization']['icon']) ? $topic->params['customization']['icon'] : '32__default.png' ?>
				<?= @ninja('select.images', array('path' => JPATH_ROOT.'/media/com_ninjaboard/images/topic', 'name' => 'params[customization][icon]', 'attribs' => array('class' => 'value', 'id' => 'params[customization][icon]'), 'selected' => $icon, 'idtag' => false, 'translate' => true, 'vertical' => false, 'script' => false)) ?>
			</div>
		<? endif ?>
		<div class="element wider" style="text-align:center;position:relative">
			<?= @helper('behavior.editor', array('name' => 'text', 'placeholder' => 'Enter some text', 'value' => $post->text)) ?>
		</div>

		<? if($topic->attachment_permissions > 1 && ($topic->attachment_settings || $forum->post_permissions > 2)) : ?>
		<div class="element attachment">
			<span class="key"><?= @text('COM_NINJABOARD_ATTACHMENTS') ?></span>
			<a href="#" class="button" id="addFile"><?= @text('COM_NINJABOARD_ADD_FILE') ?></a>
			<ul id="attachments" style="margin-left:0;display:block">
				<? foreach (@$attachments as $attachment) : ?>
				<li style="display:block">
					<label for="<?= $id = @id('attachments-'.$attachment->id) ?>" title="<?= @text('COM_NINJABOARD_REMOVE') ?>" ><?= @text('COM_NINJABOARD_REMOVE') ?><input type="checkbox" name="attachments[]" value="<?= $attachment->id ?>" id="<?= $id ?>"></label>
					<?= $attachment->name ?>
				</li>
				<? endforeach ?>
			</ul>
			<div class="attachments-extensions-help">
				<div class="click-submit">
					<? if($post->id) : ?>
						<? $text = @text('COM_NINJABOARD_CLICK_%S_IN_ORDER_TO_UPLOAD_OR_DELETE_ATTACHMENTS') ?>
					<? else : ?>
						<? $text = @text('COM_NINJABOARD_CLICK_%S_IN_ORDER_TO_UPLOAD_ATTACHMENTS') ?>
					<? endif ?>
					<?= sprintf($text, $create_topic_button_title) ?>
				</div>
				<?= @helper('com://site/ninjaboard.template.helper.attachment.upload_size_limit') ?>
				<?= @helper('com://site/ninjaboard.template.helper.attachment.extensions') ?>
			</div>
		</div>
		<? endif ?>

		<? if($forum->topic_permissions > 2 && (!$topic->id || $topic->first_post_id == $post->id)) : ?>
			<div class="element">
				<label for="sticky">
					<input type="hidden" name="sticky" value="0" />
					<input type="checkbox" name="sticky" <? if($topic->sticky) echo 'checked' ?> id="sticky" value="1" />
					<?= @text('COM_NINJABOARD_STICKY_TOPIC') ?>
				</label>
			</div>
		<? endif ?>

		<div class="element"></div>

		<? if($notify !== -1) : ?>
			<div class="element">
				<label for="notify_on_reply_topic">
					<input type="hidden" name="notify_on_reply_topic" value="0" />
					<input type="checkbox" name="notify_on_reply_topic" <? if($notify) echo 'checked' ?> id="notify_on_reply_topic" value="1" />
					<?= @text('COM_NINJABOARD_NOTIFY_ME_WHEN_A_REPLY_IS_POSTED') ?>
				</label>
			</div>
		<? endif ?>
		
		<div class="element footer">
			<div class="inner">
				<div id="<?= @id('save') ?>">
					<?= @$create_topic_button ?>
				</div>
				<div id="<?= @id('cancel') ?>"><?= str_replace('$title', @text('COM_NINJABOARD_CANCEL'), @$topic->params['tmpl']['cancel_button']) ?></div>
			</div>
		</div>
	</fieldset>
	<input type="hidden" name="forum_id" value="<?= $topic->forum_id ?>" />
	<input type="hidden" name="ninjaboard_topic_id" value="<?= $topic->id ?>" />
</form>