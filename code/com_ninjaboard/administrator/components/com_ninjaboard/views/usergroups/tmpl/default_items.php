<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<style type="text/css">
	.permission {
		-moz-border-radius: 3px;
		-moz-box-shadow: lightgray 0px 0px 5px;
		-webkit-border-radius: 3px;
		-webkit-box-shadow: lightgray 0px 0px 5px;
		background-color: rgba(20,20,20,0.5);
		background-image: -moz-linear-gradient(top, rgba(255,255,255,0.5), rgba(255,255,255,0.3) 40%, rgba(255,255,255,0.1) 80%, rgba(90,90,90,0.0) 80%, rgba(90,90,90,0.0));
		background-image: -webkit-gradient(
								linear, 
								left top, left bottom, 
								from(rgba(255,255,255,0.5)), 
								to(rgba(90,90,90,0.0)),
								color-stop(0.4, rgba(255,255,255,0.3)),
								color-stop(0.8, rgba(255,255,255,0.1)),
								color-stop(0.8, rgba(90,90,90,0.0)));
		border: 1px solid gray;
		color: white;
		display: inline-block;
		padding: 0.3em 0.5em 0.2em 0.5em;
		text-indent: 0px;
		text-shadow: rgba(0,0,0,0.6) 0px -1px 0px;
	}
	
	.permission.level-0 {
		background-color: red;
		border-color: red;
	}
	
	.permission.level-1, .permission.level-2, .permission.level-3 {
		background-color: green;
		border-color: green;
	}
	
	
	.permission.level-2 {
		background-color: #006636;
		border-color: #006636;
	}
	
	.permission.level-3 {
		background-color: #165B60;
		border-color: #165B60;
	}
	
	
	.permission .level {
		font-size: xx-small;
	}
	
	tr.selected .permission {
		-moz-box-shadow: transparent 0 0 0;
		-webkit-box-shadow: transparent 0 0 0;
		border-color: white;
		background-color: #FFF;
		color: hsl(213, 100%, 42%);
		text-shadow: transparent 0 0 0;
	}
	.selected-multiple tr.selected .permission {
		color: hsl(207, 77%, 53%);
	}
	tr.selected .permission.level-0 {
		color: red;
	}
	tr.selected .permission.level-1, tr.selected .permission.level-2, tr.selected .permission.level-3 {
		color: green;
	}
	tr.selected .permission.level-2 {
		color: #006636;
	}
	tr.selected .permission.level-3 {
		color: #165B60;
	}
</style>

<? foreach ($usergroups as $i => $usergroup) : ?>
<tr class="sortable">
	<td class="handle"></td>
	<?= @ninja('grid.count', array('total' => @$total)) ?>
	<td class="grid-check"><?= @helper('grid.checkbox', array('row' => $usergroup)) ?></td>
	<td><?= @ninja('grid.edit', array('row' => $usergroup)) ?></td>
	
		<? $permissions = $this->getService($this->getService($this->getView())->getModel())->getPermissions((int)$usergroup->id) ?>
		<? if (count($permissions) < 1) : ?>
			<td colspan="4">
				<?= @text('This usergroup doesn\'t have any permissions defined.') ?>
			</td>
		<? else : ?>
			<? foreach ($columns as $column => $title) : ?>
				<td style="white-space: nowrap;">
				<? if(isset($permissions[$column])) : ?>
					<? $permission = $permissions[$column] ?>
					<span class="permission level-<?= $permission->level ?>">
						<?/*= @text($permission->title) */?>
						<span class="level"><?= @text($this->getService('ninja:template.helper.access')->getlevel($permission->level)) ?></span>
					</span>
				<? else : ?>
					<?= sprintf(@text('%s is undefined.'), $title) ?>
				<? endif ?>
				</td>
			<? endforeach ?>
		<? endif ?>
		
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => @$total, 'colspan' => $colspan)) ?>