<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<form action="<?= @route('id='.@$setting->id) ?>" method="post" id="<?= @id() ?>">
	<div class="col width-50">
		<?= @template('form_details') ?>

		<? @ninja('behavior.tooltip', array('selector' => '[title].hasTip')) ?>

		<?= @template('admin::com.ninja.view.setting.fieldsets') ?>
	</div>
</form>