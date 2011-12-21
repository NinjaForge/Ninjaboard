/**
 * SlidingBar - Vertical sliding bars for Ninjaboard
 *
 * Copyright (c) 2009 NinjaForge (http://ninjaforge.com)
 * Developer Stian Didriksen & Uwe Walter
 * Same license as Ninjaboard
 *
 * Built on top of the jQuery library
 *  http://jquery.com
 *
 * Successfully tested:
 *    Internet Explorer 6, 7
 *    Firefox 3.0.x, 3.1, 3.5b
 *    Konqueror 3.5.10
 *    Safari 4 (528.16)
 *    Chrome 1.5.154.64
 *    Opera 9.64
 */
jQuery.fn.slidingBar = function(options) {
	// define defaults and override with options, if available
	// by extending the default settings, we don't modify the argument
	settings = jQuery.extend({
		idPrefix:			"slidingBar-",
		triggerWrapper:		".nbCategoryHeader",
		contentSelector:	".slidingBar-content",
		triggerSelector:	".slidingBar-trigger",
		shadowSelector:		".nbBarShadow",
		contentIdPrefix:	"sContent-",
		triggerIdPrefix:	"sTrigger-",
		shadowIdPrefix:		"sShadow-"
	}, options);

	var contentHeight =  Array();
	var trigger       =  null;

	this.each(function (i) {
		//Set IDs
		jQuery(this).attr("id", settings.idPrefix+i);
		jQuery(this).find(settings.triggerSelector).attr("id", settings.triggerIdPrefix + i);
		jQuery(this).find(settings.contentSelector).attr("id", settings.contentIdPrefix + i);
		jQuery(this).find(settings.shadowSelector).attr("id",  settings.shadowIdPrefix  + i);

		var custEasing = 'easeInOutQuad';
		var custDur = 'slow';

		jQuery.easing.def = custEasing;


		//Set event
		jQuery("#" + settings.triggerIdPrefix + i).click(function () { 

			//Animate top margin property
			if(jQuery("#" + settings.contentIdPrefix + i + " > :first-child")) {

				//Get offset height
				var height = jQuery("#" + settings.contentIdPrefix + i).height();

				// On slideIn, remember content height for later slide out.
				if (height > 0) contentHeight[i] = height;

				if(jQuery("#" + settings.contentIdPrefix + i + " > :first-child").css("margin-top") == "0px") {
					// Slide in header easing.
					jQuery("#" + settings.contentIdPrefix + i + " > :first-child")
						.animate({marginTop: '-' + height}, {duration:custDur, queue:false, easing:custEasing});

					jQuery("#" + settings.contentIdPrefix + i + " > :last-child")
						.animate({marginBottom: height}, {duration:custDur, queue:false, easing:custEasing});

					if (jQuery.support.opacity)
						jQuery("#" + settings.contentIdPrefix + i + " > *")
							.animate({opacity: 0}, {duration:custDur, queue:false, easing:custEasing});

					jQuery("#" + settings.shadowIdPrefix + i)
						.animate({marginBottom: "-1px", height: "0px"}, {duration:custDur, queue:false, easing:custEasing});

					jQuery("#" + settings.idPrefix+i + " " + settings.triggerWrapper)
						.animate({bottom: "-5px"}, {duration:custDur, queue:true, easing:custEasing})
						.animate({bottom: "0px", marginBottom: "0px"}, {duration:custDur, queue:true, easing:custEasing});

					jQuery("#" + settings.contentIdPrefix + i)
						.animate({height: "0px"}, {duration:custDur, queue:false, easing:custEasing})
				}
				else {
					// Slide out header easing.
					jQuery("#" + settings.contentIdPrefix + i + " > :first-child")
						.animate({marginTop: '0px', paddingTop: '3px'}, {duration:custDur, queue:false, easing:custEasing});

					jQuery("#" + settings.contentIdPrefix + i + " > :last-child")
						.animate({marginBottom: '0px'}, {duration:custDur, queue:false, easing:custEasing});

					jQuery("#" + settings.shadowIdPrefix + i)
						.animate({height: "13px", marginBottom: "-10px"}, {duration:custDur, queue:false, easing:custEasing});

					if (jQuery.support.opacity)
						jQuery("#" + settings.contentIdPrefix + i + " > *")
							.animate({opacity: 1}, {duration:custDur, queue:false, easing:custEasing});

					jQuery("#" + settings.idPrefix+i + " " + settings.triggerWrapper)
						.animate({marginBottom: "0px"}, {duration:custDur, queue:false, easing:custEasing});

					jQuery("#" + settings.contentIdPrefix + i)
						.animate({height: contentHeight[i]+"px"}, {duration:custDur, queue:false, easing:custEasing})
				}
			}
			jQuery("#" + settings.idPrefix+i).toggleClass("closed");
		});

	});
}
