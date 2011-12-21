<? /** $Id: head.php 2413 2011-08-26 14:12:28Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/pagination.css" />
<link rel="stylesheet" href="/toolbar.css" />
<link rel="stylesheet" href="/site.css" />

<?= @helper('behavior.mootools') ?>
<?= @ninja('behavior.ninja') ?>
<script type="text/javascript" src="media://lib_koowa/js/koowa.js"></script>
<script type="text/javascript" src="/toolbar.js"></script>
<script type="text/javascript" src="/site.js"></script>