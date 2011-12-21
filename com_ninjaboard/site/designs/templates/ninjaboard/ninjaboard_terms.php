<?php defined('_JEXEC') or die('Restricted access'); 

	$this->document->addScriptDeclaration('
	<!--
		Window.onDomReady(function(){try{
			document.formvalidator.setHandler("agreed", function (value) { return ($("agreed1").checked); });
		}catch(e){}});
	//-->
	');

	$this->loadbar = 'terms';
?>
	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>

	<form action="<?php echo $this->action; ?>" method="post" id="josForm" name="josForm" class="form-validate">

		<fieldset>
			<legend><?php echo $this->terms->terms; ?></legend>
			<?php echo $this->terms->termstext; ?>	
		</fieldset>

		<?php if ($this->showAgreement) : ?>			
		<fieldset>
			<legend><?php echo $this->terms->agreement; ?></legend>
			<?php echo $this->terms->agreementtext; ?>	
		</fieldset>

		<div id="nbSubmitButtons">
			<button type="submit" class="nb-buttons buttonSubmit validate"><span><?php echo JText::_('NB_SUBMIT'); ?></span></button>
		</div>
		<?php endif; ?>	

	</form>
	<div class="nbClr"></div>
	<br />
	<br />
