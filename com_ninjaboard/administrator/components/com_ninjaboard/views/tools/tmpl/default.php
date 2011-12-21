<? /** $Id: default.php 1782 2011-04-12 21:50:02Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<script type="text/javascript" src="/ProgressBar.js"></script>
<script type="text/javascript" src="/Notifications.js"></script>
<script type="text/javascript" src="/Request.Tools.js"></script>

<link rel="stylesheet" href="/ProgressBar.css" />
 
<script type="text/javascript">
	window.addEvent('domready', function(){
		new Request.Tools(<?= json_encode(array(
			'token' => JUtility::getToken(),
			'icon' => @$img('/256/ninjaboard.png'),
			'msg'   => array(
				'success'  => @text('Import complete! Converted {label} in {total}.'),
				'failure'  => @text('Import failed!'),
				'timeleft' => @text('timeleft'),
				'titleleft' => @text('titleleft'),
				'confirm'	=> @text('Import from {label} now&hellip;'),
				'warning'  => sprintf(@text("You're about to import from {label}.%sThe imported data will replace any current data.%sMake sure to take a backup on forehand,%s so you can recover if anything goes wrong."), "\n\n", "\n\n", "\n")
			)
		)) ?>);
		
		var setData = function(){		
			var request = document.getElement('.placeholder a.phpbb').retrieve('request');

			if(!request.options.data) request.options.data = {};
			
			$$('#phpbb .value').each(function(element){
				request.options.data[element.get('name')] = element.get('value');
				if(localStorage) localStorage.setItem(element.get('name'), element.get('value'));
			});
		};

		document.getElement('.placeholder a.phpbb').addEvent('mousedown', setData);
		$$('#phpbb .value').addEvent('change', setData);
		
		if(localStorage) {
			$$('#phpbb .value').each(function(element){
				element.set('value', localStorage.getItem(element.get('name')) ? localStorage.getItem(element.get('name')) : element.get('value'));
			});
		}
		
		/*$('juser').addEvent('change', function(){
			this.get('checked') ? this.getParent().removeClass('hide') : this.getParent().addClass('hide');
		});*/
	});
	
	// Preload some images
	new Asset.images(<?= json_encode(array(@$img('/32/apply.png'), @$img('/32/cancel.png'))) ?>);
	
</script> 

<style type="text/css">
	\@-webkit-keyframes 'fadeIn' {
	    0% { opacity: 0; -webkit-transform: scale(.9) translateY(15px); }
	  100% { opacity: 1; -webkit-transform: scale(1) translateY(0); } 
	}
	
	.placeholder {
		min-height: 22em;
	}
	
	.placeholder h1.title {
		-webkit-animation: fadeIn 600ms 1 ease-out;
		-webkit-transform-origin: top center;
		top: 40px;
	}
	
	.placeholder a.button, .placeholder a.button:visited, .placeholder a.button:link {
		margin: 115px auto 20px auto;
	}
	
	.placeholder a.button {
		-webkit-animation: fadeIn 600ms 1 ease-out;
		-webkit-transform-origin: bottom center;
	}
	.placeholder a.button.blur {
		//opacity: 0.8;
	}
	.placeholder a.button.confirm {
		margin-top: 10px;
		padding: 10px 15px;
		font-size: 18px;
		clear: both;
		display: inline-block;
		-webkit-animation: none;
	}
	.placeholder .form {
		display: block;
		margin: 0 37.5%;
		width: 25%;
	}
	.placeholder .description {
		text-align: left;
		font-size: 13px;
		padding: 0;
		margin: 0 5px;
		line-height: 18px;
		font-family: Arial;
		text-shadow: rgba(255,255,255,0.4) 0 1px 0;
	}
	.placeholder fieldset {
		background: white;
		background-color: hsla(0, 0%, 100%, 0.4);
		display: block;
		margin: 0 0 10px 0;
		border: 1px #CCC solid;
		border-color: hsla(0, 0%, 20%, 0.2);
		-webkit-background-clip: border;
		-webkit-background-origin: border;
		-moz-background-clip: border;
		-moz-background-origin: border;
		background-clip: border-box;
		background-origin: border-box;
	}
	.placeholder fieldset, .placeholder fieldset form > :only-child {
		-webkit-border-radius: 5px!important;
		-moz-border-radius: 5px!important;
		border-radius: 5px!important;
	}
	.placeholder fieldset form > :first-child {
		-webkit-border-top-left-radius: 5px;
		-webkit-border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-moz-border-radius-topright: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
	}
	.placeholder fieldset form > :last-child {
		-webkit-border-bottom-left-radius: 5px;
		-webkit-border-bottom-right-radius: 5px;
		-moz-border-radius-bottomleft: 5px;
		-moz-border-radius-bottomright: 5px;
		border-bottom-left-radius: 5px;
		border-bottom-right-radius: 5px;
	}
	.placeholder span span {
		background-image: none;
	}
	.placeholder a.button:active span span {
		background-image: -webkit-gradient(linear, left top, left bottom, from(hsla(0, 0%, 0%, .3)), to(hsla(0, 0%, 0%, .3)));
	}
	.toolbar .spacer {
		display: none;
	}
	.placeholder {
		opacity: 0;
	}
	.spinner {
		background: transparent;
		background-color: hsl(0, 0%, 98%);
		background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(hsla(0, 100%, 100%, 0.199219)), to(hsla(0, 0%, 0%, 0.199219)));
		background-image: -moz-linear-gradient(top, rgba(255,255,255,0.2), rgba(0,0,0,0.2));
		background-position: 50% 0%;
		background-repeat: repeat-x;
		
		/* @group To override the default .spinner styling */
//		filter: alpha(opacity=100);
//		opacity: 1;
//		position: relative;
//		z-index: auto;
		/* @end */
		
//		left: 0;
//		left:50%;
//		right: 0;
//		right:50%;
//		text-align: center;
//		top: 200px;
//		top: 64px;
//		position: absolute;
//		width: auto!important;
	}
	.spinner-img {
		background-image: url(<?= @$img('/32/spinner.gif') ?>);
		background-position: left top;
		height: 32px;
		left: 50%;
		margin-left: -16px;
		position: absolute;
		right: 50%;
		top: 0;
		width: 32px;
	}
	.spinner-msg, .spinner-img::after {
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		background-image: -webkit-gradient(linear, left top, left bottom, from(hsla(0, 0%, 0%, 1)), to(hsla(0, 0%, 40%, 0.8)));
		background-repeat: repeat-y;
		background-size: 100% 29px;
		color: #595959;
		font-size: 24px;
		font-weight: normal;
		left: 0px;
		line-height: 27px;
		margin: 0 20px;
		position: relative;
		right: 0px;
		text-align: center;
		text-shadow: rgba(255,255,255,0.4) 0 1px 0;
		top: 40px;
	}
	
	.spinner.failed .spinner-msg {
		background-image: -webkit-gradient(linear, left top, left bottom, from(hsla(0, 50%, 50%, 1)), to(hsla(0, 50%, 25%, 0.8)));
		font-weight: bold;
	}
	.spinner-img.progress.progress-100, .spinner.success .spinner-img {
		background-image: url(<?= @$img('/32/apply.png') ?>);
	}
	.spinner-img.progress.progress-failure, .spinner.failed .spinner-img {
		background-image: url(<?= @$img('/32/cancel.png') ?>);
	}
	.spinner-img.progress {
		width: 232px;
		margin-left: -116px;
		margin-left: -146px;
	}
	.spinner-img progress {
		position: relative;
		left: 42px;
		top: 7px;
		width: 200px;
		height: 22px;
	}
	.spinner-img::after {
		//content: attr(data-progress);
		display: block;
		left: 100%;
		position: relative;
		text-align: left;
		top: -19px;
	}
	
	label.hide span, label.hide strong {
		display: none;
	}
</style>

<?= @$menu ?>

<? foreach($converters as $name => $converter) : ?>
	<? if($converter->getLayout()) : ?>
		<div id="<?= $name ?>-form" style="display: none" class="form">
			<?= @template($converter->getLayout(), array('config' => $config, 'converter' => $converter)) ?>
			<div></div>
		</div>
	<? endif ?>
<? endforeach ?>