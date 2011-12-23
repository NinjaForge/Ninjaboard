<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<a href="<?= @route('option=com_ninjaboard&view=forum&id=' . @$forum->id)?>">
  <?= @escape(@$forum->title)?>
</a>