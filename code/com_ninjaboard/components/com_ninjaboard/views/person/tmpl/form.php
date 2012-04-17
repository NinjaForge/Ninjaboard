<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('com://site/ninjaboard.view.default.head') ?>
<link rel="stylesheet" href="/form.css" />
<link rel="stylesheet" href="/site.form.css" />

<script type="text/javascript">
	ninja(function($){
		var form = $('#<?= @id() ?>');
		$('#<?= @id('cancel') ?>').click(function(event){
			event.preventDefault();
			event.stopPropagation();
			form.append('<input type=\"hidden\" name=\"action\" value=\"cancel\" />')
				.trigger('submit');
		});
		
		$('#<?= @id('save') ?>').click(function(event){
			event.preventDefault();
			event.stopPropagation();
			form.append('<input type=\"hidden\" name=\"action\" value=\"apply\" />')
				.trigger('submit');
		});
		$('.change-image').click(function(event){
			event.preventDefault();
			$('.avatar.upload').show();
			$('.avatar.options, img.avatar, a.avatar').hide();
		});
		$('.delete-image').click(function(event){
			event.preventDefault();
			$('.avatar.delete').show();
			$('.avatar.options, img.avatar, a.avatar').hide();
			$('.avatar.delete input').attr('disabled', false);
		});
		$('.cancel-delete').click(function(event){
			event.preventDefault();
			$('.avatar.delete').hide();
			$('.avatar.options, img.avatar, a.avatar').css('display', 'inline-block');
			$('.avatar.delete input').attr('disabled', 'disabled');
		});
		$('.cancel-upload').click(function(event){
			event.preventDefault();
			$('.avatar.upload').hide();
			$('.avatar.options, img.avatar, a.avatar').css('display', 'inline-block');
		});
	});
</script>

<style type="text/css">
	.ninjaboard .element .avatar.options {
		top: 10px;
		<? if(JFactory::getDocument()->getDirection() == 'rtl') : ?>
		right: <?= max((int)$params['avatar_settings']['large_thumbnail_width'], 0) + 173 ?>px;
		<? else : ?>
		left: <?= max((int)$params['avatar_settings']['large_thumbnail_width'], 0) + 173 ?>px;
		<? endif ?>
	}
</style>

<div id="ninjaboard" class="person">
	<h1><?= $title ?></h1>
	<form action="<?= @route('id='.$person->id) ?>" method="post" id="<?= @id() ?>" class="ninjaboard" enctype="multipart/form-data">
		<fieldset class="ninja-form outer">
			<fieldset>
				<!--<legend><?= @text('COM_NINJABOARD_PROFILE') ?></legend>-->
				<h2><?= @text('COM_NINJABOARD_PROFILE') ?></h2>
				
				<? if($params['avatar_settings']['enable_avatar']) : ?>
				<div class="element avatar">
					<label class="key">
						<?= @text("Avatar") ?>
					</label>
					<?= @helper('com://site/ninjaboard.template.helper.avatar.image', array(
						'id'		=> $person->id,
						'class'		=> 'avatar value'
					)) ?>
					
					<span class="avatar options">
						<a href="#" class="change-image"><?= @text('COM_NINJABOARD_CHANGE_IMAGE') ?></a>
						<a href="#" class="delete-image"><?= @text('COM_NINJABOARD_DELETE_THIS_IMAGE') ?></a>
					</span>
					
					<span class="avatar upload">
						<?= @helper('com://site/ninjaboard.template.helper.avatar.input') ?>
						<small>
							<?= @helper('com://site/ninjaboard.template.helper.avatar.upload_size_limit', array('params' => $params)) ?>
							<span class="avatar-image-extensions">
								<?= @helper('com://site/ninjaboard.template.helper.avatar.extensions') ?>
							</span>
						</small>
						<a href="#" class="cancel-upload"><?= @text('COM_NINJABOARD_CANCEL_UPLOAD') ?></a>
					</span>
					
					<span class="avatar delete">
						<strong><?= @text("Click save if you're sure you want to delete this picture.") ?></strong>
						<input type="hidden" name="avatar" disabled="disabled" />
						<a href="#" class="cancel-delete"><?= @text('COM_NINJABOARD_DO_NOT_DELETE_THIS_IMAGE') ?></a>
					</span>
				</div>
				<? endif ?>
				
				<div class="element">
					<label class="key" for="signature">
						<?= @text("Signature") ?>
					</label>
					<textarea name="signature" id="signature" placeholder="<?= @text('COM_NINJABOARD_THIS_IS_YOUR_FORUM_SIGNATURE…') ?>" class="value"><?= @escape($person->signature) ?></textarea>
				</div>
			</fieldset>
			
			<? if($params->view_settings->change_display_name != 'no') : ?>
			<fieldset>
				<h2><?= @text('COM_NINJABOARD_VISIBILITY') ?></h2>
				
					<? if($params->view_settings->change_display_name == 'yes') : ?>
						<?= @template('form_change_display_name') ?>
					<? else : ?>
						<?= @template('form_custom_display_name') ?>
					<? endif ?>
				
			</fieldset>
			<? endif ?>
			
			<? if($params->email_notification_settings->enable_email_notification) : ?>
			<fieldset>
				<h2><?= @text('COM_NINJABOARD_EMAIL_NOTIFICATIONS') ?></h2>
				<div class="element">
					<div class="key">
						<input type="hidden" name="notify_on_create_topic" value="0" />
						<input type="checkbox" name="notify_on_create_topic" <? if($person->notify_on_create_topic) echo 'checked' ?> id="notify_on_create_topic" value="1" />
					</div>
					<label for="notify_on_create_topic">
						<?= @text('COM_NINJABOARD_SUBSCRIBE_TO_THREADS_I_CREATE') ?>
					</label>
				</div>
				<div class="element">
					<div class="key">
						<input type="hidden" name="notify_on_reply_topic" value="0" />
						<input type="checkbox" name="notify_on_reply_topic" <? if($person->notify_on_reply_topic) echo 'checked' ?> id="notify_on_reply_topic" value="1" />
					</div>
					<label for="notify_on_reply_topic">
						<?= @text('COM_NINJABOARD_SUBSCRIBE_TO_THREADS_I_REPLY_TO') ?>
					</label>
				</div>
				<? if($params->messaging_settings->enable_messaging) : ?>
				<div class="element">
					<div class="key">
						<input type="hidden" name="notify_on_private_message" value="0" />
						<input type="checkbox" name="notify_on_private_message" <? if($person->notify_on_private_message) echo 'checked' ?> id="notify_on_private_message" value="1" />
					</div>
					<label for="notify_on_private_message">
						<?= @text('COM_NINJABOARD_NOTIFY_ME_WHEN_I_RECEIVE_A_PRIVATE_MESSAGE') ?>
					</label>
				</div>
				<? endif ?>
			</fieldset>
			<? endif ?>
			
			<div class="element footer">
				<div class="inner">
					<div id="<?= @id('save') ?>">
						<?= $save_button ?>
					</div>
					&#160;
					<div id="<?= @id('cancel') ?>"><?= str_replace('$title', @text('COM_NINJABOARD_CANCEL'), $params['tmpl']['cancel_button']) ?></div>
				</div>
			</div>
		</fieldset>
	</form>
</div>