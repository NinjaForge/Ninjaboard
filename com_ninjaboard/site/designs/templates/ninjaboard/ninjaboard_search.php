<?php defined('_JEXEC') or die('Restricted access'); 

	$this->loadbar = 'search';

	/**
	 * TODO: We need an advanced search mask also !!!
	 */
?>
	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>

	<form action="<?php echo $this->action; ?>" method="get" id="josForm" name="josForm" class="form-validate">	
		<div id="nbSearch">
			<div class="nbLeft">
				<fieldset>
					<legend><?php echo JText::_('NB_SEARCHKEYWORDS'); ?></legend>
					<input type="text" name="searchwords" id="searchwords" class="nbInputBox required" size="30" value="<?php echo $this->searchWords; ?>" alt="<?php echo JText::_('NB_LABELSEARCHKEYWORDS'); ?>" />
					<select name="searchtype" disabled="disabled">
						<option value="default">Select search type</option>
						<option value="title">Search Title</option>
						<option value="body">Search Body</option>
						<option value="complete">Search Title &amp; Body</option>
					</select>
				</fieldset>
			</div>
			<div class="nbRight">
				<fieldset>
					<legend><?php echo JText::_('NB_USERNAME'); ?></legend>
					<input type="text" name="searchuser" id="searchuser" class="nbInputBox required" size="30" value="<?php echo JText::_('NB_SEARCHALPHA'); #echo $this->userName; ?>" alt="<?php echo JText::_('NB_LABELSEARCHKEYWORDS'); ?>" disabled="disabled" /></dd>	
					<select name="searchtype" disabled="disabled">
						<option value="default">Select thread type</option>
						<option value="topics">Search Topics</option>
						<option value="posts">Search Posts</option>
					</select>
				</fieldset>
			</div>	
			<fieldset class="nbCenter">
				<legend><?php echo JText::_('NB_SEARCHOPTIONS'); ?></legend>
				At Beta stage here will be various options.
			</fieldset>
			<div class="nbClr" id="nbSubmitButtons">
		  <button type="submit" class="nb-buttons btnSubmit validate" onclick="submitbutton(\\\'ninjaboardsearch\\\')"><span><?php echo JText::_('NB_SUBMIT'); ?></span></button>
			<button type="reset" class="nb-buttons btnReset"><span><?php echo JText::_('NB_Reset'); ?></span></button>
			</div>	
		</div>	
		<input type="hidden" name="option" value="com_ninjaboard" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<input type="hidden" name="redirect" value="<?php echo $this->redirect; ?>" />
	</form>
	<div class="nbClr"></div>

<?php
	$this->loadbar = 'searchresults';
	echo $this->loadTemplate('posts');
?>
