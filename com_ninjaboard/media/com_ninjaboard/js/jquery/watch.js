/*Plugin for ajaxifying "Email Updates" buttons in Ninjaboard*/

(function($){
	$.fn.ninjaboardWatch = function(settings){
		
		var buttons = this;
		
		buttons.click(function(event){
			event.preventDefault();

			var toggle = $(this).attr('data-watching') > 0 ? 'unwatch' : 'watch',
				options = settings[toggle],
				id		= $(this).attr('data-id') ? '&id='+$(this).attr('data-id') : '';
			
			jQuery.ajax({
			    type: 'POST',
			    url: options.url+id,
			    data: options.data,
			    dataType: 'json',
			    success: function(data){
			    	//About to unwatch
			    	if(buttons.attr('data-watching') > 0) {
			    		buttons.attr('data-watching', 0).removeClass(settings.active+' '+settings.hover);
			    		buttons.find('a').text(settings.lang.subscribe);
			    	//Not watching, but about to
			    	} else {
			    		buttons.attr('data-watching', 1).addClass(settings.active);
			    		buttons.find('a').text(settings.lang.subscribed);
			    		if(data.result.id) buttons.attr('data-id', data.result.id);
			    	}
			    }
			});
		}).hover(
			function(){
				if(buttons.attr('data-watching') > 0) {
					buttons.addClass(settings.hover);
					buttons.find('a').text(settings.lang.unsubscribe);
				}
			},
			function(){
				if(buttons.attr('data-watching') > 0) {
					buttons.removeClass(settings.hover);
					buttons.find('a').text(settings.lang.subscribed);
				}
			}
		);
		
		return this;
	};
})(jQuery);