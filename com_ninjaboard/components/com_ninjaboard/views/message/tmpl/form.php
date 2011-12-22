<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/site.css" />
<link rel="stylesheet" href="/form.css" />
<link rel="stylesheet" href="/site.form.css" />
<link rel="stylesheet" href="/bbcode.css" />
<link rel="stylesheet" href="/message.css" />

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
	#text {
	    resize: vertical;
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
	.element.name-to {
	    overflow: visible;
	}
	.textboxlist-autocomplete {
	    /* @TODO this is because of the MarkItUp editor */
	    z-index: 1;
	}
</style>

<script type="text/javascript" src="/jquery.min.js"></script>
<script type="text/javascript" src="/jquery.markitup.pack.js"></script>

<? $bbcode = dirname(KFactory::tmp('site::com.ninjaboard.view.post.form')->getIdentifier()->filepath).'/bbcode.js' ?>
<? if(file_exists($bbcode)) : ?>
<script type="text/javascript">
	<?= str_replace(
			array(
				'~/sets/bbcode/preview.php', 
				'magifier_zoom_out.png', 
				'magnifier.png'
			),
			array(
			    //@TODO move previewing into its own view
				@route('index.php?option=com_ninjaboard&view=post&layout=preview&format=raw&tmpl='), 
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
			var to = document.getElementById('to'), subject = document.getElementById('subject'), text = document.getElementById('text');

			if(to && !to.value) {
				$('#<?= @id('save') ?>').one('click', save);
				alert(<?= json_encode(@text("You need to enter a recipient.")) ?>);
				return false; 
			}

			/*if(subject && !subject.value && text && !text.value) {
				$('#<?= @id('save') ?>').one('click', save);
				alert(<?= json_encode(@text("You need to enter text and a subject.")) ?>);
				return false; 
			}

			if(subject && !subject.value) {
				$('#<?= @id('save') ?>').one('click', save);
				alert(<?= json_encode(@text("You need to enter a subject.")) ?>);
				return false; 
			}*/
			
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

<div class="ninjaboard">
	<form action="<?= @route('view=message') ?>" method="post" id="<?= @id() ?>" class="ninjaboard" enctype="multipart/form-data">
		<fieldset class="adminform ninja-form">
		    <h2 class="reply-to"></h2>
			<div class="element name-to">
				<label class="key" for="to"><?= @text('To') ?></label>
				<?= @ninja('behavior.textboxlist', array('name' => 'to', 'id' => 'to')) ?>
			</div>
			<div class="element wider" style="text-align:center;position:relative">
				<textarea name="text" id="text" placeholder="<?= @text('Enter some text') ?>"></textarea>
				<div id="text_preview"></div>
			</div>
	
			<div class="element"></div>
			
			<div class="element footer">
				<div class="inner">
					<div id="<?= @id('save') ?>">
						<?= str_replace('$title', JText::_('Send'), $params['tmpl']['create_topic_button']) ?>
					</div>
					<!--<div id="<?= @id('cancel') ?>"><?= str_replace('$title', @text('Cancel'), $params['tmpl']['cancel_button']) ?></div>-->
				</div>
			</div>
		</fieldset>
	</form>
</div>