<?php defined('_JEXEC') or die('Restricted access'); ?>

<form method="get" action="<?php echo $this->actionSearch; ?>">
	<input type="hidden" name="option" value="com_ninjaboard" />
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />

	<label for="nbInputField">
	<button type="submit" class="nbSubmitSearch"><span></span></button>
	</label>

	<input type="text" id="nbInputField" class="nbInputSearch" name="searchwords" size="30" value="<?php echo $this->searchInputBoxText; ?>" onclick="if (this.value == '<?php echo $this->searchInputBoxText; ?>') this.value = '';" onblur="if (this.value == '') this.value = '<?php echo $this->searchInputBoxText; ?>';" />

</form>

<?php # TODO: Ensure the searchInputBoxText is not only available on the forum view !!! ?>
