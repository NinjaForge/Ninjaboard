<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? $select = @ninja('default.formid', 'controller') ?>

<script><?= "
	window.addEvent('domready', function(){
		$('$select').addEvent('change', function(){
			this.form.getElements('.permissions').hide();
			$(this.get('value')).show();
			Cookie.write('$select', this.get('value'));
		})
		.set('value', Cookie.read('$select'))
		.fireEvent('change');
	});
" ?></script>

<form action="<?= @route() ?>" method="post" id="<?= @ninja('default.formid') ?>">
	<fieldset class="ninja-col adminform ninja-form right">
			<legend>
				<select id="<?= $select ?>" class="value">
				<? foreach($this->controllers as $name => $controller) : ?>
					<option value="<?= @ninja('default.formid', $name) ?>">
						<?= @text(KInflector::humanize(KInflector::pluralize($name))) ?>
					</option>
				<? endforeach ?>	
				</select>
			</legend>
		<? foreach($this->controllers as $name => $controller) : ?>
			<? @$actions = $this->getService('ninja:template.helper.access', array(
				'name'		=> 'com_ninjaboard.permission.'.$name,
				'actions'	=> $controller,
				'id'		=> @ninja('default.formid', $name),
				'inputName'	=> 'access['.$name.']',
				'inputId'	=> 'access-'.$name,
				'default'	=> $name == 'default'
			)) ?>
			
			<? $actions = array() ?>
			<? $void = array('access', 'order', 'upload', 'cancel', 'save', 'apply') ?>
			<? foreach(@$actions->getActions() as $action => $obj) : ?>
				<? if(in_array($action, $void)) continue ?>
				<? $actions[] = $action ?>
			<? endforeach ?>
			<? @$actions->setActions($actions) ?>
			<?= @$actions->permissionslist() ?>
		
		<? endforeach ?>
	</fieldset>
</form>