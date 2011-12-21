<?php defined('_JEXEC') or die('Restricted access'); 

	$postView = $this->postPreview ? 'postPreview' : 'post';
	
	/*
	 * TODO - This is a very quick and dirty emoticon emoticon handler.
	 * This MUST be replaced asap
	 */
	
	// Smileys to find...
	$in = array( 	 ':)', 	
					 ':(',
					 ':o',
					 ':s',
					 '8)',
					 ':D'
	);
	// And replace them by...
	$out = array(	'<img border="0" src="'.NB_EMOTICONS_LIVE.'/ninjaboard_yellow/smiley_smile.png" alt=":)"/>',
					'<img border="0" src="'.NB_EMOTICONS_LIVE.'/ninjaboard_yellow/smiley_sad.png" alt=":("/>',
					'<img border="0" src="'.NB_EMOTICONS_LIVE.'/ninjaboard_yellow/smiley_surprised.png" alt=":o"/>',
					'<img border="0" src="'.NB_EMOTICONS_LIVE.'/ninjaboard_yellow/smiley_confused.png" alt=":s"/>',
					'<img border="0" src="'.NB_EMOTICONS_LIVE.'/ninjaboard_yellow/smiley_cool.png" alt="8)"/>',
					'<img border="0" src="'.NB_EMOTICONS_LIVE.'/ninjaboard_yellow/smiley_biggrin.png" alt=":D"/>'
	);

?>

		<div class="nbLeft">
		<?php if ($postView != 'postPreview') : ?>
			<?php if ($this->$postView->avatarFile) : ?>
			<img class="nbAvatar" src="<?php echo $this->$postView->avatarFile; ?>" alt="<?php echo $this->$postView->avatarFileAlt; ?>" />
			<?php else : ?>
			<img class="nbAvatar" src="/media/ninjaboard/avatars/_ninjaboard.noavatar.png" alt="No Avatar" />
			<?php endif; ?>
			<?php if ($this->$postView->authorLink) : ?>
			<a class="nbAuthorLink" href="<?php echo $this->$postView->authorLink; ?>"><?php echo $this->$postView->author; ?></a>
			<?php else : ?>
			<div class="nbAuthorLink"><?php echo $this->$postView->author; ?></div>
			<?php endif; ?>
			<?php echo "\t\t\t", '<div>', $this->$postView->userRank, '</div>', "\n"; ?>
			<?php if ($this->$postView->posts) echo "\t\t\t", '<div>', JText::_('NB_POSTS'), ': ', $this->$postView->posts, '</div>', "\n"; ?>
			<?php if ($this->$postView->rankFile) : ?>
			<img src="<?php echo $this->styleSheetPathLive.DL.'images'.DL.$this->$postView->rankFile; ?>" alt="<?php echo $this->$postView->userRank; ?>" />
			<?php endif; 
			if ($this->$postView->authorRole) {
				echo "\t\t\t", '<div class="'. $this->$postView->authorClass .'">'. $this->$postView->authorRole .'</div>', "\n";
			}
			if ($this->$postView->registerDate) {
				echo
					"\t\t\t", '<div>', JText::_('NB_JOINEDBOARD').'</div>', "\n",
					"\t\t\t", '<div>', $this->$postView->registerDate .'</div>', "\n";
			} ?>
			<br/>
			<img src="<?php echo $this->styleSheetPathLive.DL.'images'.DL.$this->$postView->onlineStateFile; ?>" alt="<?php echo $this->$postView->onlineStateAlt; ?>" title="<?php echo $this->$postView->onlineStateAlt; ?>" />
			<span style="vertical-align: top;"><?php
				echo ($this->$postView->onlineState) ? JText::_('NB_USERISONLINE') : JText::_('NB_USERISOFFLINE');
			?></span>
			<?php if ($this->$postView->postsByAuthorLink) : ?>
			<br/><a href="<?php echo $this->$postView->postsByAuthorLink; ?>"><?php echo JText::_('NB_VIEWALLUSERSPOSTS'); ?></a>
			<?php endif; ?>
		<?php endif; ?>
		</div>

		<div class="nbRight">
			<div class="nbBubbleWrapper">
				<div class="nbtl">
					<div class="nbtr">
						<div class="nbbl">
							<div class="nbbr">
								<div class="nbBubble"></div>

								<div class="nbFrom">
									<img src="<?php echo $this->$postView->postIcon->fileName; ?>" alt="<?php echo $this->$postView->postIcon->title; ?>" />
									<a href="<?php echo $this->$postView->postIconLink; ?>"><span><?php
										echo $this->$postView->subject; ?></span><?php
										echo JText::_('NB_ON'), ' ', $this->$postView->postDate;
									?></a>
								</div>

								<div class="nbPost">
									<?php  // TODO - the str_replace is put our emoticons in - very quick adn dirty, must replace!
											echo str_replace($in, $out,$this->bbcode->parse($this->$postView->text)); ?>
								</div>
								<?php  	$hasImages = count($this->$postView->attachmentImages);
										$hasFiles = count($this->$postView->attachmentFiles);
																		
								if ($hasImages || $hasFiles) { ?>
								<div class="nbAttachments">
									<?php 
									
									echo  	JText::_('Attachments:');
									
									if ($hasImages) { ?>
										<ul class="nbAttachImages">
									<?php	foreach ($this->$postView->attachmentImages as $attachmentImage){
										
											
											echo '<li><img alt="A Ninjaboard attachment" title="'.substr($attachmentImage,14).'" src="'.JURI::root().'components/com_ninjaboard/attachments/'.$attachmentImage.'"/></li>';
											
											} ?>
										</ul>
								<?php } 	
									//only build the list if there are actually files to display	
									if ($hasFiles) { ?>
									
										<ul class="nbAttachFiles">
									
									<?php	foreach ($this->$postView->attachmentFiles as $attachmentFile){ 
									
									echo '<li><a title="'.substr($attachmentFile,14).'" href="'.JURI::root().'components/com_ninjaboard/attachments/'.$attachmentFile.'">'.substr($attachmentFile,14).'</a></li>';
													
											} ?>
											
									</ul>
											
							<?php	}
									
									?>
								</div>
						<?php 	}
									if (! empty($this->$postView->signature)) : ?>
								<div class="nbSignature">
									<?php echo $this->bbcode->parse($this->$postView->signature); ?>
								</div>
								<?php endif; ?>

							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if ($postView != 'postPreview') : ?>
			<ul class="nbControls">
				<?php
					if ($this->$postView->buttonMoveTopic) : ?>
					<li>
            <a class="nb-buttons buttonMoveTopic" href="<?php echo $this->$postView->buttonMoveTopic->href; ?>">
						<span class="buttonMoveTopic"><?php echo $this->$postView->buttonMoveTopic->title; ?></span>
					  </a>
          </li><?php
					endif;
					if ($this->$postView->buttonLockTopicToggle) :
						$button = $this->$postView->buttonLockTopicToggle->function;
					?>
					<li>
            <a class="nb-buttons <?php echo $button; ?>" href="<?php echo $this->$postView->buttonLockTopicToggle->href; ?>">
						  <span class="<?php echo $button; ?>"><?php echo $this->$postView->buttonLockTopicToggle->title; ?></span>
					  </a>
          </li><?php
					endif;
					if ($this->$postView->buttonReportPost) : ?>
					<li>
            <a class="nb-buttons buttonReportPost" href="<?php echo $this->$postView->buttonReportPost->href; ?>">
						  <span class="buttonReportPost"><?php echo $this->$postView->buttonReportPost->title; ?></span>
					  </a>
          </li><?php
					endif;
					if ($this->$postView->buttonDelete) : ?>
					<li>
            <a class="nb-buttons buttonDelete" href="<?php echo $this->$postView->buttonDelete->href; ?>">
						  <span class="buttonDelete"><?php echo $this->$postView->buttonDelete->title; ?></span>
					  </a>
          </li><?php
					endif;
					if ($this->$postView->buttonEdit) : ?>
					<li>
            <a class="nb-buttons buttonEdit" href="<?php echo $this->$postView->buttonEdit->href; ?>">
						  <span class="buttonEdit"><?php echo $this->$postView->buttonEdit->title; ?></span>
					  </a>
          </li><?php
					endif;
					if ($this->$postView->buttonQuote) : ?>
					<li>
            <a class="nb-buttons buttonQuote" href="<?php echo $this->$postView->buttonQuote->href; ?>">
						  <span class="buttonQuote"><?php echo $this->$postView->buttonQuote->title; ?></span>
					  </a>
          </li><?php
				endif; ?>
			</ul>
			<?php endif; ?>
		</div>
		<div class="nbClr nbmt"></div>

