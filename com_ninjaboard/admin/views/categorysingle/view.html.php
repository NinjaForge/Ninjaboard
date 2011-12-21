<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewCategorySingle extends JView
{	
	function display($tpl = null)
	{
		// initialize variables
		
		$row =& JTable::getInstance('ninjaboardcategory', 'Table');
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$id = $cid[0];
		$row->load($id);
				
		$app	=& JFactory::getApplication();
		$user	=& JFactory::getUser();
		
		// is someone else editing this category?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&controller=category&task=display';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_CATEGORY'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out category so nobody else can edit it
		$row->checkout($user->get('id'));
		
		$this->assignRef('row', $row);
					
	//setup the yes/no list for our published status	
		$this->assignRef('published', JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published));
		
	//setup our tooltips - tooltip below is an example	
		/*$tooltipText = JText::_('The userid of a valid Joomla user on this site');
		$tooltipTitle = JText::_('Author Userid');
		$this->assignRef('author_id_tt',JHTML::tooltip($tooltipText, $tooltipTitle, 'tooltip.png', '', '', false));
		
		*/
		parent::display($tpl);
	}
}
?>
