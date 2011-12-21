<? /** $Id: most_replied_topics.php 959 2010-09-21 14:33:17Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach(
	KFactory::tmp('admin::com.ninjaboard.model.topics')
				->sort(array('replies', 'hits'))
				->direction('desc')
				->limit($params->get('limit', 5))
				->getList()
  as $item) : ?>
	<? $titles[] = $item->subject ?>
	<? $legends[] = $item->replies ?>
	<? $values[] = $item->replies ?>
<? endforeach ?>

<script type="text/javascript">
	
	if(!charts) var charts = {};
	charts[<?= $module->id ?>] = function(){
		if(!$('chart<?= $module->id ?>').isVisible()) return;
	
		var chart = new URI('http://chart.apis.google.com/chart?cht=p3&chs=500x200&chdl=' + <?= json_encode(@$legends) ?>.join('|') + '&chl=' + <?= json_encode(@$titles) ?>.join('|') + '&chd=t:' + <?= json_encode(@$values) ?>.join(',')),
			img = $('chart<?= $module->id ?>'),
			parent = img.getParents()[1].get('tag') == 'dl' ? img.getParents()[2] : img.getParent(),
			x = Math.max(parent.getSize().x, 250);

		chart.setData({chs: x + 'x' + 200}, true);
		$('chart<?= $module->id ?>').set('src', chart);
	};
	window.addEvents({
		domready: charts[<?= $module->id ?>],
		resize: charts[<?= $module->id ?>],
		load: charts[<?= $module->id ?>]
	});
	window.addEvent('domready', function(){
		$('chart<?= $module->id ?>').addEvent('mouseenter', charts[<?= $module->id ?>]);
	});
</script>

<img id="chart<?= $module->id ?>" alt="" width="100%" height="200px" />