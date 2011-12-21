<? /** $Id: form_filtering.php 1270 2010-12-22 13:14:06Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<table class="adminlist ninja-list">
	<thead> 
		<tr>
			<th nowrap="nowrap" colspan="8" style="text-align: left">
				<form action="<?= @route() ?>" method="get" style="display:inline;">
		
					<label for="search"><?= @text('Search:') ?></label>
					<?= @ninja('paginator.search') ?>
					&#160;&#160;
					<input type="hidden" name="option" value="com_ninjaboard" />
					<input type="hidden" name="view" value="users" />
					<label for="fusergroup"><?= @text('Filter:') ?></label>
					<?= @ninja('paginator.usergroup') ?>
					&#160;&#160;
					<div class="button2-left" style="float: none; display: inline-block;">
					<div class="page" style="float:none;">					<a style="float:none;" href="<?= @route('option=com_ninjaboard&view=users&search=&usergroup=&limit=20&offset=0') ?>" title="<?= @text('Reset filtering') ?>"><?= @text('Reset') ?></a></div>
					</div>
				</form>
				
			</th>
		</tr>
	</thead>
</table>