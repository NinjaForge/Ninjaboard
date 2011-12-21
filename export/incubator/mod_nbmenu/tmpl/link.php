<? /** $Id: link.php 959 2010-09-21 14:33:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<a href="<?= @route('option=com_ninjaboard&view=forum&id=' . @$forum->id)?>">
  <?= @escape(@$forum->title)?>
</a>