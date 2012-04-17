<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<input type="hidden" name="which_name" value="" />
<? if($params->view_settings->display_name == 'name') : ?>
	<div class="element">
		<div class="key">
			<input type="checkbox" name="which_name" <? if($person->which_name == 'username') echo 'checked' ?> id="username" value="username" />
		</div>
		<label for="username">
			<?= sprintf(@text('COM_NINJABOARD_REPLACE_MY_SCREEN_NAME_WITH_MY_USERNAME_%S'), $person->username) ?>
		</label>
	</div>
<? else : ?>
	<div class="element">
		<div class="key">
			<input type="checkbox" name="which_name" <? if($person->which_name == 'name') echo 'checked' ?> id="name" value="name" />
		</div>
		<label for="name">
			<?= sprintf(@text('COM_NINJABOARD_REPLACE_MY_SCREEN_NAME_WITH_MY_REAL_NAME_%S'), $person->name) ?>
		</label>
	</div>
<? endif ?>