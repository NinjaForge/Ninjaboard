<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if(!$latest_style) : ?>
<style src="media://com_ninjaboard/css/latest_posts.css" /></style>
<? endif; ?>

<? if($collapse_content) : ?>
<script type="text/javascript">
	ninja(function($){
		
		$('.ninjaboard-latest-preview-text<?= $module_id ?>').each(function(){ // remove BBCode tags for preview text
		
			//remove quotes first - will do nested quotes also.
			var quote_level = 0;
			var strip_bb = $(this).text().replace(/\[(\/?)quote[^\]]*\]|./gi, function(tag, slash) {
			    
			    if(tag.length > 1 && !slash.length) quote_level += 1; // Opening tag?
			    var strip = quote_level > 0; // What to strip
			    if(tag.length > 1 && slash.length) quote_level -= 1; // Closing tag?
			    if(strip) return '';
			    return tag;
			
			});
			
			// kill youtube links and the actual link
			strip_bb = strip_bb.replace(/\[youtube\](.*?)\[\/youtube\]/gi, '');
			
			// bye bye custom smilies - for things like :smilie:
			strip_bb = strip_bb.replace(/\:(.*?)\:/gi, '');
			
			// strip all other BBCode tags away
			strip_bb = strip_bb.replace(/\[\/?(?:b|i|u|url|quote|code|img|color|size)*?.*?\]/gim, '');
			$(this).text(strip_bb);
			
		});
		
		var expand_all='Expand All';
		var collapse_all='Collapse All';
		
		$('.ninjaboard-latest-post-content<?= $module_id ?>').hide();
		$('#mod-ninjaboard-latest-posts<?= $module_id ?>').parent().prepend('<div><a href="#" id="ninjaboard-latest-expandall" class="readon readmore button">'+expand_all+'</a></div>');
		
		$('.ninjaboard-latest-preview').click(function() { // Individual Post Expand/Collapse Toggle
			
			$(this).closest('.ninjaboard-latest-post').find('.ninjaboard-latest-preview-text<?= $module_id ?>').toggle();
			$(this).closest('.ninjaboard-latest-post').find('.ninjaboard-latest-post-content<?= $module_id ?>').slideToggle('slow');

			return false;
		});
		
		$('#ninjaboard-latest-expandall').click(function() { // Expand/Collapse All
			
			if($(this).text()==expand_all) { // we do this rather than toggle incase some posts are are already expanded/collapsed.
				
				$(this).text(collapse_all);
				$('.ninjaboard-latest-preview-text<?= $module_id ?>').hide();
				$('.ninjaboard-latest-post-content<?= $module_id ?>').slideDown('slow');
				
			} else {
				
				$(this).text(expand_all);
				$('.ninjaboard-latest-preview-text<?= $module_id ?>').show();
				$('.ninjaboard-latest-post-content<?= $module_id ?>').slideUp('slow');
			}

			return false;
		});
});
</script>
<?php endif; ?>

<div class="ninjaboard-latest-posts">
	<dl class="ninjaboard-latest-header">
		<dd class="ninjaboard-latest-poster">Posted by
		</dd>
		<dd class="ninjaboard-latest-subject">Topic
		</dd>
		<? if($collapse_content) : ?>
		<dd class="ninjaboard-latest-preview">Post Preview
		</dd>
		<? endif; ?>
		<dd class="ninjaboard-latest-date">Posted
		</dd>	
	</dl>
	
	<ul class="ninjaboard-latest">
	<? foreach ($posts as $post) : ?>
		<?= @template('com://site/ninjaboard.view.post.row_post', array('post' => $post, 'collapse_content' => $collapse_content, 'module_id' => $module_id, 
																	   'display_avatar' => $display_avatar)) ?>
	<? endforeach ?>
	</ul>
</div>