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

	width:<?=$width+20?>px;

	height:<?=($height*$num_cols)+(20*$num_cols)?>px;

}

.scrollable<?=$module->id?> .items .nb-posts { 

	width:<?=$width+5?>px;

	height:<?=$height+5?>px;

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

    <div class="buttons">

    	<a class="prev browse left"></a>&nbsp;<a class="next browse right"></a>

    </div>

</div>

<div style="clear:both;"></div>





<? ob_start();?>

jQuery.noConflict();

(function($){

	$(function() { 

		$(".scrollable<?=$module->id?>").scrollable({ 

            circular: true,

        	loop: true,

            vertical: true

        });

    }); 

})(jQuery);

<? $this->_doc->addScriptDeclaration(ob_get_clean()) ?>

<?php }else{echo JText::_('Unable to retrieve Items!');}?>

<?php }else{echo JText::_('Ninjaboard is not Installed!');}?>