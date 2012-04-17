<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.form.head') ?>

<? @ninja('behavior.tooltip') ?>
<? $params = $this->getService('com://admin/ninjaboard.model.settings')->getParams() ?>

<style type="text/css">
.ninja-form .element.avatar {
	min-height: 37px;
}
.ninja-form .element .avatar.value {
	margin-right: 10px;
}
.ninja-form .element.avatar .avatar.upload,
.ninja-form .element.avatar .avatar.delete {
	display: none;
}
.ninja-form .element.avatar .avatar.options {
	display: inline-block;
}
.ninja-form .element .avatar.options {
	display: none;
}
.ninja-form .element .avatar.options {
	display: inline-block;
	width: 250px;
	position: absolute;
}
.ninja-form .element .avatar a {
	display: block;
}
.ninja-form .element .avatar-image-extensions,
.ninja-form .element .allowed-file-extensions {
	text-transform: uppercase;
}
.ninja-form .element .avatar.options {
	top: 10px;
	left: <?= max((int)$params['avatar_settings']['large_thumbnail_width'], 0) + 173 ?>px;
}
</style>

<script type="text/javascript">
	window.addEvent('domready', function(){
		var form = $('<?= @id() ?>');
		$$('.change-image').addEvent('click', function(event){
			event.preventDefault();
			$$('.avatar.upload').show();
			$$('.avatar.options, img.avatar, a.avatar').hide();
		});
		$$('.delete-image').addEvent('click', function(event){
			event.preventDefault();
			$$('.avatar.delete').show();
			$$('.avatar.options, img.avatar, a.avatar').hide();
			$$('.avatar.delete input').set('disabled', false);
		});
		$$('.cancel-delete').addEvent('click', function(event){
			event.preventDefault();
			$$('.avatar.delete').hide();
			$$('.avatar.options, img.avatar, a.avatar').setStyle('display', 'inline-block');
			$$('.avatar.delete input').set('disabled', 'disabled');
		});
		$$('.cancel-upload').addEvent('click', function(event){
			event.preventDefault();
			$$('.avatar.upload').hide();
			$$('.avatar.options, img.avatar, a.avatar').setStyle('display', 'inline-block');
		});
	});
</script>

<form action="<?= @route('id=' . @$user->id) ?>" method="post" id="<?= @id() ?>" class="-koowa-form" enctype="multipart/form-data">
	<div class="col width-50">	
		<fieldset class="adminform ninja-form">
			<legend><?= @text('COM_NINJABOARD_NINJABOARD_USER_DETAILS') ?></legend>
			<div class="element avatar">
				<label class="key">
					<?= @text("Avatar") ?>
				</label>
				<?= @helper('com://site/ninjaboard.template.helper.avatar.image', array(
					'id'		=> $user->id,
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
			<div class="element">
				<label for="signature" class="key">
					<?= @text('COM_NINJABOARD_FORUM_SIGNATURE') ?>
				</label>
				<textarea name="signature" id="signature" rows="5" cols="40" class="inputbox value" placeholder="<?= @text('COM_NINJABOARD_SIGNATURES_CAN_HAVE_BBCODE_IN_THEM') ?>"><?= $user->signature ?></textarea>
			</div>
		</fieldset>
		<fieldset class="adminform ninja-form">
			<legend>
				<?= @text('COM_NINJABOARD_JOOMLA_USER_DETAILS') ?>
			</legend>
				<? foreach (@$joomla as $key => $value) : ?>
				<div class="element">
					<div class="key"><?= @text($key) ?></div>
					<div><?= @$user->$value ?>
					<? if (next(@$ifedit)) : ?>
						<div style="position:absolute;right:5px;top:0;"><div class="button2-left" style="float:none;display:inline-block;"><div class="page" style="float:none;">					<a style="float:none;" target="_blank" href="<?= @route('option=com_users&view=user&task=edit&cid[]='.@$user->id) ?>">&#9998; <?= JText::sprintf('Edit %s in the Joomla! User Manager', @$user->username) ?></a></div></div>
					</div>
					<? endif ?>
					</div>
				</div>
				<? endforeach ?>
		</fieldset>
	</div>
	<div class="col width-50">
		<fieldset class="adminform ninja-form">	
			<legend><?= @text('COM_NINJABOARD_PERMISSIONS') ?></legend>
			<? if($user->inherits) : ?>
			<div class="element">
				<p>
					<?= sprintf(@text('COM_NINJABOARD_%S_ARE_%S_BY_DEFAULT'), $user->name, $inherits) ?>
				</p>
				<p>
					<?= sprintf(@text('COM_NINJABOARD_YOU_CAN_ASSIGN_%S_TO_OTHER_USERGROUPS_BELOW'), $user->name) ?>
				</p>
			</div>
			<? endif ?>
			<div class="element">
				<label for="usergroup_id" class="key hasHint" title="<?= @text('COM_NINJABOARD_YOU_CAN_SELECT_MULTIPLE_USERGROUPS') ?>"><?= @text('COM_NINJABOARD_ASSIGN_TO_USERGROUPS') ?></label>
				<?= $this->getService('ninja:form.element.select.genericlist')->importXml(simplexml_load_string('
					<element name="usergroup" type="ninja:form.select.genericlist" get="com://admin/ninjaboard.model.usergroups" class="value" multiple="true">
						<option value="0">- Inherit -</option>
					</element>
				'))->setValue($usergroups)->renderHtmlElement() ?>
			</div>
		</fieldset>
	</div>
	<!--<div class="col width-50">
		<fieldset class="adminform ninja-form">	
			<? $placeholder = false/*$this->getService('com://admin/ninjaboard.view.profile_fields.html')->placeholder()*/ ?>
			<? if(!$placeholder) : ?>
				<legend><?= @text('COM_NINJABOARD_CUSTOM_PROFILE_FIELDS') ?></legend>
				<a href="<?= @route('view=profile_field') ?>"></a>
				<div style="position:relative;height:30px">
					<div style="position:absolute;right:5px;top:0;">
						<div class="button2-left" style="float:none;display:inline-block;">
							<div class="page" style="float:none;">
								<a style="float:none;" href="<?= @route('view=profile_field') ?>">Add Profile Field…</a>
							</div>
						</div>
					</div>
				</div>
			<? else : ?>
				<style type="text/css">
					.placeholder {
						min-height: 30em;
					}
					.placeholder .title {
						top: 80px;
					}
					.placeholder a, .placeholder a:visited, .placeholder a:link {
						margin: 170px auto 20px;
					}
				</style>
				<?= $placeholder ?>
			<? endif ?>
			<?//= $this->getService('com://admin/ninjaboard.controller.profile_field')->layout('form')->user($user->id)->display() ?>
		</fieldset>
	</div>-->
	<input type="hidden" name="params" value=" " />
</form>