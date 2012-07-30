<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>

<div class="items">
    <? foreach ($items as $item) : ?>
        <div class="nb-posts <?= $item['unread'] ? 'unread' : 'read' ?>">
            <? 
                $title      = $item['forum'];
                $title     .= ($item['subject']) ? ' > '.$item['subject'].', ' : ', ';
                $title     .= @text('MOD_NINJABOARD_LATEST_POSTS_BY').':';
                $title     .= $item['display_name'];
                $title     .= ' '.@helper('ninja:template.helper.date.html', array('date' => $item['created_time'], 'html' => false));
                if ($params->get('name_link') == 'nb') 
                    $avatar     = @route('index.php?option=com_ninjaboard&view=avatar&id='.$item['created_user_id']);
                else 
                    $avatar     = JFile::exists($links['avatar'].$item['avatar']) ? JURI::root().$links['avatar'].$item['avatar'] : $links['davatar'];
            ?>
            <span title="<?= $title ?>">
                <a href="<?= @route($links['profile'].$item['created_user_id'].'&Itemid='.$itemID) ?>">
                    <img title="<?= $item['display_name'] ?>" src="<?= $avatar ?>" <?= $params->get('avatar_w_h', 'height') ?>="<?= str_replace('px', '', $params->get('avatar_size', 50)) ?>" alt="" id="nb-posts-avatar">
                </a>
                <a href="<?= @route('index.php?option=com_ninjaboard&view=topic&id='.$item['ninjaboard_topic_id'].'&Itemid='.$itemID.'#p'.$item['ninjaboard_post_id']) ?>">
                    <?= (strlen($item['subject']) > $params->get('subject_max', 50)) ? substr($item['subject'], 0, $params->get('subject_max', 50)-4).'...' : $item['subject']  ?>
                </a>
                <? $text = (strlen($item['text']) > $params->get('message_max', 200)) ? substr($item['text'], 0, $params->get('message_max', 200)-4).'...' : $item['text']  ?>
                <?= $this->getService('ninja:helper.bbcode')->parse(array('text' => $text)); ?>
            </span>
        </div>
    <? endforeach ?>
</div>