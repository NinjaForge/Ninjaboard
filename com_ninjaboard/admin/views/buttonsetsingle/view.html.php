<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewButtonSetSingle extends JView
{	
	function display($tpl = null)
	{
		
		// initialize variables
		$cid = JRequest::getVar('cid', '');

		//If someone selected more than 1 cid, then set it to the first one only
		if (is_array($cid)) {
			$cid = $cid[0];
		}

		$ninjaboardButtonSet = new NinjaboardButtonSet($cid);	
		$ninjaboardButtonSet->loadLanguage('admin');

		//assign the rows to the view
		$this->assignRef('ninjaboardButtonSet', $ninjaboardButtonSet);

		parent::display($tpl);
	}
}
?>
