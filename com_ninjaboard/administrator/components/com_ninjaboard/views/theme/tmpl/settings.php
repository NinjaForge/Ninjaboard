<? /** $Id: settings.php 2508 2011-11-22 11:31:30Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach($theme->xml->form->children() as $fieldset) : ?>
			
	<? if(count($fieldset->children()) < 1 || isset($fieldset['hide'])) continue ?>

	<?= @service('ninja:form.parameter', array(
			'data'     => $theme->params,
	  		'xml'  	   => $theme->xml->form,
	  		'render'   => 'fieldset',
	  		'group'	   => (string)$fieldset['group'],
	  		//'groups'   => false,
	  		'grouptag' => 'fieldset',
	  		'name'	   => 'params'
	  ))->render() ?>

<? endforeach ?>