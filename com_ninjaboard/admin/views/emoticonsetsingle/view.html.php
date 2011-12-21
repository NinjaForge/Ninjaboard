<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewEmoticonSetSingle extends JView
{	
	function display($tpl = null)
	{
		// initialize variables
		$cid = JRequest::getVar('cid', array());

		if (!is_array($cid)) {
			$cid = array();
		}

		$ninjaboardEmoticonSet = new NinjaboardEmoticonSet($cid[0]);	
		$ninjaboardEmoticonSet->loadLanguage('admin');

	    //assign the rows to the view
		$this->assignRef('ninjaboardEmoticonSet', $ninjaboardEmoticonSet);
		
		parent::display($tpl);
	}
}
?>
