<?php defined('_JEXEC') or die('Restricted access');

	// add required java scripts
	$this->document->addScriptDeclaration('
	<!--
		document.getElementsByTagName("body").onload=setTimeout("document.josForm.login_username.focus()", 1000)
	//-->
	');

	$this->loadbar = 'login';
?>

	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>

	<div id="nbLogin">
		<form action="<?php echo $this->action; ?>" method="post" id="josForm" name="josForm" class="form-validate">
			<fieldset>
				<legend><?php echo JText::_('NB_LOGIN'); ?></legend>
				<dl>
					<dt><label for="login_username"><?php echo JText::_('NB_USERNAME'); ?></label></dt>
					<dd><input name="login_username" id="login_username" type="text" class="nbInputBox required" size="15" alt="<?php echo JText::_('NB_USERNAME'); ?>" /></dd>
					<dt><label for="login_password" class="jbLabel"><?php echo JText::_('NB_PASSWORD'); ?></label>
					<dd><input name="login_password" id="login_password" type="password" class="nbInputBox required" size="15" alt="<?php echo JText::_('NB_PASSWORD'); ?>" /></dd>
					<dt></dt>
					<dd><a href="index.php?option=com_ninjaboard&view=requestlogin&Itemid=<?php echo $this->Itemid; ?>"><?php echo JText::_('NB_FORGOTYOURLOGIN'); ?></a></dd>
					<dt></dt>
					<dd>
						<input type="checkbox" name="remember" id="login_remember" class="nbCheckBox" value="yes" alt="<?php echo JText::_('NB_REMEMBERME'); ?>" />
						<label for="login_remember"><?php echo JText::_('NB_REMEMBERME'); ?></label>
					</dd>
				</dl>

				<div id="nbSubmitButtons">
				<button type="submit" class="nb-buttons btnSubmit validate"><span><?php echo JText::_('NB_SUBMIT'); ?></span></button>
				</div>
			</fieldset>

			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="ninjaboardlogin" />
			<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
			<input type="hidden" name="redirect" value="<?php echo $this->redirect; ?>" />
		</form>
	</div>
