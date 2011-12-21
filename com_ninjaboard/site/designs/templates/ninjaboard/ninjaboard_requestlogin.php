<?php defined('_JEXEC') or die('Restricted access'); $this->loadbar = 'requestlogin'; ?>

	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>

	<div id="nbLogin">
		<form action="<?php echo $this->action; ?>" method="post" id="josForm" name="josForm" class="form-validate">
			<fieldset>
				<legend><?php echo JText::_('NB_REQUESTLOGIN'); ?></legend>
				<?php echo JText::_('NB_REQUESTLOGINTEXT'); ?>
				<br /><br />
				<label for="email"><?php echo JText::_('NB_EMAILADDRESS'); ?></label>
				<input type="text" name="email" id="email" class="nbInputBox required validate-email" size="30" alt="<?php echo JText::_('NB_EMAILADDRESS'); ?>" />
			
				<br />
				<div id="nbSubmitButtons">
				<button type="submit" class="nb-buttons btnSubmit validate"><span><?php echo JText::_('NB_SUBMIT'); ?></span></button>
				</div>
			</fieldset>

			<input type="hidden" name="option" value="com_ninjaboard" />
			<input type="hidden" name="task" value="ninjaboardrequestlogin" />
			<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
			<input type="hidden" name="redirect" value="<?php echo $this->redirect; ?>" />
		</form>
	</div>
