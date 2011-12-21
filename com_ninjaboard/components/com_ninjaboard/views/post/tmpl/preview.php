<? /** $Id: preview.php 1768 2011-04-11 20:38:57Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= str_replace(
    '<a href="', 
    '<a target="_blank" href="', 
    @ninja('bbcode.parse', array('text' => urldecode(KRequest::get('get.text', 'raw'))))
) ?>