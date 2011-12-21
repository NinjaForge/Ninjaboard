<?php defined('_JEXEC') or die('Restricted access'); 

	$this->loadbar = 'reportpost';
?>

	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>

	<div id="nbLogin">
		<form action="<?php echo $this->action; ?>" method="post" id="josForm" name="josForm" class="form-validate">
			<fieldset>
				<legend class="jbLegend"><?php echo JText::_('NB_REPORTPOST'); ?></legend>
				<?php echo JText::_('NB_REPORTPOSTABUSE'); ?><br /><br />
				<label for="report_comment" class="jbLabel"><?php echo JText::_('NB_COMMENT'); ?></label>
				<textarea name="report_comment" id="report_comment" class="jbInputBox jbField required validate-report_comment" cols="35" rows="5"></textarea>		
				<div id="nbSubmitButtons">
        <button type="submit" class="nb-buttons btnSubmit validate"><span><?php echo JText::_('NB_SUBMIT'); ?></span></button>
				</div>
			</fieldset>

		<input type="hidden" name="option" value="com_ninjaboard" />
		<input type="hidden" name="task" value="ninjaboardreportpost" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<input type="hidden" name="redirect" value="<?php echo $this->redirect; ?>" />
		<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>
