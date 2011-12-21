(function($){

    window.addEvent('domready', function(){
        $$('#ninjaboard li.row').addEvents({
            mouseenter: function(){
                if(this.match(':first-child')) {
                	this.getParent('.ninjaboard-block').getElement('li.header').addClass('hover-next');
                } else {
                	this.getPrevious().addClass('hover-next');
                }
                if(this.getNext()) this.getNext().addClass('hover-previous');
            },
            mouseleave: function(){
                if(this.match(':first-child')) {
                	this.getParent('.ninjaboard-block').getElement('li.header').removeClass('hover-next');
                } else {
                	this.getPrevious().removeClass('hover-next');
                }
                if(this.getNext()) this.getNext().removeClass('hover-previous');
            }
        });
    
    	//Shortcut key for reply topic
    	//ALT-R or ALT-SHIFT-R (Windows, Linux) or Ctrl-Option-R or Ctrl-R on Mac OS X (depending on the browser!) 
    	$$('.ninjaboard .new-topic a').getFirst().setProperty('accesskey', 'n');
    	
    	//Shortcut key for new topic
    	//ALT-N or ALT-SHIFT-N (Windows, Linux) or Ctrl-Option-N or Ctrl-N on Mac OS X (depending on the browser!) 
    	$$('.ninjaboard .reply-topic a').getFirst().setProperty('accesskey', 'r');
    
        //Browser got history support, lets use it to prevent page reloads when post links are clicked
    	if(typeof history.replaceState !== 'undefined') {
        	$$('.ninjaboard-post-permalink').addEvent('click', function(event){
        	    if(this.hash) {
        	        //Prevent the page from reloading
            	    event.preventDefault();
            	    //Set the hash to scroll the page to the post
            	    window.location.hash = this.hash;
            	    //Set the current url to the real post permalink without reloading the page
            	    history.replaceState({}, '', this.href);
        	    }
    	    });
    	}
    });
    
    //Set the right padding on domready, and on load to catch any images
    var setBottomPadding = function(){
    	$$('.ninjaboard .ninjaboard-post-footer').each(function(element){
    		element.getParent().setStyle('padding-bottom', Math.max(element.getSize().y + 20, 60));
    	});
    };
    window.addEvents({load: setBottomPadding, domready: setBottomPadding});

})(document.id);