<? /** $Id: settings.php 1214 2010-12-13 02:38:37Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach($theme->xml->form->children() as $fieldset) : ?>
			
	<? if(count($fieldset->children()) < 1 || isset($fieldset['hide'])) continue ?>
	
	<?= KFactory::tmp('admin::com.ninja.form.parameter', array(
			'data'     => $theme->params,
	  		'xml'  	   => $theme->xml->form,
	  		'render'   => 'fieldset',
	  		'group'	   => (string)$fieldset['group'],
	  		//'groups'   => false,
	  		'grouptag' => 'fieldset',
	  		'name'	   => 'params'
	  ))->render() ?>

<? endforeach ?>