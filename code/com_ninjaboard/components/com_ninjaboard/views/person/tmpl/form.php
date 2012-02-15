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
				<!--<legend><?= @text('Profile') ?></legend>-->
				<h2><?= @text('Profile') ?></h2>
				
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
						<a href="#" class="change-image"><?= @text('Change image') ?></a>
						<a href="#" class="delete-image"><?= @text('Delete this image') ?></a>
					</span>
					
					<span class="avatar upload">
						<?= @helper('com://site/ninjaboard.template.helper.avatar.input') ?>
						<small>
							<?= @helper('com://site/ninjaboard.template.helper.avatar.upload_size_limit', array('params' => $params)) ?>
							<span class="avatar-image-extensions">
								<?= @helper('com://site/ninjaboard.template.helper.avatar.extensions') ?>
							</span>
						</small>
						<a href="#" class="cancel-upload"><?= @text('Cancel upload') ?></a>
					</span>
					
					<span class="avatar delete">
						<strong><?= @text("Click save if you're sure you want to delete this picture.") ?></strong>
						<input type="hidden" name="avatar" disabled="disabled" />
						<a href="#" class="cancel-delete"><?= @text('Do not delete this image.') ?></a>
					</span>
				</div>
				<? endif ?>
				
				<div class="element">
					<label class="key" for="signature">
						<?= @text("Signature") ?>
					</label>
					<textarea name="signature" id="signature" placeholder="<?= @text('This is your forum signature…') ?>" class="value"><?= @escape($person->signature) ?></textarea>
				</div>
			</fieldset>
			
			<? if($params->view_settings->change_display_name != 'no') : ?>
			<fieldset>
				<h2><?= @text('Visibility') ?></h2>
				
					<? if($params->view_settings->change_display_name == 'yes') : ?>
						<?= @template('form_change_display_name') ?>
					<? else : ?>
						<?= @template('form_custom_display_name') ?>
					<? endif ?>
				
			</fieldset>
			<? endif ?>
			
			<? if($params->email_notification_settings->enable_email_notification) : ?>
			<fieldset>
				<h2><?= @text('Email Notifications') ?></h2>
				<div class="element">
					<div class="key">
						<input type="hidden" name="notify_on_create_topic" value="0" />
						<input type="checkbox" name="notify_on_create_topic" <? if($person->notify_on_create_topic) echo 'checked' ?> id="notify_on_create_topic" value="1" />
					</div>
					<label for="notify_on_create_topic">
						<?= @text('Subscribe to threads I create') ?>
					</label>
				</div>
				<div class="element">
					<div class="key">
						<input type="hidden" name="notify_on_reply_topic" value="0" />
						<input type="checkbox" name="notify_on_reply_topic" <? if($person->notify_on_reply_topic) echo 'checked' ?> id="notify_on_reply_topic" value="1" />
					</div>
					<label for="notify_on_reply_topic">
						<?= @text('Subscribe to threads I reply to') ?>
					</label>
				</div>
				<? if($params->messaging_settings->enable_messaging) : ?>
				<div class="element">
					<div class="key">
						<input type="hidden" name="notify_on_private_message" value="0" />
						<input type="checkbox" name="notify_on_private_message" <? if($person->notify_on_private_message) echo 'checked' ?> id="notify_on_private_message" value="1" />
					</div>
					<label for="notify_on_private_message">
						<?= @text('Notify me when I receive a private message') ?>
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
					<div id="<?= @id('cancel') ?>"><?= str_replace('$title', @text('Cancel'), $params['tmpl']['cancel_button']) ?></div>
				</div>
			</div>
		</fieldset>
	</form>
</div>