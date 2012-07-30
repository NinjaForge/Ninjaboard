<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>

<script src="media://com_ninja/js/jquery/jquery.js"/>
<script src="media://com_ninja/js/jquery/jquery.tools.min.js"/>
<style src="media://mod_ninjaboard_latest_posts/css/mod_ninjaboard_latest_posts.css" />

<div>
    <div class="scrollable<?= $module->id?> vertical">
        <?= @template('default_items') ?>
    </div>
    <div class="buttons">
    	<a class="prev"></a>&nbsp;<a class="next"></a>
    </div>
</div>
<div style="clear:both;"></div>

<script type="text/javascript">
    (function($){
    	$(function() { 
    		$(".scrollable<?= $module->id?>").scrollable({ 
                circular: true,
            	loop: true,
                vertical: true
            });
        }); 
    })(ninja);
</script>