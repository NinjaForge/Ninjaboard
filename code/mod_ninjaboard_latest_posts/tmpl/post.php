<?php

/*

* @version		1.0.2

* @package		mod_ninjaboard_latest_posts

* @author 		NinjaForge

* @author email	support@ninjaforge.com

* @link			http://ninjaforge.com

* @license      http://www.gnu.org/copyleft/gpl.html GNU GPL

* @copyright	Copyright (C) 2010 NinjaForge - All rights reserved.

*/

defined( '_JEXEC' ) or die( 'Restricted access' );?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<a href="<?= @route('option=com_ninjaboard&view=post&id=' . @$post->id)?>">

  <?= @escape(@$post->title)?>

</a>
