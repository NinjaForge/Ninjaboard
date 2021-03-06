/*
---

script: Request.Tools.js

description: The Tools application, a subclass to Request

license: MIT-style license.

requires:
- /Native

provides: [Request.Tools]

...
*/

Request.Tools = new Class({

	Extends: Request.JSON,
	
	options: {
		token: false,
		icon: false,
		placeholder: false,
		title: false
	},
	
	timer: 0,

	initialize: function(options){

		this.options.placeholder = document.getElement('.placeholder');
		this.options.title = document.getElement('title').set('data-title', document.getElement('title').get('text'));

		this.parent(options);
	
		var buttons = $$('.placeholder a'),
			sizes   = buttons.getSize(),
			width   = 0,
			x;
			
		sizes.each(function(size){
			x = size.x-62;
			if(x > width) width = x;
		});

		buttons.setStyle('width', width);
		
		buttons.getChildren().each(function(span){
			bg = span[0].getStyle('background-image');		
			span[0].getElement('span').style.webkitMaskImage = bg;
			
		});

		this.options.placeholder.fade('hide');
		
		(function(){		
			this.options.placeholder.fade('in');
		}.bind(this)).delay(400);
		
		var links = this.options.placeholder.getElements('a')
			msg   = this.options.msg,
			token = this.options.token,
			limit = this.options.limit.toInt(),
			icon  = this.options.icon,
			time  = 0,
			start = 0,
			left  = 0,
			end   = 0,
			dur   = [],
			timer = 0;

		//Desktop notifications
		links.addEvent('click', function(){
			if(Notifications.checkPermission()) Notifications.requestPermission();
		});

		links.store('request', this);
		links.each(function(link){
			var name = link.get('data-name'),
				form = $(name+'-form'),
				request = this.createRequest.pass([link, name], this);
			
			//Converter has a form	
			if(form) {
				this.options.placeholder.grab(form.adopt(new Element('a', {
					'class': 'confirm button',
					html: this.options.msg.confirm.replace('{label}', link.get('text')),
					events: {
						click: request
					}
				})));
				link.addEvents({
					click: function(){
						if(!link.hasClass('active')) {
							link.fireEvent('select');
						} else {
							link.fireEvent('deselect');
						}
						
					}.bindWithEvent(this),
					select: function(){
						link.addClass('active');
						link.getSiblings().addClass('blur');
						form.reveal();
					}.bindWithEvent(this),
					deselect: function(){
						link.removeClass('active');
						link.getSiblings().removeClass('blur');
						form.dissolve();
					}.bindWithEvent(this),
					mousedown: function(){
						link.getSiblings().fireEvent('deselect');
					}
				});
			} else {
				link.addEvent('click', request);
			}

//			var name =form = 
//			console.error(link, link.get('data-name'), this.options.placeholder, $(link.get('data-name')+'-form'));
			
			//link.addClass('active');
			//return;
			
		}, this);
	
	},
	
	createRequest: function(link, name, importing){
		//Abort if the user don't confirm
		if(this.options.msg.warning && !confirm(this.options.msg.warning.replace('{label}', link.get('text')))) return;

		if(!importing) importing = 'Importing from %s';
		// @TODO make translatable
		this.options.msg.importing = importing.replace('%s', link.get('text'));

		var request = this, total = new Date, spinner = new Spinner(this.options.placeholder, $merge(this.options.title.get('spinner:options'), {message: this.options.msg.importing}));

		this.options.placeholder.store('spinner', spinner);
		
		//@TODO icons are too blurry
		//icon = link.getElement('span').getStyle('background-image').replace(/^url\(/, '').replace(/\)$/, '');

		var instance = new Request.JSON({
		    url: new URI(window.location).setData({action: 'import', format: 'json', 'import': name}, true).toString(),
		    placeholder: this.options.placeholder,
		    title: this.options.title,
		    
		    onRequest: function(){
		    	spinner.show();
		    },
		    onSuccess: function(response){
				if(response.splittable) {
		    		var data = new Hash(this.options.data),
		    			steps = response.steps * this.options.data.limit, 
		    			/*progress = new Element('progress', {
		    				max: steps
		    			}),*/
		    			spinner = this.options.placeholder.get('spinner', {message: this.options.spinnerOptions.message}).hide(true).show(true).img/*.adopt(progress)*/,
		    			value = 0;

		    		progress = new ProgressBar({container: this.options.placeholder.get('spinner').img, displayText: true});
		    		spinner.addClass('progress');
		    		this.options.placeholder.get('spinner').content.setStyles({left: '0px', right: '0px'});
		    		
		    		time = new Date;

				// In order for this to work in Gecko, Presto and Trident, we need a small timeout.
				(function(){
					new Request.JSON({
						
						className: '',
						
						url: this.options.url,
						
						placeholder: this.options.placeholder,
						title: this.options.title,
												
						data: data.extend({offset: 0}),
																
						onRequest: function(){
							start = time = new Date;
						},

						onSuccess: function(){
							value = ( ( this.options.data.offset / steps ) * 100).toInt();
							spinner.removeClass(this.options.className);
							this.options.className = 'progress-' + value;
							spinner.addClass('progress-' + value).set('data-progress', value + '%');

							
							//this.options.placeholder.get('spinner').msg.setStyles({left: '0px', right: 0}).set('text', msg.success);

							//progress.set('value', this.options.data.offset);
							progress.set(value);
							
							if(this.options.data.offset >= steps) {
								/*(function(){
									this.hide();
								}.bind(this.options.placeholder.get('spinner', {message: msg.success}).hide(true).show(true))).delay(1200);
								//*/
								var message = msg.success.replace('{label}', link.get('text')).replace('{total}', total.timeDiff());
								Notifications.createNotification(icon, document.title,  message);
								this.options.placeholder.get('spinner').msg.set('text', message);
								$clear(timer);
								this.options.title.set('text', message + ' | ' + this.options.title.get('data-title'));
								request.fireEvent('complete');
								return this;
							}
							
							this.options.data.offset += this.options.data.limit;
							dur.push(time.clone().diff(new Date, 'ms'));
							left = ( steps - this.options.data.offset ) / this.options.data.limit;
							this.now = new Date;
							this.end = new Date().increment('ms', Math.max(dur.average() * left, 1));
							
							this.options.placeholder.get('spinner').msg.set('text', msg.importing + msg.timeleft.replace('%s',this.end.timeDiffInWords(this.now)));
							
							//request.timer = $clear(request.timer);
							//this.end.increment('second');
							
							var self = this;
							if(!timer){
								var updateTitle = (function(){
									var accurate = self.now.timeDiff(self.end.decrement('ms', 100));
									self.options.title.set('text', msg.titleleft.replace('%s', accurate) + self.options.title.get('data-title'));
									var relative = msg.timeleft.replace('%s', self.end.timeDiffInWords(self.now));
									self.options.placeholder.get('spinner').msg.set('text', msg.importing + relative);
									
									
								});
								updateTitle();
								timer = updateTitle.periodical(100);
							}

							time = new Date;

							this.post();
						},

						onFailure: function(){
							request.onRequestFailure.run($A(arguments), request);
						}

					}).post();
					
				}.bind(this)).delay(100);
		    	} else {
		    	    var message = msg.success.replace('{label}', link.get('text')).replace('{total}', total.timeDiff());
		    		Notifications.createNotification(icon, document.title,  message);
		    		this.options.title.set('text', message + ' | ' + this.options.title.get('data-title'));
		    		var spinner = this.options.placeholder.get('spinner')
		    		spinner.msg.set('html', message);

		    		spinner.hide(true).show(true);
		    		spinner.element.addClass('success');
		    		request.fireEvent('complete');
		    	}
		    	
		    },
		    onFailure: function(){
		    	var spinner = this.options.placeholder.get('spinner')
		    	spinner.msg.set('html', message);
				spinner.hide(true).show(true);
		    	this.options.placeholder.get('spinner').element.addClass('failed');
		    	Notifications.createNotification(icon, document.title,  msg.failure);
		    }
		}).post(new Hash({
			action: 'import',
			format: 'json',
			'import': name,
			'_token': token,
			limit: limit
		}).extend(this.options.data).getClean());
	},
	
	onRequestFailure: function(){
		this.options.placeholder.get('spinner').img.addClass('progress-failure');
		//progress.set('value', this.options.data.offset);
		$clear(this.timer);
		this.options.title.set('text', this.options.msg.failure + ' | ' + this.options.title.get('data-title'));

		this.options.placeholder.get('spinner').msg.setStyle('width', this.options.placeholder.get('spinner').msg.getWidth()).set('text', this.options.msg.failure);
		Notifications.createNotification(icon, document.title,  this.options.msg.failure);
		//placeholder.get('spinner', {message: msg.failure}).hide(true).show(true);
		//placeholder.get('spinner').element.addClass('failed');
	}

});