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

<? if (file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')) : ?>
<? if(count(@$items)) : ?>

<div class="scrollable<?=$module->id?> vertical">
    <div class="items">

	<? foreach(@$items as @$i =>$item) : ?>
        <?= @template('topic'); ?>
    <? endforeach ?>
                   
    </div>
</div>
<div style="clear:both;"></div>

<? else : ?>
<?= JText::_('Unable to retrieve Items!'); ?>
<? endif ?>

<? else : ?>
<?= JText::_('Ninjaboard is not Installed!'); ?>
<? endif ?>
