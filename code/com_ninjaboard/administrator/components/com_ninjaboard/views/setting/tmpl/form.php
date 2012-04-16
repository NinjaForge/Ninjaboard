<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.form.head') ?>

<form action="<?= @route('id='.@$setting->id) ?>" method="post" id="<?= @id() ?>" class="validator-inline -koowa-form">
	<div class="col width-50">
		<?= @template('form_details') ?>

		<? @ninja('behavior.tooltip', array('selector' => '[title].hasTip')) ?>

		<?= @template('ninja:view.setting.fieldsets') ?>
	</div>
</form>