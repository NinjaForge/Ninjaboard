<? /** $Id: preview.php 1787 2011-04-12 23:38:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= str_replace(
    '<a href="', 
    '<a target="_blank" href="', 
    @ninja('bbcode.parse', array('text' => urldecode(KRequest::get('get.text', 'raw'))))
) ?>