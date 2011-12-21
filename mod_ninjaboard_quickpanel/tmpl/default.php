<? /** $Id: default.php 2304 2011-07-27 23:43:33Z captainhook $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<style type="text/css">
	.ninjaboard-avatar {
		display: block;
		float: left;
	}
	.ninjaboard-cpanel-title {
	    float: left;
	    padding-left: 5px;
	    margin: 0;
	}
	.ninjaboard-cpanel-links {
	    clear: left;
	}
</style>

<?= @helper('site::com.ninjaboard.template.helper.avatar.image', array(
	'class' => 'ninjaboard-avatar',
	'thumbnail' => 'small',
	'avatarurl' => @route('index.php?option=com_ninjaboard&view=avatar&id='.$me->id.'&thumbnail=small'),
	'profileurl' => $profileurl
)) ?>
<h4 class="ninjaboard-cpanel-title"><?= sprintf(@text('Hi %s,'), $me->display_name) ?></h4>
<div class="ninjaboard-cpanel-links">
	<ul>
		<? if($messages) : ?>
		<li><a href="<?= @route('index.php?option=com_ninjaboard&view=messages') ?>"><?= sprintf(@text('Messages: (%s)'), '<strong>'.sprintf(@text('%d Unread'), $unread).'</strong>') ?></a></li>
		<? endif; ?>
		<? if($watches) : ?>
		<li><a href="<?= @route('index.php?option=com_ninjaboard&view=watches') ?>"><?= @text('My Watches') ?></a></li>
		<? endif; ?>
		<? if($profile) : ?>
		<li><a href="<?= $profileurl ?>"><?= @text('My Profile') ?></a></li>
		<? endif; ?>
		<? if($logout) : ?>
		<li><a href="<?= JRoute::_('index.php?option=com_user&task=logout&return='.base64_encode(JURI::getInstance()->toString())) ?>"><?= @text('Logout') ?></a></li>
		<? endif; ?>
	</ul>
</div>