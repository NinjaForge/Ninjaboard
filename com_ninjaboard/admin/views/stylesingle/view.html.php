<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewStyleSingle extends JView
{	
	function display($tpl = null)
	{
		// initialize variables
		$app	=& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$cid 	= JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) {
			$cid = array(0);
		}	

		if(!$data = NinjaboardHelper::parseXMLFile(NB_STYLES.DS.basename($cid[0], ".xml"), $cid[0], 'style')) {
			$link = 'index.php?option=com_ninjaboard&task=display&controller=style';
			$msg  = sprintf(JText::_('NB_MSGFILENOTFOUND'), $cid[0]);
			$app->redirect($link, $msg);
		} else {
			$row = $data;
		}
		
		$lists = array();
		
		// get default style
		$query = "SELECT d.style"
				. "\n FROM #__ninjaboard_designs AS d"
				. "\n WHERE default_design = 1"
				;
		$db->setQuery($query);
		$defaultstyle = $db->loadResult();
		
		// build the html radio buttons for state
		$lists['defaultstyle'] = JHTML::_('select.booleanlist', 'defaultstyle', '', ($row->file_name == $defaultstyle));			
				
	

	//assign the rows to the view
		$this->assignRef('row', $row);
		$this->assignRef('lists', $lists);
		
		parent::display($tpl);
	}
}
?>
