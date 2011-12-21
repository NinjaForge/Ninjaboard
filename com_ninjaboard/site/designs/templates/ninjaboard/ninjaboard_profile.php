<?php defined('_JEXEC') or die('Restricted access'); 

	$this->loadbar = 'viewprofile';
?>

	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>

	<div id="nbProfile">
		<fieldset class="nbOuter">
			<legend><?php echo JText::_('NB_USERDETAILS'); ?></legend>
			<dl>
				<dt><label for="name" class="jbLabel"><?php echo JText::_('NB_NICKNAME'); ?></label></dt>
				<dd><?php echo $this->ninjaboardUserView->get('name'); ?></dd>
			<?php if ($this->ninjaboardUserView->get('email') != '') : ?>
				<dt><label for="email"><?php echo JText::_('NB_EMAIL'); ?></label></dt>
				<dd><?php echo $this->ninjaboardUserView->get('email'); ?></dd>
			<?php endif; ?>
			<?php if ($this->enableAvatars) : ?>
				<dt><label for="avatarimage" class="jbLabel"><?php echo JText::_('NB_AVATAR'); ?></label></dt>
				<dd>
					<?php if ($this->avatarFile != '') : ?>
						<img src="<?php echo $this->avatarFile; ?>" alt="<?php echo $this->avatarFileAlt; ?>" id="avatarimage" />
					<?php else : ?>
						<?php echo JText::_('NB_NOAVATARIMAGE'); ?>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
			</dl>
		</fieldset>
		<?php
			$n = count($this->profilefieldsets);
			for ($i=0; $i < $n; $i++) {
				$fieldset 	=& $this->profilefieldsets[$i];
			?>
			<fieldset class="nbOuter">
				<legend><?php echo JText::_($fieldset->name); ?></legend>
				<dl>
					<?php
					$m = count($this->profilefields);
					for ($j=0; $j < $m; $j++) {
						$field 	=& $this->profilefields[$j];
						if ($fieldset->id == $field->id_profile_field_set) {
					?>						
					<dt><label for="<?php echo $field->name; ?>"><?php echo JText::_($field->title); ?></label></dt>
					<dd><?php echo $field->value ? $field->value : '-----'; ?></dd>
					<?php
						}
					}
					?>		
				</dl>
			</fieldset>																																					
			<?php
			}
		?>
	</div>
