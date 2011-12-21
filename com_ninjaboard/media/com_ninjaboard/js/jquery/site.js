jQuery(function($){
	$('li.row', '#ninjaboard').hover(function(){
		if($(this).is(':first-child')) {
			$(this).closest('.category').find('li.header').addClass('hover-next');
		} else {
			$(this).prev().addClass('hover-next');
		}
		$(this).next().addClass('hover-previous');
	}, function(){
		if($(this).is(':first-child')) {
			$(this).closest('.category').find('li.header').removeClass('hover-next');
		} else {
			$(this).prev().removeClass('hover-next');
		}
		$(this).next().removeClass('hover-previous');
	});

	//Shortcut key for reply topic
	//ALT-R or ALT-SHIFT-R (Windows, Linux) or Ctrl-Option-R or Ctrl-R on Mac OS X (depending on the browser!) 
	$('.new-topic a', '.ninjaboard').first().attr('accesskey', 'n');
	
	//Shortcut key for new topic
	//ALT-N or ALT-SHIFT-N (Windows, Linux) or Ctrl-Option-N or Ctrl-N on Mac OS X (depending on the browser!) 
	$('.reply-topic a', '.ninjaboard').first().attr('accesskey', 'r');
});

//Set the right padding on domready, and on load to catch any images
(function($){
	var setBottomPadding = function(){
		$('.footer.signature', '.ninjaboard').each(function(){
			$(this).parent().css('padding-bottom', Math.max($(this).innerHeight() + 20, 60));
		});
	};
	$(setBottomPadding);
	$(window).load(setBottomPadding);
})(jQuery);