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

<?php if (file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')){?>

<?php if(count($items)){?>

<? ob_start();?>

.scrollable<?=$module->id?> {

    overflow:hidden;

	position:relative;

	width:<?=$width?>px;

	height:<?=($height*$num_cols)?>px;

}

.scrollable<?=$module->id?> .items .nb-posts { 

	width:<?=$width?>px;

	height:<?=$height?>px;

}

<? $this->_doc->addStyleDeclaration(ob_get_clean()) ?>

<div>

    <div class="scrollable<?=$module->id?> vertical">

        <div class="items">

			<? foreach ($c = 1; $c <= @template(@$params->get('num_cols')); $c++) : ?>

            	<?= @template('default') ?>

            <? endfor; ?>

        </div>

    </div>

    <div class="navi"></div>

</div>

<div style="clear:both;"></div>

<? ob_start();?>

jQuery.noConflict();

(function($){

	$(function() { 

		$(".scrollable<?=$module->id?>").scrollable({

            speed: 1000,

            circular: true,

            loop:true,

            vertical:true

        }).autoscroll({

        	autopause: true,

    		autoplay: true,

            steps: 1, 

    		interval: 10000 

		}).navigator();

    }); 

})(jQuery);

<? $this->_doc->addScriptDeclaration(ob_get_clean()) ?>

<?php }else{echo JText::_('Unable to retrieve Items!');}?>

<?php }else{echo JText::_('Ninjaboard is not Installed!');}?>

