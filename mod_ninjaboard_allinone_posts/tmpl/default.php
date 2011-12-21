<?php
/*
* @version		1.0.7
* @package		mod_ninjaboard_allinone_posts
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
	position:relative;
	width:<?php echo ($width*$num_cols)+(20*$num_cols)?>px;
	height:<?php echo ($height*$x)+(20*$x)?>px;
}
.scrollable<?php echo $module_id?> .items .nb_posts_multi{
	width:<?php echo $width+15?>px;
	height:<?php echo ($height*$x)+(15*$x)?>px;
    margin:5px;
    float:left;
}
.scrollable<?php echo $module_id?> .items .nb_posts { 
	width:<?php echo $width+5?>px;
	height:<?php echo $height+5?>px;
}

<?php $document->addStyleDeclaration(ob_get_clean()) ?>
<div class="scrollable<?php echo $module_id?> vertical">
    <div class="items">
			<?php for ($c = 1; $c <= $num_cols; $c++) : ?>
            <?php if($num_cols>1){ echo '<div class="nb_posts_multi">'; } ?>
                <?php for ($i=(($c-1)*$x); $i < ($c*$x); $i++) : ?>
                <div class="nb_posts"><span title="<?php echo $items[$i]->tooltip ?>"><?php echo $items[$i]->title ?></span></div>
                <?php endfor; ?>
            <?php if($num_cols>1){ echo '</div>'; } ?>
            <?php endfor; ?>
    </div>
</div>
<div style="clear:both;"></div>   
<?php }else{echo JText::_('Unable to retrieve Items!');}?>
<?php }else{echo JText::_('Ninjaboard is not Installed!');}?>