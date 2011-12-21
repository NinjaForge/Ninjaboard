<? /** $Id: form.php 2460 2011-10-11 21:21:19Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? $user = $this->getService('com://admin/ninjaboard.model.users')->id($state->user)->getItem() ?>

<? foreach($profile_fields as $profile_field) : ?><div class="element">
	<label for="custom_<?= $profile_field->name ?>" class="key"><?= $profile_field->label ?></label>
	<input type="text" name="custom_<?= $profile_field->name ?>" id="custom_<?= $profile_field->name ?>" value="<?= $user->{'custom_'.$profile_field->name} ?>" class="value" />
</div><? endforeach ?>