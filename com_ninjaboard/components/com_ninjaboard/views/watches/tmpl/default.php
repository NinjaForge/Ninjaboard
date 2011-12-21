<? /** $Id: default.php 2180 2011-07-11 14:01:18Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/site.css" />

<div id="ninjaboard" class="ninjaboard watches <?= $params['pageclass_sfx'] ?> <?= $params['style']['type'] ?> <?= $params['style']['border'] ?> <?= $params['style']['separators'] ?>">

	<div class="header relative">
		<script type="text/javascript">
			jQuery(function($){
				$('[name=type_name]').change(function(){
					var url = '<?= @route('type_name=%24type_name%24', true) ?>'.replace('$type_name$', $(this).val());

					window.location.href = url;
				});
			});
		</script>
		<label for="type"><?= @text('Filter') ?></label>
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

	<form action="<?= @route() ?>" method="post" id="<?= @id() ?>">
		<table class="ninja-list">
			<thead>
				<tr>
					<th width="20px" style="text-align: center"><?= @ninja('grid.checkall') ?></th>
					<th width="20px"><?= @text('Type') ?></th>
					<th><?= @text('Title') ?></th>				
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4" style="text-align: right;">
						<script type="text/javascript">
							jQuery(function($){
								$('.<?= @id('remove-selected') ?>').click(function(event){
									event.preventDefault();
									if(event.target == this) return;

									var form = $(this).closest('form'), join = form.attr('action').match(/\?/) ? '&' : '?';
									form.attr('action', form.attr('action')+join+$('input.id:checked', form).serialize()).submit();
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
			<tr class="<?= @ninja('grid.zebra') ?>">
				<td align="center"><?= @ninja('grid.id', array('value' => $watch->id)) ?></td>
				<td style="text-align: center"><img src="<?= $watch->icon ?>" title="<?= $watch->type ?>" alt="icon" width="16px" /></td>
				<td><a href="<?= $watch->link ?>"><?= $watch->title ?></a></td>
			</tr>
			<? endforeach ?>
			</tbody>
		</table>
		<input type="hidden" name="action" value="delete" />
	</form>

</div>