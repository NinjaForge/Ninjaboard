<? /** $Id: form.php 2507 2011-11-22 11:31:02Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.form.head') ?>

<?= @ninja('behavior.livetitle', array('title' => @$rank->title)) ?>

<form action="<?= @route('id='.@$setting->id) ?>" method="post" id="<?= @id() ?>" class="validator-inline -koowa-form">
	<div class="col width-50">
		<?= @template('form_details') ?>

		<? @ninja('behavior.tooltip', array('selector' => '[title].hasTip')) ?>

		<?= @template('ninja:view.setting.fieldsets') ?>
	</div>
</form>