ninja(function($){
	$('.message-header-disclosure-triangle').live('click', function(){
		$(this).closest('.splitview').toggleClass('message-header-expand');
	});
});