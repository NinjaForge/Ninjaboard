<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('com://site/ninjaboard.view.default.head') ?>

<script type="text/javascript">
	ninja(function($){
		<? /* @route('view=post&tmpl=&format=json') fails on sites with SEF + URL suffixes turned on */ ?>
		var posts = $('.<?= @id() ?>'), url = '<?= KRequest::root() ?>/?option=com_ninjaboard&view=post&tmpl=&format=json', parts = [];
		posts.click(function(event){
			if($(event.target).hasClass('delete') && confirm(<?= json_encode(@text("Are you sure you want to delete this post? This action cannot be undone.")) ?>)){
				parts = event.currentTarget.id.split('-');
				$(event.target).addClass('spinning');
				
				event.preventDefault();
				
				$.post(
					url+'&id='+parts.pop(), 
					{
						action: "delete",
						_token: <?= json_encode(JUtility::getToken()) ?>
					},
					function(){
						$(event.target).removeClass('spinning');
						$(event.currentTarget).animate({height: 0, opacity: 0, margin: 0, padding: 0}, 'slow', function(){ $(this).remove(); });
					},
					'json');
			}
		});
	});
</script>

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