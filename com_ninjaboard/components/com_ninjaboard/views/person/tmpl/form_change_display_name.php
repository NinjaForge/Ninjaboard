<? /** $Id: form_change_display_name.php 1195 2010-12-09 03:39:46Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<input type="hidden" name="which_name" value="" />
<? if($params->view_settings->display_name == 'name') : ?>
	<div class="element">
		<div class="key">
			<input type="checkbox" name="which_name" <? if($person->which_name == 'username') echo 'checked' ?> id="username" value="username" />
		</div>
		<label for="username">
			<?= sprintf(@text('Replace my screen name with my username (%s)'), $person->username) ?>
		</label>
	</div>
<? else : ?>
	<div class="element">
		<div class="key">
			<input type="checkbox" name="which_name" <? if($person->which_name == 'name') echo 'checked' ?> id="name" value="name" />
		</div>
		<label for="name">
			<?= sprintf(@text('Replace my screen name with my real name (%s)'), $person->name) ?>
		</label>
	</div>
<? endif ?>