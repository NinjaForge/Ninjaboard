<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewEmoticonSetAll extends JView
{	
	function display($tpl = null)
	{
		// initialize variables
		$app			=& JFactory::getApplication();
		$db				=& JFactory::getDBO();
		$context		= 'com_ninjaboard.ninjaboard_emoticonset_view';
		$limit			= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart		= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// get default template
		$query = "SELECT d.emoticon_set"
				. "\n FROM #__ninjaboard_designs AS d"
				. "\n WHERE d.default_design = 1"
				;
		$db->setQuery($query);
		$defaultEmoticonSet = $db->loadResult();
		
		// list templates
		jimport('joomla.filesystem.folder');
		$emoticonSets	= JFolder::folders(NB_EMOTICONS);
				
		$rows = array();
		foreach ($emoticonSets as $emoticonSetFolder) {
			$fileList = JFolder::files(NB_EMOTICONS.DS.$emoticonSetFolder, '.xml');
			foreach ($fileList as $emoticonSetFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_EMOTICONS.DS.$emoticonSetFolder, $emoticonSetFile, 'emoticonset')){
					continue;
				} else {
					$data->default_emoticon_set = ($data->file_name == $defaultEmoticonSet);
					$rows[]  = $data;
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
