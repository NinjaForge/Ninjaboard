<? /** $Id: slide.php 959 2010-09-21 14:33:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<script type="text/javascript">
	jQuery.noConflict();  
	jQuery(document).ready(function(){
	jQuery("#NBslide").treeview ({
		collapsed: true,
		animated: "medium",
		persist: "location"
	})
});
</script>
<div id="NBslide" class="NBslide">
<ul class="NBtreeview NBtreeview-slide">
<li><a href="<?= @route('option=com_ninjaboard&view=forums')?>">
	 	<?= 'Forum Index'?>
</a></li>
<? foreach (@$forums as @$i => @$forum) : ?>
	<?= @template('links') ?>
<? endforeach ?>
</ul>
</div>