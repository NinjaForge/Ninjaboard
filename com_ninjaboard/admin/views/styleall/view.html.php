<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewStyleAll extends JView
{	
	function display($tpl = null)
	{
		// initialize variables
		$app	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		
		$context		= 'com_ninjaboard.ninjaboard_template_view';
		$limit			= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart		= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// get default style
		$query = "SELECT d.style"
				. "\n FROM #__ninjaboard_designs AS d"
				. "\n WHERE default_design = 1"
				;
		$db->setQuery($query);
		foreach ($db->loadObject() as $defaultstyle);
		
		// list styles
		jimport('joomla.filesystem.folder');
		$styleList = JFolder::folders(NB_STYLES);
				
		$rows = array();
		foreach ($styleList as $styleFolder) {
			$fileList = JFolder::files(NB_STYLES.DS.$styleFolder, '.xml');
			foreach ($fileList as $styleFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_STYLES.DS.$styleFolder, $styleFile, 'style')) {
					continue;
				} else {
					$data->default_style = ($data->file_name == $defaultstyle);
					$data->folder_name = $styleFolder;
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
