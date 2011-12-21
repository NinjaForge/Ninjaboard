<? /** $Id: form_custom_display_name.php 1543 2011-02-15 22:18:07Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<script type="text/javascript">
jQuery(function($){
	var focusInput = function(){
		if($('#use_alias').is(':checked')) $('#alias').select();
	};
	$('#use_alias').change(focusInput);
});
</script>

<div class="element">
	<div class="key">
		<input type="radio" name="which_name" <? if($person->which_name == 'username') echo 'checked' ?> id="username" value="username" />
	</div>
	<label for="username">
		<?= sprintf(@text('Replace my screen name with my username (%s)'), $person->username) ?>
	</label>
</div>
<div class="element">
	<div class="key">
		<input type="radio" name="which_name" <? if($person->which_name == 'name') echo 'checked' ?> id="name" value="name" />
	</div>
	<label for="name">
		<?= sprintf(@text('Replace my screen name with my real name (%s)'), $person->name) ?>
	</label>
</div>
<div class="element">
	<div class="key">
		<input type="radio" name="which_name" <? if($person->which_name == 'alias') echo 'checked' ?> id="use_alias" value="alias" />
	</div>
	<?= sprintf(@text('Replace my screen name with %s'), '<input type="text" name="alias" id="alias" placeholder="'.@text('what I type in here…').'" class="value" value="'.$person->alias.'" />') ?>
</div>