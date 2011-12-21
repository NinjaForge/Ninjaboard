<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class NinjaBoardViewDashboard extends JView
{	
	function display($tpl = null)
	{
		
		//create an array of button information for the dash page
		//the 3 fields are the joomla link details button, the button icon name and the text to go under it
		$butInfo = array(	array('&task=ninjaboard_forum_view','header/icon-48-forum.png',JText::_('NB_BOARD')),
					 		array('&task=ninjaboard_config_view','header/icon-48-ninjaboardtools.png',JText::_('NB_CONFIG')),
					 		array('&task=ninjaboard_timezone_view','header/icon-48-timezone.png',JText::_('NB_TIMEZONES')),
					 		array('&task=ninjaboard_timeformat_view','header/icon-48-timeformat.png',JText::_('NB_TIMEFORMATS')),
					 		array('&task=ninjaboard_design_view','header/icon-48-style.png',JText::_('NB_DESIGNS')),
					 		array('&task=ninjaboard_user_view','header/icon-48-profilefield.png',JText::_('NB_USERS')),
					 		array('&task=ninjaboard_group_view','header/icon-48-group.png',JText::_('NB_GROUPS')),
					 		array('&task=ninjaboard_profilefield_view','header/icon-48-ninjaboarduser.png',JText::_('NB_PROFILEFIELDS')),
					 		array('&task=ninjaboard_terms_view','header/icon-48-terms.png',JText::_('NB_TERMS')),
					 		array('&task=ninjaboard_usersync_view','header/icon-48-usersync.png',JText::_('NB_TOOLS'))
					);
					
		
		$this->assignRef('butInfo', $butInfo);
		
		
		//Taken from Ninjaboard.php controller file
		
		// initialize variables
		$db		=& JFactory::getDBO();
		
		// get number of topics
		$query = "SELECT '". JText::_('NB_BOARDTOPICS') ."' AS description, SUM(topics) AS value" .
				 "\n FROM #__ninjaboard_forums"
				 ;
		$db->setQuery($query);	
		$rows = $db->loadObjectList();

		if (!is_array($rows)) {
			$object = new stdClass(); $object->description = JText::_('NB_BOARDTOPICS'); $object->value = JText::_('NB_NOTAVAILABLE');
			$rows[] = $object;
		}
						
		// get number of posts
		$query = "SELECT '". JText::_('NB_BOARDPOSTS') ."' AS description, SUM(posts) AS value" .
				 "\n FROM #__ninjaboard_forums"
				 ;
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!is_array($result)) {
			$object = new stdClass(); $object->description = JText::_('NB_BOARDPOSTS'); $object->value = JText::_('NB_NOTAVAILABLE');
			$result[] = $object;
		}
				
		$rows = array_merge($rows, $result);

		// get number of users
		$query = "SELECT  '". JText::_('NB_BOARDUSERS') ."' AS description , COUNT(*) AS value" .
				 "\n FROM #__users"
				 ;
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!is_array($result)) {
			$object = new stdClass(); $object->description = JText::_('NB_BOARDUSERS'); $object->value = JText::_( 'NB_NOTAVAILABLE' );
			$result[] = $object;
		}
					
		$rows = array_merge($rows, $result);
		
		// get forum size
		$query = "SHOW TABLE STATUS LIKE '%ninjaboard%'"
				 ;
		$db->setQuery($query);
		$tablerows = $db->loadObjectList();
		
		// get board size
		$size = 0;
		foreach($tablerows as $tablerow) {
			$size += $tablerow->Data_length;
		}
		
        if ( $size < 964 ) { 
			$size = round($size) ." Bytes"; 
		} else if ( $size < 1000000 ) { 
			$size = round( $size/1024,2 ) ." KB" ; 
		} else { 
			$size = round( $size/1048576,2 ) ." MB"; 
		}
		
		$object = new stdClass(); $object->description = JText::_('NB_BOARDSIZE'); $object->value = $size;
		$rows[] = $object;
					
	//Removed by Dan - will use the dashboard view instead.
		//ViewNinjaboard::showControlPanel($rows);
	

	//assign the rows to the view
		$this->assignRef('rows', $rows);
		
		parent::display($tpl);
	}
}
?>
