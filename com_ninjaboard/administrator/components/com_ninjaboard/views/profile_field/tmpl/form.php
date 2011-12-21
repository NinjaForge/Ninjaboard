<? /** $Id: form.php 1188 2010-12-08 22:56:12Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<form action="<?= @route('id='.$profile_field->id) ?>" method="post" id="<?= @id() ?>">
	<div class="col width-50">
		<fieldset class="adminform ninja-form">
			<div class="element">
				<label for="label" class="key"><?= @text('Label') ?></label>
				<input type="text" name="label" id="label" class="value" value="<?= @escape($profile_field->label) ?>" />
			</div>
			<div class="element">
				<label for="name" class="key"><?= @text('Field') ?></label>
				<input type="text" name="name" id="name" class="value" value="<?= $profile_field->name ?>" />
			</div>
		</fieldset>
	</div>
</form>
