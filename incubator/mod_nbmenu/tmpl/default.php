<? /** $Id: default.php 959 2010-09-21 14:33:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if(@$params->get('layout')!='') : ?>
<?= @template(@$params->get('layout'))?>
<? else : ?>
<ul>
<li><a href="<?= @route('option=com_ninjaboard&view=forums')?>"><?= 'Forum Index'?></a>
</li>
<? foreach (@$forums as @$i => @$forum) : ?>
	<? @$parent_id = 0 ?>
	<?= @template('links') ?>
<? endforeach ?>
</ul>
</div>
<? endif?>