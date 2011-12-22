
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<a href="<?= @route('option=com_ninjaboard&view=topic&id=' . @$topic->id)?>">

  <?= @escape(@$topic->title)?>

</a>
