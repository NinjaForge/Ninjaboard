<? /** $Id: preview.php 959 2010-09-21 14:33:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @ninja('bbcode.parse', array('text' => urldecode(KRequest::get('get.text', 'raw')))) ?>