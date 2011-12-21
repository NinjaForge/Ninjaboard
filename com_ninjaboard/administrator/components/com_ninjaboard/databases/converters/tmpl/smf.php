<? /** $Id: smf.php 1518 2011-02-09 22:21:46Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<style type="text/css">
#smf-form {
	margin: 0 25%;
	width: 50%;
}
</style>

<script type="text/javascript">
	window.addEvent('domready', function(){
	
		var setData = function(){		
			var request = document.getElement('.placeholder a.smf').retrieve('request');

			if(!request.options.data) request.options.data = {};
			
			$$('#smf .value').each(function(element){
				request.options.data[element.get('name')] = element.get('value');
				if(localStorage) localStorage.setItem('smf-'+element.get('name'), element.get('value'));
			});
		};

		document.getElement('.placeholder a.smf').addEvent('mousedown', setData);
		$$('#smf .value').addEvent('change', setData);
		
		if(localStorage) {
			$$('#smf .value').each(function(element){
				element.set('value', localStorage.getItem('smf-'+element.get('name')) ? localStorage.getItem('smf-'+element.get('name')) : element.get('value'));
			});
			setData();
		}
	
		var ran = false;
		$('smf-form').getElement('a.confirm').addEvent('mouseup', function(){
			var request = $E('.placeholder a.smf').retrieve('request');
	
			request.addEvent('onComplete', function(){
				if(ran) return;
				request.options.msg.warning = false;
				request.createRequest.attempt([new Element('a', {
					text: 'SMF',
					'class': 'smf_users'
				}), 'smf_users', 'Synchronizing users from %s'], request);
				
				ran = true;
			});
		});
	});
</script> 

<fieldset class="ninja-form">
	<form id="smf">
		<div class="element">
			<label for="smf-path" class="key"><?= @text('Absolute path to SMF') ?></label>
			<input type="text" name="path" id="smf-path" value="<?= $converter->getPath() ?>" placeholder="<?= $converter->getPath() ?>" class="value" />
		</div>
	</form>
</fieldset>
<p class="description"><?= $converter->getDescription() ?></p>