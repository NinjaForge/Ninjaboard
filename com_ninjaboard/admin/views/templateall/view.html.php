<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewTemplateAll extends JView
{	
	function display($tpl = null)
	{
		// initialize variables
		$app			=& JFactory::getApplication();
		$db				=& JFactory::getDBO();
		$context		= 'com_ninjaboard.ninjaboard_template_view';
		$limit			= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart		= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// get default template
		$query = "SELECT d.template"
				. "\n FROM #__ninjaboard_designs AS d"
				. "\n WHERE default_design = 1"
				;
		$db->setQuery($query);
		$defaulttemplate = $db->loadResult();
		
		// list templates
		jimport('joomla.filesystem.folder');
		$templatesList 	= JFolder::folders(NB_TEMPLATES);
				
		$rows = array();
		foreach ($templatesList as $templateFolder) {
			$fileList = JFolder::files(NB_TEMPLATES.DS.$templateFolder, '.xml');
			foreach ($fileList as $templateFile) {
				if(!$data = NinjaboardHelper::parseXMLFile(NB_TEMPLATES.DS.$templateFolder, $templateFile, 'template')){
					continue;
				} else {
					$data->default_template	= ($data->file_name == $defaulttemplate);
					$rows[]					= $data;
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
