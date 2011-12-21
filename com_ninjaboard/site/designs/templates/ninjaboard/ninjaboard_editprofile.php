<?php defined('_JEXEC') or die('Restricted access'); 

	$this->document->addScriptDeclaration('
	<!--
		Window.onDomReady(function(){
			document.formvalidator.setHandler("passverify", function (value) {
				return ($("password").value == value);
			});
		});
	//-->
	');

	$this->loadbar = 'editprofile';
?>

	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>


	<form action="index.php" method="post" id="josForm" name="josForm" class="form-validate" enctype="multipart/form-data">
		<div id="nbProfile">
			<fieldset class="nbOuter">
				<legend><?php echo JText::_('NB_MYDETAILS'); ?></legend>
				<dl>
					<dt><label for="username"><?php echo JText::_('NB_LOGIN'); ?><span>*</span></label></dt></dt>
					<dd><input type="text" name="username" id="username" class="nbInputBox required validate-username" size="40" value="<?php echo $this->ninjaboardUser->get('username'); ?>" maxlength="50" readonly="true" /></dd>
					<dt><label for="name"><?php echo JText::_('NB_NICKNAME'); ?><span>*</span></label></dt>
					<dd><input type="text" name="name" id="name" class="nbInputBox required" size="40" value="<?php echo $this->ninjaboardUser->get('name'); ?>" maxlength="50" /></dd>
					<dt><label for="email"><?php echo JText::_('NB_EMAIL'); ?><span>*</span></label></dt>
					<dd><input type="text" name="email" id="email" class="nbInputBox required validate-email" size="40" value="<?php echo $this->ninjaboardUser->get('email'); ?>" /></dd>
					<dt><label id="pwmsg" for="password"><?php echo JText::_('NB_PASSWORD'); ?><span>*</span></label></dt>
					<dd><input type="password" name="password" id="password" class="nbInputBox validate-password" size="40" value="" /></dd>
					<dt><label id="pw2msg" for="password2"><?php echo JText::_('NB_VERIFYPASSWORD'); ?><span>*</span></label></dt>
					<dd><input type="password" name="password2" id="password2" class="nbInputBox validate-passverify" size="40" value="" /></dd>
				</dl>
			</fieldset>
			<fieldset class="nbOuter">
				<legend><?php echo JText::_('NB_PREFERENCES'); ?></legend>
				<div class="nbLeft">
					<fieldset>
					<legend><?php echo JText::_('NB_GENERALSETTINGS'); ?></legend>
					<dl>
						<dt><label for="show_email" class="jbLabel"><?php echo JText::_('NB_SHOWMYEMAIL'); ?></label></dt>
						<dd><?php echo $this->lists['show_email']; ?></dd>
						<dt><label for="show_online_state" class="jbLabel"><?php echo JText::_('NB_SHOWMYONLINESTATE'); ?></label></dt>
						<dd><?php echo $this->lists['show_online_state']; ?></dd>
					</dl>
					</fieldset>
				</div>
				<div class="nbRight">
					<fieldset>
					<legend><?php echo JText::_('NB_POSTSETTINGS'); ?></legend>
					<dl>
						<dt><label for="enable_bbcode" class="jbLabel"><?php echo JText::_('NB_ENABLEBBCODE'); ?></label></dt>
						<dd><?php echo $this->lists['enable_bbcode']; ?></dd>
						<dt><label for="enable_emoticons" class="jbLabel"><?php echo JText::_('NB_ENABLEEMOTICONS'); ?></label></dt>
						<dd><?php echo $this->lists['enable_emoticons']; ?></dd>
						<dt><label for="notify_on_reply" class="jbLabel"><?php echo JText::_('NB_NOTIFYMEONREPLY'); ?></label></dt>
						<dd><?php echo $this->lists['notify_on_reply']; ?></dd>
					<dl>
					</fieldset>
				</div>
				<fieldset class="nbClr">
					<legend><?php echo JText::_('NB_TIMESETTINGS'); ?></legend>
					<label for="time_zone" class="jbLabel"><?php echo JText::_('NB_TIMEZONE'); ?></label>
					<?php echo $this->lists['timezones']; ?>
					<br />
					<label for="time_format" class="jbLabel"><?php echo JText::_('NB_TIMEFORMAT'); ?></label><br />
					<?php echo $this->lists['timeformats']; ?>
				</fieldset>
			</fieldset>
			<?php
			$n =count($this->profilefieldsets);
			for ($i=0; $i < $n; $i++) :
				$fieldset =& $this->profilefieldsets[$i]; ?>
			<fieldset class="nbOuter">
				<legend><?php echo JText::_($fieldset->name); ?></legend><?php
				$m = count($this->profilefields);
				for ($j=0; $j < $m; $j++) :
					$field 	=& $this->profilefields[$j];
					if ($fieldset->id == $field->id_profile_field_set) : ?>
						<label for="<?php echo $field->name; ?>"><?php echo JText::_($field->title), $field->required ? '<span>*</span>' : ''; ?></label><br />
						<?php echo str_replace('jbInputBox', 'nbInputBox', $field->element), '<br /><br />';
						# TODO: Replace class name at the engine !!!
					endif;
				endfor; ?>		
			</fieldset><?php
			endfor; ?>
			<?php if ($this->enableAvatars) : ?>
			<fieldset class="nbOuter">
				<legend><?php echo JText::_('NB_AVATAR'); ?></legend>
				<label for="avatarfile"><?php echo JText::_('NB_UPLOADAVATARFILE'); ?></label>
				<input type="file" name="avatarfile" id="avatarfile" class="nbInputBox" size="40" value="" maxlength="255" />
				<br />
				<label for="avatarimage"><?php echo JText::_('NB_CURRENTAVATARIMAGE'); ?></label>
				<div class="nbAvatar"><?php
				if ($this->avatarFile != '') : ?>
					<img src="<?php echo $this->avatarFile; ?>" alt="<?php echo $this->avatarFileAlt; ?>" id="avatarimage" /><?php
				else : 
					echo JText::_('NB_NOAVATARIMAGE');
				endif; ?>
				</div>
			</fieldset>
			<?php endif; ?>
			<fieldset class="nbOuter">
				<legend><?php echo JText::_('NB_SIGNATURE'); ?></legend>
				<textarea name="signature" id="signature" rows="5" cols="40"><?php echo $this->ninjaboardUser->get('signature'); ?></textarea>
			</fieldset>
			<div id="nbSubmitButtons">
			  <button type="submit" class="nb-buttons btnSubmit validate"><span><?php echo JText::_('NB_SUBMIT'); ?></span></button>
			  <button type="reset" class="nb-buttons btnReset"><span><?php echo JText::_('NB_Reset'); ?></span></button>
			</div>
		</div>

		<input type="hidden" name="option" value="com_ninjaboard" />
		<input type="hidden" name="task" value="ninjaboardsaveprofile" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<input type="hidden" name="agree" value="1" />
		<input type="hidden" name="id" value="<?php echo $this->ninjaboardUser->get('id'); ?>" />
		<input type="hidden" name="redirect" value="<?php echo $this->redirect; ?>" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
	<div class="nbClr"></div>
	<br />
	
