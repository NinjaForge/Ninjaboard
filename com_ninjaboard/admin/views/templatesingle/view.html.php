<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewTemplateSingle extends JView
{	
	function display($tpl = null)
	{
		// initialize variables
		$db		=& JFactory::getDBO();
		$cid 	=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}	

		if(!$data = NinjaboardHelper::parseXMLFile(NB_TEMPLATES.DS.basename($cid[0], ".xml"), $cid[0], 'template')) {
			$link = 'index.php?option=com_ninjaboard&task=display&controller=template';
			$msg  = sprintf(JText::_('NB_MSGFILENOTFOUND'), $cid[0]);
			$app->redirect($link, $msg);
		} else {
			$row = $data;
		}
		
		$lists = array();
		
	// get default template
		$query = "SELECT d.template"
				. "\n FROM #__ninjaboard_designs AS d"
				. "\n WHERE default_design = 1"
				;
		$db->setQuery($query);
		$defaulttemplate = $db->loadResult();
		
	// build the html radio buttons for state
		$lists['defaulttemplate'] = JHTML::_('select.booleanlist', 'defaulttemplate', '', ($row->file_name == $defaulttemplate));			
			
	
	//assign the rows to the view
		$this->assignRef('lists', $lists);
		$this->assignRef('row', $row);
		
		parent::display($tpl);
	}
}
?>
