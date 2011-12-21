<? /** $Id: phpbb.php 1517 2011-02-09 22:21:34Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<script type="text/javascript">
	window.addEvent('domready', function(){
	
		var setData = function(){		
			var request = document.getElement('.placeholder a.phpbb').retrieve('request');

			if(!request.options.data) request.options.data = {};
			
			$$('#phpbb .value').each(function(element){
				request.options.data[element.get('name')] = element.get('value');
				if(localStorage) localStorage.setItem('phpbb-'+element.get('name'), element.get('value'));
			});
		};

		document.getElement('.placeholder a.phpbb').addEvent('mousedown', setData);
		$$('#phpbb .value').addEvent('change', setData);
		
		if(localStorage) {
			$$('#phpbb .value').each(function(element){
				element.set('value', localStorage.getItem('phpbb-'+element.get('name')) ? localStorage.getItem('phpbb-'+element.get('name')) : element.get('value'));
			});
			setData();
		}
	
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
			<label for="phpbb-path" class="key"><?= @text('phpBB3 path') ?></label>
			<input type="text" name="path" id="phpbb-path" value="<?= $converter->getPath() ?>" placeholder="<?= $converter->getPath() ?>" class="value" />
		</div>
	</form>
</fieldset>
<p class="description"><?= $converter->getDescription() ?></p>