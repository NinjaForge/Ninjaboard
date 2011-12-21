<? /** $Id: phpbb.php 1168 2010-12-06 00:42:20Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<script type="text/javascript">
	window.addEvent('domready', function(){
		var ran = false;
		$('phpbb-form').getElement('a.confirm').addEvent('mouseup', function(){
			var request = $E('.placeholder a.phpbb').retrieve('request');
	
			request.addEvent('onComplete', function(){
				if(ran) return;
				request.options.msg.warning = false;
				request.createRequest.attempt([new Element('a', {
					text: 'phpBB3',
					'class': 'phpbb_users'
				}), 'phpbb_users', 'Synchronizing users from %s'], request);
				
				ran = true;
			});
		});
	});
</script> 

<fieldset class="ninja-form">
	<form id="phpbb">
		<div class="element">
			<label for="path" class="key"><?= @text('phpBB3 path') ?></label>
			<input type="text" name="path" id="path" value="<?= $converter->getPath() ?>" placeholder="<?= $converter->getPath() ?>" class="value" />
		</div>
	</form>
</fieldset>
<p class="description"><?= $converter->getDescription() ?></p>