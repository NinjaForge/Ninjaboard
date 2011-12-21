<? /** $Id: form.php 2470 2011-11-01 14:22:28Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.form.head') ?>

<? @ninja('behavior.tooltip', array('selector' => '[title].hasTip')) ?>
<? @ninja('behavior.livetitle', array('title' => $usergroup->title)) ?>

<form action="<?= @route('id='.$usergroup->id) ?>" method="post" id="<?= @id() ?>" class="-koowa-form">
	<div class="col width-50">
		<fieldset class="adminform ninja-form">
			<legend><?= @text('Details') ?></legend>
			<div class="element">	
				<label for="title" class="key"><?= @text('Title') ?></label>
				<input type="text" name="title" id="title" class="inputbox required value" rel="<?= @text('User groups require a title!') ?>" size="50" value="<?= @escape($usergroup->title) ?>" maxlength="150" />
			</div>
			<div class="element">	
				<label class="key hasTip" title="<?= @text("Visibility allows you to setup advanced acl without exposing it to your users. By only showing usergroups that makes sense to them, like 'Moderator', 'Administrator' and 'Banned'.") ?>"><?= @text('Visibility') ?></label>
				<? /* Needs to use other icons than the default enabled/disabled ones, or better yet should be iPhone style switches */ ?>
				<?= @ninja('select.statelist', array('name' => 'visible', 'id' => 'visible', 'attribs' => array('class' => 'validate-reqchk-byname label:\'visible\''), 'selected' => $usergroup->visible, 'yes' => 'Visible', 'no' => 'Hidden')) ?>
			</div>
		</fieldset>
		<fieldset class="adminform ninja-form">
			<legend><?= @text('Group permissions') ?></legend>
				
			<?= $permissions->render() ?>
		
		</fieldset>
	</div>
	<style type="text/css">
		#human-readable {
			margin-top: 149px;
		}
		#human-readable h3 {
			line-height: 37px;
			font-size: 13px;
		}
		#human-readable span {
			display: none;
		}
		
		/* @group forum condition */
			#human-readable.forum-0 .forum-0,
			#human-readable.forum-1 .forum-1,
			#human-readable.forum-2 .forum-2,
			#human-readable.forum-3 .forum-3 {
				display: inline;
			}
			
			/* @group special cases */
				/*#human-readable.topic-0.forum-0 .topic-0,
				#human-readable.forum-0.topic-1 .forum-0.topic-1,
				#human-readable.forum-0.topic-2 .forum-0.topic-2,
				#human-readable.forum-0.topic-3 .forum-0.topic-3 {
					display: inline;
				}
				
				#human-readable.forum-0.topic-1 .topic-1,
				#human-readable.forum-0.topic-2 .topic-2,
				#human-readable.forum-0.topic-3 .topic-3 {
					display: none;
				}*/
			/* @end */
		/* @end */
		
		/* @group topic condition */
			#human-readable.topic-0 .topic-0,
			#human-readable.topic-1 .topic-1,
			#human-readable.topic-2 .topic-2,
			#human-readable.topic-3 .topic-3 {
				display: inline;
			}
		/* @end */
		
		/* @group post condition */
			#human-readable.post-0 .post-0,
			#human-readable.post-1 .post-1,
			#human-readable.post-2 .post-2,
			#human-readable.post-3 .post-3 {
				display: inline;
			}
		/* @end */
		
		/* @group attachment condition */
			#human-readable.attachment-0 .attachment-0,
			#human-readable.attachment-1 .attachment-1,
			#human-readable.attachment-2 .attachment-2,
			#human-readable.attachment-3 .attachment-3 {
				display: inline;
			}
		/* @end */
	</style>
	<script type="text/javascript">
		window.addEvent('domready', function(){
			$$('#<?= @id('permissions') ?> tbody tr').each(function(object){
				var inputs = object.getElements('input');
				inputs.each(function(input){
					input.addEvent('change', function(inputs, object){

						$('human-readable').addClass(object.get('data-object')+'-'+input.get('value'));
					
						inputs.each(function(input){
							if(input == this) return;
							$('human-readable').removeClass(object.get('data-object')+'-'+input.get('value'));
						}.bind(input));

					}.pass([inputs, object], input));
				});
			});
			$$('#<?= @id('permissions') ?> [checked]').fireEvent('change');
		});
	</script>
	<div id="human-readable" class="col width-50">
		<h3>
			<?= @text('This usergroup') ?>
			<span class="forum-0"><?= @text('can\'t see any forums,') ?><br /></span>
			<span class="forum-1"><?= @text('can see forums,') ?><br /></span>
			<span class="forum-2"><?= @text('can create and browse forums,') ?><br /></span>
			<span class="forum-3"><?= @text('can administrate forums,') ?><br /></span>
			
			<span class="topic-0"><?= @text('can\'t see any topics,') ?><br /></span>
			<span class="topic-1"><?= @text('can browse topics,') ?><br /></span>
			<span class="topic-2"><?= @text('can create and browse topics,') ?><br /></span>
			<span class="topic-3"><?= @text('can moderate topics (move, split, merge, lock, delete, etc),') ?><br /></span>
			<!--<span class="forum-0 topic-1"><?= @text('can browse topics,') ?><br /></span>
			<span class="topic-2"><?= @text('can create and browse topics,') ?><br /></span>
			<span class="topic-3"><?= @text('can moderate topics (move, split, merge, lock, delete, etc),') ?><br /></span>-->
			
			<span class="post-0"><?= @text('can\'t see posts') ?><br /></span>
			<span class="post-1"><?= @text('can see posts, but not reply topics') ?><br /></span>
			<span class="post-2"><?= @text('can reply topics') ?><br /></span>
			<span class="post-3"><?= @text('can moderate posts (edit, delete, etc)') ?><br /></span>
			
			<span class="attachment-0"><?= @text('and don\'t have access to see or download attachments.') ?><br /></span>
			<span class="attachment-1"><?= @text('and can see and download attachments.') ?><br /></span>
			<span class="attachment-2"><?= @text('and can upload attachments.') ?><br /></span>
			<span class="attachment-3"><?= @text('and can moderate attachments (delete, etc).') ?><br /></span>
		</h3>
	</div>
</form>