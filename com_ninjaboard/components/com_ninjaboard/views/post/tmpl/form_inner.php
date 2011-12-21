<? /** $Id: form_inner.php 1768 2011-04-11 20:38:57Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/site.css" />
<link rel="stylesheet" href="/form.css" />
<link rel="stylesheet" href="/site.form.css" />
<link rel="stylesheet" href="/bbcode.css" />

<style type="text/css">
	
	#text_preview {	
		display:none;
		padding: 5px;
		border: 1px solid transparent;
		overflow: auto;
	}
	/* Hide any dropdown menus */
	.markItUpHeader.previewing ul li ul {
		opacity: 0;
		-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
		filter: alpha(opacity=0);
	}
	.markItUpHeader .markItUpButton, .markItUpHeader .markItUpSeparator {
		-webkit-transition: opacity 0s linear;
		-moz-transition: opacity 0s linear;
		transition: opacity 0s linear;
	}
	.markItUpHeader.previewing .markItUpButton, .markItUpHeader.previewing .markItUpSeparator {
		-webkit-transition: opacity 300ms linear;
		-moz-transition: opacity 300ms linear;
		transition: opacity 300ms linear;
		opacity: 0.2;
		-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=20)";
		filter: alpha(opacity=20);
	}
	.markItUpHeader.previewing .markItUpButton.button_preview {
		opacity: 1;
		-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
		filter: alpha(opacity=100);
	}
	#text.previewing {
		-webkit-user-select: none;
		color: transparent;
		opacity: 0.6;
		
		-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
		filter: alpha(opacity=0);
	}
	#text::-webkit-input-placeholder {
		color: currentcolor;
	}
</style>

<script type="text/javascript" src="/jquery.min.js"></script>
<script type="text/javascript" src="/jquery.markitup.pack.js"></script>

<? $bbcode = dirname($this->getView()->getIdentifier()->filepath).'/bbcode.js' ?>
<? if(file_exists($bbcode)) : ?>
<script type="text/javascript">
	<?= str_replace(
			array(
				'~/sets/bbcode/preview.php', 
				'magifier_zoom_out.png', 
				'magnifier.png'
			),
			array(
				'?option=com_ninjaboard&view=post&layout=preview&format=raw&tmpl=', 
				@$img('/bbcode/magifier_zoom_out.png'), 
				@$img('/bbcode/magnifier.png')
			),
			file_get_contents($bbcode)
		)
	?>
</script>
<? endif ?>



<script type="text/javascript">
	//jQuery version of keepalive
	setInterval(function(){
		jQuery.get(<?= json_encode(KRequest::url()->get(KHttpUri::PART_BASE ^ KHttpUri::PART_PATH).@route()) ?>);
	}, <?= 60000 * max(1, (int)JFactory::getApplication()->getCfg('lifetime')) ?>);

	jQuery(function($){
		var form = $('#<?= @id() ?>');
		$('#<?= @id('cancel') ?>', '#<?= @id('save') ?>').click(function(event){
			event.preventDefault();
			event.stopPropagation();
		});
		$('#<?= @id('cancel') ?>').one('click', function(event){
			event.preventDefault();
			event.stopPropagation();
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
				alert(<?= json_encode(@text("You need to enter text and a subject.")) ?>);
				return false; 
			}

			if(subject && !subject.value) {
				$('#<?= @id('save') ?>').one('click', save);
				alert(<?= json_encode(@text("You need to enter a subject.")) ?>);
				return false; 
			}
			
			if(text && !text.value) {
				$('#<?= @id('save') ?>').one('click', save);
				alert(<?= json_encode(@text("You need to enter some text.")) ?>);
				return false; 
			}
			
			form.append('<input type=\"hidden\" name=\"action\" value=\"save\" />')
				.trigger('submit');
		};
		$('#<?= @id('save') ?>').one('click', save);
		$('#text').markItUp(myBbcodeSettings);
		
		var slideDownAttachmentsHelp = function(){
			$('.attachments-extensions-help').slideDown();
		};
		$("#addFile").one('click', slideDownAttachmentsHelp).click(function () {
			
			var attachment = $('<li>'+
					'<a class="remove" href="#" title="<?= @text('Remove') ?>">&#10005;</a>'+
					<?= json_encode(@helper('site::com.ninjaboard.template.helper.attachment.input')) ?>+
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
		
		$('.image-select input')
		.bind('change', function(){
			$(this).siblings('label').removeClass('selected');
			$(this).next().addClass('selected');
		});
		
		//This is for IE, which don't respect the for attribute if the input isn't visible
		$('.image-select label').bind('click', function(){
			$(this).prev().attr('checked', true).trigger('change');
		});
		
		$('#text_preview').insertAfter($('#text'));
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
				<label class="key" for="subject"><?= @text('Subject') ?></label>
				<input type="text" name="subject" id="subject" class="inputbox required value" size="50" value="<?= @escape($post->subject) ?>" maxlength="100" />
			</div>
			<div class="element icons">
				<label class="key"><?= @text('Topic Icon') ?></label>
				<? $icon = isset($topic->params['customization']['icon']) ? $topic->params['customization']['icon'] : '32__default.png' ?>
				<?= @ninja('select.images', array('path' => JPATH_ROOT.'/media/com_ninjaboard/images/topic', 'name' => 'params[customization][icon]', 'attribs' => array('class' => 'value', 'id' => 'params[customization][icon]'), 'selected' => $icon, 'idtag' => false, 'translate' => true, 'vertical' => false, 'script' => false)) ?>
			</div>
		<? endif ?>
		<div class="element wider" style="text-align:center;position:relative">
			<textarea name="text" id="text" placeholder="<?= @text('Enter some text') ?>"><?= @escape($post->text) ?></textarea>
			<div id="text_preview"></div>
		</div>

		<div class="element attachment">
			<span class="key"><?= @text('Attachments') ?></span>
			<a href="#" class="button" id="addFile"><?= @text('Add File') ?></a>
			<ul id="attachments" style="margin-left:0;display:block">
				<? foreach (@$attachments as $attachment) : ?>
				<li style="display:block">
					<label for="<?= $id = @id('attachments-'.$attachment->id) ?>" title="<?= @text('Remove') ?>" ><?= @text('Remove') ?><input type="checkbox" name="attachments[]" value="<?= $attachment->id ?>" id="<?= $id ?>"></label>
					<?= $attachment->name ?>
				</li>
				<? endforeach ?>
			</ul>
			<div class="attachments-extensions-help">
				<div class="click-submit">
					<? if($post->id) : ?>
						<? $text = @text('Click «%s» in order to upload or delete attachments.') ?>
					<? else : ?>
						<? $text = @text('Click «%s» in order to upload attachments.') ?>
					<? endif ?>
					<?= sprintf($text, $create_topic_button_title) ?>
				</div>
				<?= @helper('site::com.ninjaboard.template.helper.attachment.upload_size_limit') ?>
				<?= @helper('site::com.ninjaboard.template.helper.attachment.extensions') ?>
			</div>
		</div>

		<div class="element"></div>

		<? if($notify !== -1) : ?>
			<div class="element">
				<label for="notify_on_reply_topic">
					<input type="hidden" name="notify_on_reply_topic" value="0" />
					<input type="checkbox" name="notify_on_reply_topic" <? if($notify) echo 'checked' ?> id="notify_on_reply_topic" value="1" />
					<?= @text('Notify me when a reply is posted') ?>
				</label>
			</div>
		<? endif ?>
		
		<div class="element footer">
			<div class="inner">
				<div id="<?= @id('save') ?>">
					<?= @$create_topic_button ?>
				</div>
				<? /*&#160;
				<div id="<?= @id('preview') ?>">
					<?= $preview_button ?>
				</div>*/ ?>
				&#160;
				<div id="<?= @id('cancel') ?>"><?= str_replace('$title', @text('Cancel'), @$topic->params['tmpl']['cancel_button']) ?></div>
			</div>
		</div>
	</fieldset>
	<input type="hidden" name="forum_id" value="<?= $topic->forum_id ?>" />
	<input type="hidden" name="ninjaboard_topic_id" value="<?= $topic->id ?>" />
</form>