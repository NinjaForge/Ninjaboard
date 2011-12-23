<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @ninja('bbcode.parse', array('text' => urldecode(KRequest::get('get.text', 'raw')))) ?>