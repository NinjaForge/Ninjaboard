// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
(function($){
var NB = {
	customPreview: function (markItUp) {
	    var preview_pane = $('#' + markItUp.textarea.id + '_preview');
	    var txtarea = $('#' + markItUp.textarea.id);
	    var txtarea_footer = txtarea.parent().find('.markItUpFooter');
	    
	    // Swap the preview pane and the edit pane
	    if(!preview_pane.is(":visible")) {
	      var formatted_text;
	      ninja.ajaxSetup( { async:false } );
	      var value = encodeURIComponent(markItUp.textarea.value)
											      				.replace(/!/g, '%21')
											      				.replace(/'/g, '%27')
											      				.replace(/\(/g, '%28')
											      				.replace(/\)/g, '%29')
											      				.replace(/\*/g, '%2A')
											      				.replace(/%20/g, '+');
	      $.get(myBbcodeSettings.previewParserPath, {text: value}, function(content){
	      	 formatted_text = content;
	      	 
	      	 //To prevent accidental editing
	      	 txtarea.blur();
	      	 
	      	 //IE needs a tiny timeout
	      	 if($.browser.msie) {
		      	 setTimeout(function(){
		      	 	txtarea.blur();
		      	 }, 100);
	      	 }
	      });
	      preview_pane.html('');
	      preview_pane.html(formatted_text);
	      
	      preview_pane.width(txtarea.width());
	      txtarea.add(txtarea.prev()).addClass('previewing');
	      txtarea_footer.hide();
	      preview_pane.show().css('position', 'absolute').css(txtarea.position()).width(txtarea.width()).height(txtarea.height());

	      txtarea.parent().find('.button_preview a').addClass('previewing');
	    } else {
	      preview_pane.hide();
	      preview_pane.html('');
	      txtarea.add(txtarea.prev()).removeClass('previewing');

	      txtarea_footer.show();
	      txtarea.parent().find('.button_preview a').removeClass('previewing');
	    }
	}
};
myBbcodeSettings = {
  nameSpace:          "bbcode", // Useful to prevent multi-instances CSS conflict
  previewParserPath:  "~/sets/bbcode/preview.php",
  markupSet: [
      {name:'Bold', key:'B', openWith:'[b]', closeWith:'[/b]'}, 
      {name:'Italic', key:'I', openWith:'[i]', closeWith:'[/i]'}, 
      {name:'Underline', key:'U', openWith:'[u]', closeWith:'[/u]'}, 
      {separator:'---------------' },
      {name:'Picture', key:'P', replaceWith:'[img][![Url]!][/img]'}, 
      {name:'Link', key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
      {separator:'---------------' },
      {name:'Colors', openWith:'[color=[![Color]!]]', closeWith:'[/color]', dropMenu: [
          {name:'Yellow', openWith:'[color=yellow]', closeWith:'[/color]', className:"col1-1" },
          {name:'Orange', openWith:'[color=orange]', closeWith:'[/color]', className:"col1-2" },
          {name:'Red', openWith:'[color=red]', closeWith:'[/color]', className:"col1-3" },
          {name:'Blue', openWith:'[color=blue]', closeWith:'[/color]', className:"col2-1" },
          {name:'Purple', openWith:'[color=purple]', closeWith:'[/color]', className:"col2-2" },
          {name:'Green', openWith:'[color=green]', closeWith:'[/color]', className:"col2-3" },
          {name:'White', openWith:'[color=white]', closeWith:'[/color]', className:"col3-1" },
          {name:'Gray', openWith:'[color=gray]', closeWith:'[/color]', className:"col3-2" },
          {name:'Black', openWith:'[color=black]', closeWith:'[/color]', className:"col3-3" }
      ]},
      {name:'Size', key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]', dropMenu :[
          {name:'Big', openWith:'[size=200]', closeWith:'[/size]' },
          {name:'Normal', openWith:'[size=100]', closeWith:'[/size]' },
          {name:'Small', openWith:'[size=50]', closeWith:'[/size]' }
      ]},
      {separator:'---------------' },
      {name:'Bulleted list', openWith:'[list]\n[*] ', closeWith:'\n[/list]'}, 
      {name:'Numeric list', openWith:'[list=]\n[*] ', closeWith:'\n[/list]'}, 
      {name:'List item', openWith:'[*] '}, 
      {separator:'---------------' },
      {name:'Quotes', openWith:'[quote]', closeWith:'[/quote]'}, 
      {name:'Code', openWith:'[code]', closeWith:'[/code]'}, 
      {separator:'---------------' },
      {name:'Preview', className:"button_preview", beforeInsert:function(markItUp) { return NB.customPreview(markItUp) } }
   ]
}
})(ninja);