<?php defined('_JEXEC') or die('Restricted access');

	$this->document->addScript($this->templatePathLive.'/js/jquery.js');
	$this->document->addScript($this->templatePathLive.'/js/ninjaboard.jquery.js');
	$this->document->addScript($this->templatePathLive.'/js/jquery.jcarousel.pack.js');
	
	$this->document->addScriptDeclaration('
	<!--
		$j(document).ready(function(){try{
			$j("#'.$this->iconFunction.'").addClass("jcarousel-item-selected");
			$j("#nbIconPreview").html($j("#'.$this->iconFunction.'").html());
		
			$j("#subject").keyup(function () {
				$j("#changeable").text($j(this).val());
			}).keyup();
		
			$j("#jcarousel").jcarousel({
				scroll: 1,
				initCallback: carousel_initCallback
			});
		
			$j("#jcarousel li").click(function () {
				$j("#jcarousel").find("li").removeClass("jcarousel-item-selected");
				$j(this).addClass("jcarousel-item-selected");
				$j("input[name=\'icon_function\']").val($j(this).attr("id"));
				$j("#nbIconPreview").html($j(this).html()); 
			});
		
			$j("#addFile").click(function () {
				$j("#fileList")
					.append(\'<li><a class="remove" title="'.JText::_('NB_REMOVEATTACHMENT').'" onclick="$j(this).parent().remove(); return false;" href="#">Remove</a><input type="file" name="attachmentList[]" size="30" value="" /></li>\');
			});
		}catch(e){}});
		
		function removeMe($this) {
			$this.parent().remove();
		}
	
		function carousel_initCallback(carousel) {
			$j("#jcarousel-next").bind("click", function() {
				carousel.next();
				return false;
			});
	
			$j("#jcarousel-prev").bind("click", function() {
				carousel.prev();
				return false;
			});
		};

		function submitbutton(pressbutton) {
			var form = document.josForm;
			form.task.value = pressbutton;
			form.submit();
		}
	//-->
	');

	$this->loadbar = 'edittopic';
	
	//Show our text editor and plugins if enabled.
	if ($this->enableBBCode) {
		
		$this->document->addStylesheet($this->templatePathLive.'/js/markitup/skins/simple/style.css');
		$this->document->addStylesheet($this->templatePathLive.'/js/markitup/sets/bbcode/style.css');

		$this->document->addScript($this->templatePathLive.'/js/markitup/jquery.markitup.pack.js');
		$this->document->addScript($this->templatePathLive.'/js/markitup/sets/bbcode/set.js');
	
		$init = '$j(document).ready(function(){
					
					$j("#text").markItUp(mySettings);
				';
				
		//This allows for disabling emoticons
		if ($this->enableEmoticons) {
			$init .= ' 
						$j("#emoticons a").click(function() {
					        emoticon = $j(this).attr("title");
					        $j.markItUp( { replaceWith:emoticon } );
							return false;
					    });
					';
		}
		
		$init .= '});';
				
		$this->document->addScriptDeclaration($init);
	}
?>
	<div class="nbCategoryWrapper">
		<?php echo $this->loadTemplate('category'); ?>
	</div>
	<form action="<?php echo $this->action; ?>" method="post" id="josForm" name="josForm" class="form-validate" enctype="multipart/form-data">

		<div id="nbEditTopic">

			<fieldset>
				<legend><?php echo JText::_('NB_TOPICICON'); ?></legend>
				<div class="nbLeft nbmt">
					<div id="nbIconPreview"></div>
				</div>
				<div class="nbRight nbmt">
					<div id="jcarousel" class="jcarousel-skin-nfnb">
						<ul><?php
							foreach ($this->postIcons as $postIcon) :
							?><li id="<?php echo $postIcon->function; ?>"><img src="<?php echo $postIcon->fileName; ?>" width="32" height="32" title="<?php echo $postIcon->title; ?>" alt="<?php echo $postIcon->title; ?>" /></li><?php
							endforeach;
						?></ul>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<?php
					if ($this->enableGuestName) :
				?><div class="nbLeft"><label for="nbGuestName"><?php echo JText::_('NB_GUESTNAME'); ?>:</label></div>
				<div class="nbRight">
					<input id="nbGuestName" class="nbInputBox<?php
						echo $this->guestNameRequired ? ' required' : '';
					?>" type="text" name="guest_name" size="60" maxlength="255" value="<?php echo $this->post->guest_name; ?>" />
				</div>
				<?php endif; ?>

				<div class="nbLeft">
					<label for="subject"><?php echo JText::_('NB_SUBJECT'); ?>:</label>
				</div>
				<div class="nbRight">
					<input id="subject" class="nbInputBox required" type="text" name="subject" size="60" maxlength="255" value="<?php echo $this->post->subject; ?>" />
				</div>
			</fieldset>

			<fieldset>
				
				<div class="nbLeft nbClr">
					<?php echo JText::_('NB_Message');
							//we have disabled emoticons for now and will hard code in some temporarily 
							//$this->editor->getEmoticons('text'); # ToDo: Why is the emoticon set not loaded?
					if ($this->enableEmoticons) {		
					?>
					<div id="emoticons">
						<p>
						<a title=":)" href="#">
							<img border="0" src="<?php echo NB_EMOTICONS_LIVE; ?>/ninjaboard_yellow/smiley_smile.png" alt=":)"/>
						</a>
						<a title=":(" href="#">
							<img border="0" src="<?php echo NB_EMOTICONS_LIVE; ?>/ninjaboard_yellow/smiley_sad.png" alt=":("/>
						</a>
						<a title=":o" href="#">
							<img border="0" src="<?php echo NB_EMOTICONS_LIVE; ?>/ninjaboard_yellow/smiley_surprised.png" alt=":o"/>
						</a>
						<a title=":s" href="#">
							<img border="0" src="<?php echo NB_EMOTICONS_LIVE; ?>/ninjaboard_yellow/smiley_confused.png" alt=":s"/>
						</a>
						<a title="8)" href="#">
							<img border="0" src="<?php echo NB_EMOTICONS_LIVE; ?>/ninjaboard_yellow/smiley_cool.png" alt="8)"/>
						</a>
						<a title=":D" href="#">
							<img border="0" src="<?php echo NB_EMOTICONS_LIVE; ?>/ninjaboard_yellow/smiley_biggrin.png" alt=":D"/>
						</a>
						</p>
					</div>
					<?php } ?>
				</div>
				<div class="nbRight">
					<textarea id="text" class="nbEditor required" style="width: 500px; height: 275px;" rows="15" cols="70" name="text"><?php echo $this->post->text; ?></textarea>
				</div>

				<div class="nbLeft nbmt"></div>
				<div class="nbRight nbmt" id="nbSubmitButtons">
					<button type="submit" class="nb-buttons btnSubmit validate"><span><?php echo JText::_('NB_SUBMIT'); ?></span></button>
					<button type="button" class="nb-buttons btnPreview" onclick="submitbutton('ninjaboardpreviewtopic');"><span><?php echo JText::_('NB_PREVIEW'); ?></span></button>
					<button type="button" class="nb-buttons btnCancel" onclick="history.back();return false;"><span><?php echo JText::_('NB_CANCEL'); ?></span></button>
				</div>
			</fieldset>
			<?php if ($this->enableAttachments) : ?>
			
				
			<fieldset>
				<div class="nbLeft">
					<span><?php echo JText::_('NB_UPLOADATTACHMENT'); ?></span>
				</div>
				<div class="nbRight">
					<div id="attachmentList">
						<a class="nb-buttons buttonAddFile" id="addFile"><span class="buttonAddFile"><?php echo JText::_('NB_ADDFILE'); ?></span></a>
						<ul id="fileList">
							<?php 	if (count($this->attachmentsList) > 0) { 
							
										for ($i = 0; $i < count($this->attachmentsList); $i++){ 
										?>
				
									<li><label for="remAtch<?php echo $i; ?>" title="<?php echo JText::_('NB_REMOVEATTACHMENT'); ?>" ><span><?php echo JText::_('NB_REMOVEATTACHMENT'); ?></span><input type="checkbox" name="removeattach[]" value="<?php echo $this->attachmentsList[$i]->id; ?>" id="remAtch<?php echo $i; ?>"></label><?php echo substr($this->attachmentsList[$i]->file_name,14); ?> </li>
								
							<?php		}
									}?>
							
							
						</ul>
					</div>
				</div>
			</fieldset>
			<?php endif; ?>

			<h5 class="nbClr"><?php echo JText::_('NB_FURTHERPARAMETERS'); ?></h5>
			<div id="nbTopicParams">
				<dl>
					<dt><?php echo JText::_('NB_POSTTOPICAS'); ?></dt>
					<dd><?php echo $this->lists['topictype']; ?></dd>
					<dt><?php echo JText::_('NB_NOTIFYONREPLY'); ?></dt>
					<dd><?php echo $this->lists['notifyonreply']; ?></dd>
				</dl>
			</div>
		</div>
				
		<input type="hidden" name="option" value="com_ninjaboard" />
		<input type="hidden" name="task" value="ninjaboardsavetopic" />
		<input type="hidden" name="icon_function" value="<?php echo $this->iconFunction; ?>" />
		<input type="hidden" name="id_post" value="<?php echo $this->post->id; ?>" />
		<input type="hidden" name="id_topic" value="<?php echo $this->topic->id; ?>" />
		<input type="hidden" name="id_forum" value="<?php echo $this->forum->id; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
	<br />
	<div class="nbSeparator nbClr"></div>
	<br />
