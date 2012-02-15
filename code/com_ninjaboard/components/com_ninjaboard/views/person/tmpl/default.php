<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('com://site/ninjaboard.view.default.head') ?>

<div id="ninjaboard" class="ninjaboard person <?= $params['pageclass_sfx'] ?> <?= $params['style']['type'] ?> <?= $params['style']['border'] ?> <?= $params['style']['separators'] ?>">
	<? if($edit_button) : ?>
		<div class="header">
			<?= $edit_button ?>
		</div>
	<? endif ?>
	<? if(($watch_button || $message_button) && $person->id != $me->id) : ?>
		<div class="header end">
			<? if($message_button) echo @helper('com://site/ninjaboard.template.helper.behavior.message', array(
			    'message_to_id'           => $person->id,
			    'message_to_display_name' => $person->display_name
			)) ?>
			<? if($watch_button)   echo @helper('com://site/ninjaboard.template.helper.behavior.watch') ?>
		</div>
	<? endif ?>
	
	<? if($params['avatar_settings']['enable_avatar']) : ?>
	<div id="<?= @id('avatar') ?>" class="avatar">
		<?= @helper('com://site/ninjaboard.template.helper.avatar.image', array('id' => $person->id)) ?>
	</div>
	<? endif ?>
	<h1>
		<?= $person->display_name ?>
	</h1>

	<? if($person->rank_title && $person->rank_icon && @$img('/rank/'.$person->rank_icon)) : ?>
	<div>
		<strong class="rank"><?= $person->rank_title ?></strong>
		<div class="rank_icon"><img src="<?= @$img('/rank/'.$person->rank_icon) ?>" /></div>
	</div>
	<? endif ?>
	
	<div style="clear: both; display: block;"></div>
	<?= @helper('com://site/ninjaboard.template.helper.template.space') ?>
	
	<div class="profile">
	
		<?= @render(@template('default_profile'), $person->display_name, $params['module']) ?>
	
	</div>
	<? if($person->posts) : ?>
	    <?= @helper('com://site/ninjaboard.template.helper.template.space') ?>
    	<div class="header relative">
    		<?= $pagination ?>
    		<div style="clear:both"></div>
    	</div>
    	<?= $posts ?>
    	<div class="footer relative">
    		<?= $pagination ?>
    	</div>
	<? endif ?>
</div>