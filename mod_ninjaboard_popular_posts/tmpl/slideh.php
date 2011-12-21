<?php
/*
* @version		1.0.7
* @package		mod_ninjaboard_popular_posts
* @author 		NinjaForge
* @author email	support@ninjaforge.com
* @link			http://ninjaforge.com
* @license      http://www.gnu.org/copyleft/gpl.html GNU GPL
* @copyright	Copyright (C) 2010 NinjaForge - All rights reserved.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>
<?php if (file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')){?>
<?php if(count($items)){?>
<?php $x = floor(count($items)/$num_cols); ?>
<?php ob_start();?>
.scrollable<?php echo $module_id?> {
    overflow:hidden;
	position:relative;
	width:<?php echo ($width*$num_cols)+(20*$num_cols)?>px;
	height:<?php echo ($height)+(20)?>px;
}
.scrollable<?php echo $module_id?> .items .nb-posts { 
	float:left;
	width:<?php echo $width+5?>px;
	height:<?php echo $height+5?>px;
}
<?php $this->_doc->addStyleDeclaration(ob_get_clean()) ?>
<div>
    <div class="scrollable<?php echo $module_id?>">
        <div class="items">
            <?php for ($c = 1; $c <= $num_cols; $c++) : ?>
                <?php for ($i=(($c-1)*$x); $i < ($c*$x); $i++) : ?>
                <div class="nb-posts"><span title="<?php echo $items[$i]->tooltip ?>"><?php echo $items[$i]->title ?></span></div>
                <?php endfor; ?>
            <?php endfor; ?>
        </div>
    </div>
    <div class="buttons">
    	<a class="prev browse left"></a>&nbsp;<a class="next browse right"></a>
    </div>
</div>
<div style="clear:both;"></div>
<?php ob_start();?>
jQuery.noConflict();
(function($){
	$(function() { 
		$(".scrollable<?php echo $module_id?>").scrollable({ 
            circular: true,
        	loop: true
        });
    }); 
})(jQuery);
<?php $this->_doc->addScriptDeclaration(ob_get_clean()) ?>
<?php }else{echo JText::_('Unable to retrieve Items!');}?>
<?php }else{echo JText::_('Ninjaboard is not Installed!');}?>
