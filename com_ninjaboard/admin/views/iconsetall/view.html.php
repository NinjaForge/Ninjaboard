<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewIconSetAll extends JView
{	
	function display($tpl = null)
	{
		// initialize variables
		$app	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		
		$context		= 'com_ninjaboard.ninjaboard_iconset_view';
		$limit			= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart		= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// get default button set
		$query = "SELECT d.icon_set"
				. "\n FROM #__ninjaboard_designs AS d"
				. "\n WHERE d.default_design = 1"
				;
		$db->setQuery($query);
		$defaultIconSet = $db->loadResult();
		
		// list templates
		jimport('joomla.filesystem.folder');
		$iconSets = JFolder::folders(NB_ICONS);
				
		$rows = array();
		foreach($iconSets as $iconSetFolder) {
			$fileList = JFolder::files(NB_ICONS.DS.$iconSetFolder, '.xml');
			foreach($fileList as $iconSetFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_ICONS.DS.$iconSetFolder, $iconSetFile, 'iconset')){
					continue;
				} else {
					$data->default_icon_set =($data->file_name == $defaultIconSet);
					$rows[] = $data;
				}
			}
		}
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination(count($rows), $limitstart, $limit);
		$rows = array_slice($rows, $pagination->limitstart, $pagination->limit);


	//assign the rows to the view
		$this->assignRef('rows', $rows);
		$this->assignRef('pagination', $pagination);
		
		parent::display($tpl);
	}
}
?>
