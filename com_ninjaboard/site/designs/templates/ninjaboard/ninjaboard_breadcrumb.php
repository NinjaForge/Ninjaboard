<?php defined('_JEXEC') or die('Restricted access'); ?>

<div id="nbBreadCrumbs">
	<?php
		# ToDo: Add category to bradcrumbs object !!!
		$crumbs = $this->breadCrumbs->getBreadCrumbs();
		$count  = count($crumbs);
		if ($count > 1) {
			for ($i = 0; $i < $count - 1; $i++) {
				echo
					'<a href="', $crumbs[$i]->href, '">', $crumbs[$i]->name, '</a>',
					'&nbsp;', JText::_('NB_GT'),'&nbsp;';
			}
		}
		if (! empty($crumbs[$count-1]->href))
			echo '<a href="', $crumbs[$count-1]->href, '">', $crumbs[$count-1]->name, '</a>';
		else
			echo $crumbs[$count-1]->name;
	?>
</div>
