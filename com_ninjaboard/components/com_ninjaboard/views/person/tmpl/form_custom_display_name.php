<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<script type="text/javascript">
jQuery(function($){
	$('#use_alias').click(function(){
		$('#alias').select();
	});
	$('#alias').focus(function(){
		if(!$('#use_alias').is(':checked')) $('#use_alias').attr('checked', true);
	});
});
</script>
<link rel="stylesheet" href="/site.person.form_custom_display_name.css" />

<? $which_name = $person->which_name ? $person->which_name : $params->view_settings->display_name ?>

<div class="element which_name-username">
	<div class="key">
		<input type="radio" name="which_name" <? if($which_name == 'username') echo 'checked' ?> id="username" value="username" />
	</div>
	<label for="username">
		<?= sprintf(@text('Replace my screen name with my username (%s)'), $person->username) ?>
	</label>
</div>
<div class="element which_name-name">
	<div class="key">
		<input type="radio" name="which_name" <? if($which_name == 'name') echo 'checked' ?> id="name" value="name" />
	</div>
	<label for="name">
		<?= sprintf(@text('Replace my screen name with my real name (%s)'), $person->name) ?>
	</label>
</div>
<div class="element which_name-alias">
	<div class="key">
		<input type="radio" name="which_name" <? if($which_name == 'alias') echo 'checked' ?> id="use_alias" value="alias" />
	</div>
	<label for="alias">
		<?= @text('Replace my screen name with') ?> 
	</label>
	<input type="text" name="alias" id="alias" placeholder="<?= @escape(@text('what I type in here…')) ?>" class="value" value="<?= @escape($person->alias) ?>" />
</div>