<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div id="NBblocks">
<ul>
<li><a href="<?= @route('option=com_ninjaboard&view=forums')?>">
	 	<?= 'Forum Index'?>
</a></li>
<? foreach (@$forums as @$i => @$forum) : ?>
	<? @$parent_id = 0 ?>
	<?= @template('links') ?>
<? endforeach ?>
</ul>
</div>