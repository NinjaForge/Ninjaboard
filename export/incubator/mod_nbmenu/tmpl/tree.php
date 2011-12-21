<? /** $Id: tree.php 959 2010-09-21 14:33:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<script type="text/javascript">
	jQuery.noConflict();  
	jQuery(document).ready(function(){
	jQuery("#NBbrowser").treeview ({
		collapsed: true,
		animated: "medium",
		control:"#NBtreecontrol",
		persist: "location"
	})
});
</script>
<div id="NBbrowser">
<div id="NBtreecontrol">
                <a title="Collapse the entire tree below" href="#"><img src="<?=JRoute::_('modules/mod_nbmenu/images/bullet_delete.png')?>" /> Collapse All</a>&nbsp;|&nbsp;
                <a title="Expand the entire tree below" href="#"><img src="<?=JRoute::_('modules/mod_nbmenu/images/bullet_add.png')?>" /> Expand All</a>
</div>
<ul class="NBtreeview NBfiletree NBtreeview-famfamfam">
<li><a href="<?= @route('option=com_ninjaboard&view=forums')?>">
	 	<?= 'Forum Index'?>
</a></li>
<? foreach (@$forums as @$i => @$forum) : ?>
	<?= @template('links') ?>
<? endforeach ?>
</ul>
</div>