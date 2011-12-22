<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? $user = KFactory::get('admin::com.ninjaboard.model.users')->id($state->user)->getItem() ?>

<? foreach($profile_fields as $profile_field) : ?><div class="element">
	<label for="custom_<?= $profile_field->name ?>" class="key"><?= $profile_field->label ?></label>
	<input type="text" name="custom_<?= $profile_field->name ?>" id="custom_<?= $profile_field->name ?>" value="<?= $user->{'custom_'.$profile_field->name} ?>" class="value" />
</div><? endforeach ?>