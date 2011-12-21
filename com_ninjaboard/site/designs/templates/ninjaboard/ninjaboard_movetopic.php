<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
$this->document->addScript($this->templatePathLive.DL.'js'.DL.'jquery.min.js');
$this->document->addScript($this->templatePathLive.DL.'js'.DL.'ninjaboard.jquery.js');
?>
<script language="javascript" type="text/javascript">
	$j(document).ready(function(){try{
		$j("#<?php echo $this->forums[0]->id; ?>").addClass("jbForumSelected");

		$j("li").click(function () {
			$j("#jbForums").find("li").removeClass("jbForumSelected");
			$j(this).addClass("jbForumSelected");
			$j("input[name='forum']").val($j(this).attr("id"));
		});
	}catch(e){}});
</script>
<form action="<?php echo $this->action; ?>" method="post" id="josForm" name="josForm" class="form-validate">
	<div class="jbBoxTopLeft"><div class="jbBoxTopRight"><div class="jbBoxTop">
		<div class="jbTextHeader"><?php echo JText::_('NB_MOVETOPIC'); ?></div>
	</div></div></div>
	<div class="jbBoxOuter"><div class="jbBoxInner">
		<div class="jbLeft jbMargin5">
			<div><?php echo JText::sprintf('NB_MOVETOPICTOFORUM', $this->post->subject); ?></div>
			<ul id="jbForums"><?php
				$forumsCount = count($this->forums);
				for ($i = 0; $i < $forumsCount; $i ++) :
					$forum =& $this->getForum($i);?>
					<li id="<?php echo $forum->id; ?>"><?php echo $forum->category_name .' / '. $forum->name; ?><li><?php
				endfor; ?>
			</ul>
			<button type="submit" class="nb-buttons btnSubmit validate"><span><?php echo JText::_('NB_SUBMIT'); ?></span></button>
			<button type="button" class="nb-buttons btnCancel" onclick="history.back();return false;"><span><?php echo JText::_('NB_CANCEL'); ?></span></button>
		</div>
		<br clear="all" />
	</div></div>
	<div class="jbBoxBottomLeft"><div class="jbBoxBottomRight"><div class="jbBoxBottom"></div></div></div>
	<div class="jbMarginBottom10"></div>
	<input type="hidden" name="option" value="com_ninjaboard" />
	<input type="hidden" name="task" value="ninjaboardmovetopic" />
	<input type="hidden" name="forum" value="<?php echo $this->forums[0]->id; ?>" />
	<input type="hidden" name="topic" value="<?php echo $this->topic->id; ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>
