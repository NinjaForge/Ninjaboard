<? /** $Id: form.php 1214 2010-12-13 02:38:37Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<form action="<?= @route('id='.@$setting->id) ?>" method="post" id="<?= @id() ?>">
	<div class="col width-50">
		<?= @template('form_details') ?>

		<? @ninja('behavior.tooltip', array('selector' => '[title].hasTip')) ?>

		<?= @template('admin::com.ninja.view.setting.fieldsets') ?>
	</div>
</form>