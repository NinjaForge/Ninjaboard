<? /** $Id: form_details.php 1359 2011-01-10 19:45:28Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? @ninja('behavior.livetitle', array('title' => @$setting->title)) ?>

<fieldset class="adminform ninja-form">
	<legend><?= @text('Details') ?></legend>		
		<div class="element">
			<label class="key" for="title"><?= @text('Title') ?></label>
			<input type="text" name="title" id="title" class="inputbox required value" rel="<?= @text('Not so fast, you forgot the title.') ?>" size="50" value="<?= @escape($setting->title) ?>" maxlength="100" />
		</div>
		<script type="text/javascript">
			//Using onLoad in addition to onDomReady as the images in the labels changes the <li> width once loaded
			var setWidth = function(){
				var states = $('states').getElements('li'), max = 0;
				states.setStyle('width', '').getWidth().each(function(width){
					if(width > max) max = width;
				});
				states.setStyle('width', max);
			}
			window.addEvents({domready: setWidth, load: setWidth});
		</script>
		<div class="element" id="states">
			<label class="key"><?= @text('State') ?></label>
			<div><?= @ninja('select.statelist', array('attribs' => array('class' => 'validate-reqchk-byname label:\'state\''), 'selected' => @$setting->enabled)) ?></div>
			<div><?= @ninja('select.statelist', array('name' => 'default', 'attribs' => array('class' => 'validate-reqchk-byname label:\'default\''), 'selected' => @$setting->default, 'yes' => 'Default', 'no' => 'Not Default', 'img_y' => '/16/star.png', 'img_x' => '/16/star_off.png', 'id' => 'default')) ?></div>
		</div>
		<!--<div class="element">
			<label class="key" for="theme"><?= @text('Theme') ?></label>
			<input type="text" name="theme" id="theme" class="inputbox required value" size="50" value="<?= @escape($setting->title) ?>" maxlength="100" />
		</div>-->
</fieldset>