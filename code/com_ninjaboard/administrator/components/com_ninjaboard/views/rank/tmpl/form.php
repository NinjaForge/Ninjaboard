<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.form.head') ?>

<?= @ninja('behavior.livetitle', array('title' => @$rank->title)) ?>

<form action="<?= @route('id='.@$rank->id) ?>" method="post" id="<?= @id() ?>" class="validator-inline -koowa-form">
	<div class="col width-50">
		<fieldset class="adminform ninja-form">
			<legend><?= @text('COM_NINJABOARD_DETAILS') ?></legend>
			<div class="element">	
				<label for="title" class="key"><?= @text('COM_NINJABOARD_TITLE') ?></label>
				<input type="text" name="title" id="title" class="inputbox required value" rel="<?= @text('COM_NINJABOARD_RANKS_REQUIRE_A_TITLE') ?>" size="50" value="<?= @escape($rank->title) ?>" maxlength="150" />
			</div>
			<div class="element">	
				<label for="min" class="key"><?= @text('COM_NINJABOARD_MIN_REQUIRED_POSTS') ?></label>
				<input type="text" name="min" id="min" class="inputbox required validate-integer value" size="50" value="<?= @escape($rank->min) ?>" maxlength="150" />
			</div>
			<div class="element">	
				<label class="key"><?= @text('COM_NINJABOARD_STATE') ?></label>
				<div class="value"><?= @ninja('select.statelist', array('attribs' => array('class' => 'validate-reqchk-byname label:\'state\''), 'selected' => @$rank->enabled)) ?></div>
			</div>
			<div class="element">	
				<label for="rank_file" class="key"><?= @text('COM_NINJABOARD_RANK_IMAGE_FILE') ?></label>
				<div class="value"><?= @ninja('select.images', array('path' => JPATH_ROOT.'/media/com_ninjaboard/images/rank', 'name' => 'rank_file', 'atrribs' => array('class' => 'value'), 'selected' => @$rank->rank_file, 'vertical' => true)) ?></div>
			</div>
			<div class="element">
				<?= $this->getService('ninja:element.note', array(
					'node' => simplexml_load_string('<param name="hint" type="note" class="note" slide="true" description="Go to the Joomla! media manager, and upload icons here: %s. Create the folders if they don\'t already exist." eval="return JPATH_ROOT.DS.\'images\'.DS.\'com_ninjaboard\'.DS.\'rank\';" show="Show how to upload custom icons." hide="Hide how to upload custom icons." />'),
					'value'	=> '',
					'field'	=> 'note'
				)) ?>
			</div>
	</div>
	<input type="hidden" name="description" value="<?= @escape($rank->description) ?>"/>
	<input type="hidden" name="params" value="<?= @escape($rank->params) ?>"/>
	<input type="hidden" name="rank_thumb" value="<?= @escape($rank->rank_thumb) ?>"/>
</form>