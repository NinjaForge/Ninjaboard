<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.grid.head') ?>

<? /*if(@$length > 0) : ?>
	<?= @template('ninja:view.search.filter_thead') ?>
<? endif*/ ?>

<style type="text/css">
	.<?= @id('order-up') ?>,
	.<?= @id('order-down') ?> {
		display: block;
		background-position: center;
		background-repeat: no-repeat;
		min-height: 12px;
		min-width: 11px;
		cursor: pointer;
	}
	.<?= @id('order-up') ?> {
		background-image: url(<?= @$img('/triangle-up.png') ?>);
		background-position-y: 1px;
	}
	.<?= @id('order-down') ?> {
		background-image: url(<?= @$img('/triangle-down.png') ?>);
		background-position-y: -1px;
	}
	.selected .<?= @id('order-up') ?> {
		background-image: url(<?= @$img('/triangle-up-focus.png') ?>);
	}
	.selected .<?= @id('order-down') ?> {
		background-image: url(<?= @$img('/triangle-down-focus.png') ?>);
	}
</style>

<script type="text/javascript">
new Image().src = '<?= @$img('/triangle-up-focus.png') ?>';
new Image().src = '<?= @$img('/triangle-down-focus.png') ?>';
window.addEvent('domready', function(){
	$$('.<?= @id('order-up') ?>', '.<?= @id('order-down') ?>').addEvent('click', function(){
		var order = this.getParent().getText().toInt(),
			id = this.getParent().getParent().getElement('[type=checkbox]').value;

		if(this.hasClass('<?= @id('order-up') ?>')) {
			order = - 1;
		} else {
			order = + 1;
		}
		new Element('form', {action: 'index.php?option=com_ninjaboard&view=forums&sort=path_sort_ordering&id[]='+id, method: 'post'})
			.inject(document.body)
			.adopt([
				new Element('input', {type: 'hidden', name: 'action', value: 'edit'}),
				new Element('input', {type: 'hidden', name: '_token', value: '<?= JUtility::getToken() ?>'}),
				new Element('input', {type: 'hidden', name: 'order', value: order})
			]).submit();
	});
});
</script>

<? if(@$length > 0) : ?>
	<?= @template('ninja:view.search.filter_search_enabled') ?>
<? endif ?>

<form action="<?= @route() ?>" method="post" id="<?= @id() ?>" class="-koowa-grid">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check">
					<?= @helper('grid.checkall') ?>
				</th>
				<th class="grid-align">
					<?= @text('COM_NINJABOARD_TITLE') ?>
				</th>
				<th width="40px" class="grid-center">
					<?= @text('COM_NINJABOARD_TOPICS') ?>
				</th>
				<th width="40px" class="grid-center">
					<?= @text('COM_NINJABOARD_POSTS') ?>
				</th>
				<th width="32px" colspan="2"></th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => @$total, 'colspan' => 7)) ?>
		<tbody class="sortable">
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>