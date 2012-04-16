<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('com://site/ninjaboard.view.default.head') ?>
	    	
<?= @helper('behavior.deleteposts', array('element' => '.'.@id())) ?>

<? if($params['view_settings']['topic_layout'] == 'classic') : ?>
	<div class="header relative">
		<?= $pagination ?>
		<div style="clear:both"></div>
	</div>
<? endif ?>

<? $i = 0; $t = count($posts) ?>
<? foreach($posts as $post) : ?>
	<?= @template('com://site/ninjaboard.views.posts.default_item', array('post' => $post, 'forum' => $forum, 'user' => $user, 'topic' => $topic, 'delete_post_button' => $delete_post_button, 'params' => $params)) ?>
	<? if($t > ++$i) echo @helper('com://site/ninjaboard.template.helper.template.space') ?>
<? endforeach ?>

<? if($params['view_settings']['topic_layout'] == 'minimal') : ?>
	<?= $pagination ?>
<? else : ?>
	<div class="footer relative">
		<?= $pagination ?>
	</div>
<? endif ?>