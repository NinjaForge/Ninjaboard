<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.grid.head') ?>
<script type="text/javascript" src="/raphael.js"></script>
<script type="text/javascript" src="/Mapper.js"></script>

<style type="text/css">
	.header.icon-48-joomlausergroupmaps {
		background-image: url(<?= @$img('/48/usergroups.png') ?>);
	}
</style>

<script type="text/javascript">
	
	window.addEvent("domready", function(){(function(){

		document.getElement('.map-wrapper').setStyle('height', Math.max(
			document.getElement('.map-points-from').getSize().y,
			document.getElement('.map-points-to').getSize().y
		));

		new Mapper({
			holder: 'holder',
			data: <?= json_encode($joomlausergroupmaps->getData()) ?>,
			points: {
				from: $('<?= @id('points-from') ?>').getChildren('.map'),
				to: $('<?= @id('points-to') ?>').getChildren()
			}
		});
		
		//Height adjust fix
		/*if(window.parent) {
			window.parent.document.getElement('#sbox-content iframe')
				.setStyle('overflow', 'hidden')
				.fireEvent('load');
		}*/
		
	}).delay(100)});
</script>
<form action="<?= @route() ?>" method="post" id="<?= @id() ?>">
	<? if(KRequest::get('get.tmpl', 'cmd') == 'component') : ?>
		<fieldset style="border-radius: 3px;" id="toolbar-joomlausergroupmaps">
			<div style="float: right">
				<button type="submit" name="save"><?= @text('Save') ?></button>
			</div>
			<div class="configuration"><?= @text(KInflector::humanize($this->getView()->getName())) ?></div>
			<input type="hidden" name="action" value="edit" />
			<input type="hidden" name="tmpl" value="component" />
		</fieldset>
	<? endif ?>
	<h3 style="text-align: center;"><?= @text("Drag the points to change the mapping.") ?></h3>
	<div style="width:100%;text-align:center;">
		<div class="map-wrapper" onselectstart="return false;" style="-moz-user-select: none;">
			<div class="map-points-from">
				<ul style="margin-right:25%;text-align:right;" id="<?= @id('points-from') ?>">
				    <? $i = 0; $fallback = current($usergroups->getData()) ?>

				    <? if (!JVersion::isCompatible('1.6.0')) : ?>
					    <li class="map" id="<?= @id('points-from-0') ?>">
					    	<h1><?= @text('Unregistered') ?></h1>
					    	<? $value = isset($maps[0]) ? $maps[0] : null ?>
					    	<input type="hidden" name="group[0]" id="group<?= (int) $i++ ?>" value="<?= $value ? $value : $fallback['id'] ?>" />
					    </li>
					<? endif ?>
					<? foreach(@$acltree as $acl) : ?>
						<? if(in_array($acl->id, array(29, 30))) continue ?>
						<li class="map" id="<?= @id('points-from-' . $acl->value) ?>">
							<h1><?= $acl->title ?></h1>
							<? $value = isset($maps[$acl->id]) ? (int) $maps[$acl->id] : 0 ?>
							<input type="hidden" name="group[<?= $acl->id ?>]" id="group<?= (int) $i++ ?>" value="<?= $value ? $value : $fallback['id'] ?>" />
						</li>
					<? endforeach ?>
				</ul>
			</div>
			
			<div id="holder"></div>
			<h1 class="map-direction">&#10151;</h1>
			<div class="map-points-to">
				<ul id="<?= @id('points-to') ?>" style="margin-left: 25%;text-align: left;">
					<? foreach($usergroups as $acl) : ?>
						<li class="map" id="<?= @id('points-to-' . $acl->id) ?>">
							<h1><?= $acl->title ?></h1>
						</li>
					<? endforeach ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="clr"></div>
	<h5 style="text-align: center;"><?= @text("Note: Super Administrators will always have full permissions and no access restrictions throughout Ninjaboard") ?></h5>
	<? if(KRequest::get('get.tmpl', 'cmd') == 'component') : ?>
		<input type="hidden" name="tmpl" value="component" />
	<? endif ?>
</form>