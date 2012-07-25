<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if (!JFactory::getUser()->guest) : ?>

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

	<?= @helper('com://site/ninjaboard.template.helper.avatar.image', array(
		'class' => 'ninjaboard-avatar',
		'thumbnail' => 'small',
		'avatarurl' => @route('index.php?option=com_ninjaboard&view=avatar&id='.$me->id.'&thumbnail=small'),
		'profileurl' => $profileurl
	)) ?>
	<h4 class="ninjaboard-cpanel-title"><?= sprintf(@text('MOD_NINJABOARD_QUICKPANEL_HI'), $me->display_name) ?></h4>
	<div class="ninjaboard-cpanel-links">
		<ul>
			<? if($messages) : ?>
				<li><a href="<?= @route('index.php?option=com_ninjaboard&view=messages') ?>"><?= sprintf(@text('MOD_NINJABOARD_QUICKPANEL_MESSAGES'), '<strong>'.sprintf(@text('MOD_NINJABOARD_QUICKPANEL_UNREAD'), $unread).'</strong>') ?></a></li>
			<? endif; ?>
			<? if($watches) : ?>
				<li><a href="<?= @route('index.php?option=com_ninjaboard&view=watches') ?>"><?= @text('MOD_NINJABOARD_QUICKPANEL_SUBSCRIPTIONS') ?></a></li>
			<? endif; ?>
			<? if($profile) : ?>
				<li><a href="<?= $profileurl ?>"><?= @text('MOD_NINJABOARD_QUICKPANEL_PROFILE') ?></a></li>
			<? endif; ?>
			<? if($logout) : ?>
				<? $link = version_compare(JVERSION,'1.6.0','ge') ? 'com_users&task=user.logout&'. JUtility::getToken() .'=1' : 'com_user&task=logout' ?>
				<li><a href="<?= JRoute::_('index.php?option='.$link.'&return='.base64_encode(JURI::getInstance()->toString())) ?>"><?= @text('MOD_NINJABOARD_QUICKPANEL_LOGOUT') ?></a></li>
			<? endif; ?>
		</ul>
	</div>

<? endif ?>