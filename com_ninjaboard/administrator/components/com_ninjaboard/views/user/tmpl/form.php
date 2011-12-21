<? /** $Id: form.php 1188 2010-12-08 22:56:12Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? @ninja('behavior.tooltip') ?>

<form action="<?= @route('id=' . @$user->id) ?>" method="post" id="<?= @id() ?>">
	<div class="col width-50">	
		<fieldset class="adminform ninja-form">
			<legend><?= @text('Ninjaboard User Details') ?></legend>
			<div class="element">
				<? @ninja('behavior.uploader') ?>
				<label for="avatarfile" class="key">
					<?= @text('Upload Avatar') ?>
				</label>
				<? @$size = \@getimagesize(JPATH_ROOT.'/'.@$user->avatar) ?>
				<div id="demo-portrait" style="background-image: url(<?= KRequest::root().@$user->avatar ?>); height: <?= @$size[1] ?>px; width: <?= @$size[0] ?>px;" class="value">
					<a href="#" id="select-0" title="Please upload only images, maximal 2 Mb filesize!">
						<?= @text('Upload new Avatar') ?>
					</a>
				</div>
				<!--<input disabled="true" type="file" name="avatarfile" id="avatarfile" size="40" value="" maxlength="50" class="inputbox value" /> Our file upload api isn't ready just yet-->
			</div>
			<!--<div class="element">
						<label for="avatarimage" class="key">
							<?= @text('User Avatar') ?>
						</label>
					<? if (@$avatar) : ?>
						<img src="<?= @$avatar ?>" id="avatarimage" class="value" />
					<? else : ?>
						<p class="value"><img style="vertical-align:middle;" src="<?= @mediaurl ?>/com_ninjaboard/images/avatar.png" title="This is the avatar shown until you upload your own." />This is your avatar until you upload one yourself.</p>
					<? endif ?>
			</div>-->
				<? if (@$avatar) : ?>
				<div class="element">
					<label for="deleteavatar">
						<?= @text('NB_DELETEAVATARIMAGE') ?>
					</label>
					<input type="checkbox" name="deleteavatar" value="1" />
					</div>
				<? endif ?>	
			<div class="element">
				<label for="signature" class="key">
					<?= @text('Forum Signature') ?>
				</label>
				<textarea name="signature" id="signature" rows="5" cols="40" class="inputbox value" placeholder="<?= @text('Singatures can have bbcode in them.') ?>"><?= $user->signature ?></textarea>
			</div>
		</fieldset>
		<fieldset class="adminform ninja-form">
			<legend>
				<?= @text('Joomla! User Details') ?>
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
			<legend><?= @text('Permissions') ?></legend>
			<? if($user->inherits) : ?>
			<div class="element">
				<p>
					<?= sprintf(@text('%s are %s by default.'), $user->name, $inherits) ?>
				</p>
				<p>
					<?= sprintf(@text('You can assign %s to other usergroups below'), $user->name) ?>
				</p>
			</div>
			<? endif ?>
			<div class="element">
				<label for="usergroup_id" class="key hasHint" title="<?= @text('You can select multiple usergroups.') ?>"><?= @text('Assign to usergroups') ?></label>
				<?= KFactory::get('admin::com.ninja.form.element.select.genericlist')->importXml(simplexml_load_string('
					<element name="usergroup" type="admin::com.ninja.form.select.genericlist" get="admin::com.ninjaboard.model.usergroups" class="value" multiple="true">
						<option value="0">- Inherit -</option>
					</element>
				'))->setValue($usergroups)->renderHtmlElement() ?>
			</div>
		</fieldset>
	</div>
	<!--<div class="col width-50">
		<fieldset class="adminform ninja-form">	
			<? $placeholder = false/*KFactory::get('admin::com.ninjaboard.view.profile_fields.html')->placeholder()*/ ?>
			<? if(!$placeholder) : ?>
				<legend><?= @text('Custom Profile Fields') ?></legend>
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
			<?//= KFactory::get('admin::com.ninjaboard.controller.profile_field')->layout('form')->user($user->id)->browse() ?>
		</fieldset>
	</div>-->
	<input type="hidden" name="params" value=" " />
</form>