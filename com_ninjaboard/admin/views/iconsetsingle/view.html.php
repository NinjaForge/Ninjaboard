<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewIconSetSingle extends JView
{	
	function display($tpl = null)
	{
		
	// initialize variables
		$cid 	= JRequest::getVar('cid', '');

	//If someone selected more than 1 cid, then set it to the first one only
		if (is_array($cid)) {
			$cid = $cid[0];
		}

		$ninjaboardIconSet = new NinjaboardIconSet($cid);	
		$ninjaboardIconSet->loadLanguage('admin');

	//assign the rows to the view
		$this->assignRef('ninjaboardIconSet', $ninjaboardIconSet);
		
		parent::display($tpl);
	}
}
?>
