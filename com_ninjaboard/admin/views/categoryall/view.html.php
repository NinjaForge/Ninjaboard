<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewCategoryAll extends JView
{	
	function display($tpl = null)
	{
		$rows =& $this->get('data');
		$pagination =& $this->get('pagination');
		$search = $this->get('search');
		$lists = $this->get('orderbylists');
		
		$this->assignRef('rows', $rows);
		$this->assignRef('pagination', $pagination);
		$this->assign('search', $search);		
		$this->assignRef('lists', $lists);
		
		parent::display($tpl);
	}
}
?>
