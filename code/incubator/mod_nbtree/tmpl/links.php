<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? @prepare() ?>
<? if(@$subforums = @$subforum->getList()) : ?>

	<? if (@$subforum->getTotal()>0) : ?>

		<? if(@$params->get('layout') == 'arrows' || @$params->get('layout') == 'bullets' || @$params->get('layout') == 'accordion') : ?>
        	<? @$Icon=JURI::base().'modules/mod_nbtree/images/noicon.png'?>
        	<? @$IconOpen=JURI::base().'modules/mod_nbtree/images/noicon.png'?>
        <? endif?>
    
    	<? if(@$params->get('layout') == 'forum_icons') :?>
    		<? @$Icon=url( @$img('/forums/'.$forum->params['customization']['icon']))?>
    		<? @$IconOpen=url( @$img('/forums/'.$forum->params['customization']['icon']))?>
    	<? endif?>

    	<?= @template('link') ?>
        <? @$subparent_id = @$forum->id ?>
		<? @$total = @$total + @$subforum->getTotal() ?>
        <? foreach (@$subforums as @$i => @$forum) : ?>
        	<? @$parent_id = @$subparent_id ?>
            <?= @template('links') ?>
		<? endforeach ?>
        
	<? else : ?>
		<? if(@$params->get('layout') == 'arrows'){@$Icon=JURI::base().'modules/mod_nbtree/images/bullet_grey.png';}?>
        <?= @template('link') ?>
	<? endif ?>
    
<? else : ?>
    <? @parent_id = @$forum->id ?>
	<?= @template('link') ?>
<? endif ?>