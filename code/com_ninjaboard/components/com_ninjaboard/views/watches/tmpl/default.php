<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('com://site/ninjaboard.view.default.head') ?>

<div id="ninjaboard" class="ninjaboard watches <?= $params['pageclass_sfx'] ?> <?= $params['style']['type'] ?> <?= $params['style']['border'] ?> <?= $params['style']['separators'] ?>">

	<div class="header relative">
		<script type="text/javascript">
			ninja(function($){
				$('[name=type_name]').change(function(){
					window.location.href = "<?= @route('type_name=', true) ?>".replace('type_name=', 'type_name='+$(this).val());
				});
			});
		</script>
		<label for="type"><?= @text('COM_NINJABOARD_FILTER') ?></label>
		<?= @helper('select.optionlist', array(
			'options' => array(
				@helper('select.option', array('value' => '', 'text' => 'All Items')),
				@helper('select.option', array('value' => 'forum', 'text' => 'Forum')),
				@helper('select.option', array('value' => 'person', 'text' => 'Person')),
				@helper('select.option', array('value' => 'topic', 'text' => 'Topic'))
			),
			'name' => 'type_name',
			'selected' => $state->type_name,
			'translate' => true
		)) ?>
		<?= $pagination ?>
		<div style="clear:both"></div>
	</div>

	<form action="<?= @route() ?>" method="post" id="<?= @id() ?>" class="-koowa-grid">
		<table class="ninja-list">
			<thead>
				<tr>
					<th width="20px" style="text-align: center"><?= @ninja('grid.checkall') ?></th>
					<th width="20px"><?= @text('COM_NINJABOARD_TYPE') ?></th>
					<th><?= @text('COM_NINJABOARD_TITLE') ?></th>				
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4" style="text-align: right;">
						<script type="text/javascript">
							ninja(function($){
								$('.<?= @id('remove-selected') ?>').click(function(event){
									event.preventDefault();
									if(event.target == this) return;

									var form = $(this).closest('form'), join = form.attr('action').match(/\?/) ? '&' : '?';
									form.attr('action', form.attr('action')+join+$('.-koowa-grid-checkbox:checked', form).serialize()).submit();
								});
							});
						</script>
						<div class="<?= @id('remove-selected') ?>">
							<?= $remove_selected_button ?>
						</div>
					</td>
				</tr>
			</tfoot>
			<tbody class="watches">
			<? foreach ($watches as $watch) : ?>
			<tr>
				<td align="center"><?= @helper('grid.checkbox', array('row' => $watch)) ?></td>
				<td style="text-align: center"><img src="<?= $watch->icon ?>" title="<?= $watch->type ?>" alt="icon" width="16px" /></td>
				<td><a href="<?= $watch->link ?>"><?= $watch->title ?></a></td>
			</tr>
			<? endforeach ?>
			</tbody>
		</table>
		<input type="hidden" name="action" value="delete" />
	</form>

</div>