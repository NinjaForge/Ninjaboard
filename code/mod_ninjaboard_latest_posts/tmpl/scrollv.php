<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>

<script src="media://com_ninja/js/jquery/jquery.js"/>
<script src="media://com_ninja/js/jquery/jquery.tools.min.js"/>
<style src="media://mod_ninjaboard_latest_posts/css/mod_ninjaboard_latest_posts.css" />

<div>
    <div class="scrollable<?= $module->id?> vertical">
        <?= @template('default_items') ?>
    </div>
    <div class="navi"></div>
</div>
<div style="clear:both;"></div>
<script type="text/javascript">
    (function($){
    	$(function() { 
    		$(".scrollable<?= $module->id?>").scrollable({
                speed: 1000,
                circular: true,
                vertical:true
            }).autoscroll().navigator();
        }); 
    })(ninja);
</script>
