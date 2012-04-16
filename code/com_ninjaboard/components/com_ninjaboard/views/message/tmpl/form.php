<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<?= @template('com://site/ninjaboard.view.default.head') ?>

<link rel="stylesheet" href="/form.css" />
<link rel="stylesheet" href="/site.form.css" />
<link rel="stylesheet" href="/bbcode.css" />
<link rel="stylesheet" href="/message.css" />

<script type="text/javascript" src="/jquery/jquery.markitup.pack.js"></script>
<script type="text/javascript" src="/jquery/bbcode.js"></script>


<?= @helper('behavior.keepalive') ?>

<script type="text/javascript">
	ninja(function($){
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

			if(text && !text.value) {
				$('#<?= @id('save') ?>').one('click', save);
				alert(<?= json_encode(@text("You need to enter some text.")) ?>);
				return false; 
			}
			
			form.append('<input type=\"hidden\" name=\"action\" value=\"save\" />')
				.trigger('submit');
		};
		$('#<?= @id('save') ?>').one('click', save);

		myBbcodeSettings.previewParserPath = '<?= @route("option=com_ninjaboard&view=post&layout=preview&format=raw&tmpl=") ?>';
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
				<?= @helper('behavior.editor', array('name' => 'text', 'placeholder' => 'Enter some text')) ?>
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