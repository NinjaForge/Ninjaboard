<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.helper');

// load the component language file
$language = &JFactory::getLanguage();
$language->load('com_ninjaboard');

?>

<h2>Ninjaboard Forum Extension Removal</h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('NB_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('NB_STATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'Ninjaboard Forum '.JText::_('NB_COMPONENT'); ?></td>
			<td><strong><?php echo JText::_('NB_REMOVED'); ?></strong></td>
		</tr>
	</tbody>
</table>
