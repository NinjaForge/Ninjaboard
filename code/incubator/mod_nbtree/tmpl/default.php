<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if(@$params->get('layout') != 'accordion'):?>
<div class="dtreenb">
	<p><a href="javascript: d<?= @$module->id?>.openAll();">open all</a> | <a href="javascript: d<?= @$module->id?>.closeAll();">close all</a></p>
<? endif?>
    
	<script type="text/javascript">
		<!--
		d<?= @$module->id?> = new dTreeNB('d<?= @$module->id?>');
		
		d<?= @$module->id?>.config.inOrder=true;
		d<?= @$module->id?>.config.useSelection=false;
		
		<? if(@$params->get('layout') == 'bullets'):?>
		d<?= @$module->id?>.config.useLines=false;
		d<?= @$module->id?>.icon.nlPlus='modules/mod_nbtree/images/bullet_add.png';
		d<?= @$module->id?>.icon.nlMinus='modules/mod_nbtree/images/bullet_delete.png';
		<? endif?>
		
		<? if(@$params->get('layout') == 'arrows'):?>
		d<?= @$module->id?>.config.useLines=false;
		d<?= @$module->id?>.icon.nlPlus='modules/mod_nbtree/images/bullet_arrow_right.png';
		d<?= @$module->id?>.icon.nlMinus='modules/mod_nbtree/images/bullet_arrow_down.png';
		<? endif?>
		
		<? if(@$params->get('layout') == 'forum_icons'):?>
		d<?= @$module->id?>.config.useLines=false;
		d<?= @$module->id?>.icon.nlPlus='modules/mod_nbtree/images/bullet_toggle_plus.png';
		d<?= @$module->id?>.icon.nlMinus='modules/mod_nbtree/images/bullet_toggle_minus.png';
		<? endif?>
		
		<? if(@$params->get('layout') == 'accordion'):?>
		d<?= @$module->id?>.config.useLines=false;
		d<?= @$module->id?>.config.closeSameLevel=true;
		d<?= @$module->id?>.icon.nlPlus='modules/mod_nbtree/images/arrow_right.png';
		d<?= @$module->id?>.icon.nlMinus='modules/mod_nbtree/images/arrow_down.png';
		<? endif?>
		
		<? @$parent_id = '-1' ?>
		<?= @template('link') ?>
			<? foreach (@$forums as @$i => @$forum) : ?>
				<? @$parent_id = '0' ?>
				<?= @template('links') ?>
			<? endforeach ?>
		document.write(d<?= @$module->id?>);
		//-->
	</script>