/*
---

script: Notifications.js

description: Enables easy usage of webkitNotifications if available

license: MIT-style license.

requires:
- /Native

provides: [Notifications]

...
*/

var Notifications = new Class({

	Implements: Options,
	
	options: {
		image: '',
		title: 'Notifications Title',
		body: 'Notifications Body'
	},

	initialize: function(options){
		this.setOptions(options);
	},

	createNotification: function(){
		if(!window.webkitNotifications) return;
		
		if(this.checkPermission()) {
			this.requestPermission(this.createNotification.bind(this));
		} else {
			try
			{
				window.webkitNotifications.createNotification(this.options.image, this.options.title, this.options.body).show();
			} catch(Err) {
				alert(Err.message); // Will output a security error if we don't have permission.
			}
		}
	},

	requestPermission: function(){
		if(!window.webkitNotifications) return;
		
		return window.webkitNotifications.requestPermission();
	},

	checkPermission: function(){
		if(!window.webkitNotifications) return;
		
		return window.webkitNotifications.checkPermission();
	}

});

Notifications.createNotification = function(image, title, body){
	return new Notifications({image: image, title: title, body: body}).createNotification();
};

Notifications.requestPermission = function(options){
	return new Notifications(options).requestPermission();
};

Notifications.checkPermission = function(options){
	return new Notifications(options).checkPermission();
};


function notification()
{
	if(window.webkitNotifications) // If we have the Notification API
	{
		var nc = window.webkitNotifications; // Set the API to nc for easy access
		if(nc.checkPermission()) // 1 = Not Allowed, 2 = Denied, 0 = Allowed
		{
			nc.requestPermission(myNotification); // Request Permission with a callback to myNotification();
		} else { myNotification(); }
	}
}
function myNotification()
{
	try
	{
		var nc = window.webkitNotifications;
		var notif = nc.createNotification(null,"Notification Title","Notification Body"); // Parameters: string URL_TO_IMAGE, string Title, string Body
		notif.show(); // Show Notification
	} catch(Err) {
		alert(Err.message); // Will output a security error if we don't have permission.
	}
}