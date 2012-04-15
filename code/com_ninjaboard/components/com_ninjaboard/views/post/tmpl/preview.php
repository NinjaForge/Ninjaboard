<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= str_replace(
    '<a href="', 
    '<a target="_blank" href="', 
    @ninja('bbcode.parse', array('text' => urldecode(KRequest::get('get.text', 'raw'))))
) ?>