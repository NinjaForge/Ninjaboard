<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewButtonSetAll extends JView
{	
	function display($tpl = null)
	{
		// initialize variables
		$app			=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$context		= 'com_ninjaboard.ninjaboard_buttonset_view';
		$limit			= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart		= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// get default button set
		$query = "SELECT d.button_set"
				. "\n FROM #__ninjaboard_designs AS d"
				. "\n WHERE d.default_design = 1"
				;
		$db->setQuery($query);
		$defaultButtonSet = $db->loadResult();
		
		// list templates
		jimport('joomla.filesystem.folder');
		$buttonSets = JFolder::folders(NB_BUTTONS);
				
		$rows = array();
		foreach ($buttonSets as $buttonSetFolder) {
			$fileList = JFolder::files(NB_BUTTONS.DS.$buttonSetFolder, '.xml');
			foreach ($fileList as $buttonSetFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_BUTTONS.DS.$buttonSetFolder, $buttonSetFile, 'buttonset')){
					continue;
				} else {
					$data->default_button_set = ($data->file_name == $defaultButtonSet);
					$rows[]  = $data;
				}
			}
		}
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination(count($rows), $limitstart, $limit);
		$rows = array_slice($rows, $pagination->limitstart, $pagination->limit);
		
	//ViewButtonSet::showButtonSets($rows, $pagination);	
	

	//assign the rows to the view
		$this->assignRef('rows', $rows);
		$this->assignRef('pagination', $pagination);
		
		parent::display($tpl);
	}
}
?>
