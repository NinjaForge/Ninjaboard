<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<script type="text/javascript">
<!--
	Window.onDomReady(function(){
		document.formvalidator.setHandler('passverify', function (value) { return ($('password').value == value); }	);
	});
// -->
</script>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" id="josForm" name="josForm" class="form-validate">
	<div class="jbBoxTopLeft"><div class="jbBoxTopRight"><div class="jbBoxTop">
		<div class="jbTextHeader"><?php echo JText::_('NB_RESETLOGIN'); ?></div>
	</div></div></div>
	<div class="jbBoxOuter"><div class="jbBoxInner">
		<div class="jbLeft jbMargin5">
			<fieldset class="ninjaboardform">
				<legend class="jbLegend"><?php echo JText::_('NB_MYDETAILS'); ?></legend>
				<label for="username" class="jbLabel"><?php echo JText::_('NB_LOGIN'); ?></label>
				<input type="text" name="username" id="username" class="jbInputBox jbField required validate-username" size="40" value="<?php echo $this->ninjaboardUser->get('username'); ?>" maxlength="50" readonly="readonly" />
				<br clear="all" />
				<label for="name" class="jbLabel"><?php echo JText::_('NB_NICKNAME'); ?></label>
				<input type="text" name="name" id="name" class="jbInputBox jbField required" size="40" value="<?php echo $this->ninjaboardUser->get('name'); ?>" maxlength="50" readonly="readonly" />
				<br clear="all" />
				<label for="email" class="jbLabel"><?php echo JText::_('NB_EMAIL'); ?></label>
				<input type="text" name="email" id="email" class="jbInputBox jbField required validate-email" size="40" value="<?php echo $this->ninjaboardUser->get('email'); ?>" readonly="readonly" />
				<br clear="all" />
				<label id="pwmsg" for="password" class="jbLabel"><?php echo JText::_('NB_PASSWORD'); ?></label>
				<input type="password" name="password" id="password" class="jbInputBox jbField validate-password" size="40" value="" /> *
				<br clear="all" />
				<label id="pw2msg" for="password2" class="jbLabel"><?php echo JText::_('NB_VERIFYPASSWORD'); ?></label>
				<input type="password" name="password2" id="password2" class="jbInputBox jbField validate-passverify" size="40" value="" /> *
			</fieldset>
			<button type="submit" class="nb-buttons btnSubmit validate"><span><?php echo JText::_('NB_SUBMIT'); ?></span></button>
		</div>
		<br clear="all" />
	</div></div>
	<div class="jbBoxBottomLeft"><div class="jbBoxBottomRight"><div class="jbBoxBottom"></div></div></div>
	<div class="jbMarginBottom10"></div>
	<input type="hidden" name="option" value="com_ninjaboard" />
	<input type="hidden" name="task" value="ninjaboardresetlogin" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->ninjaboardUser->get('id'); ?>" />
	<input type="hidden" name="activation" value="<?php echo $this->activation; ?>" />
</form>