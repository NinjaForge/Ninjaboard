<?php
/**
 * @version $Id: config.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'views'.DS.'config.php');

/**
 * Ninjaboard Config Controller
 *
 * @package Ninjaboard
 */
class ControllerConfig extends JController
{

	/**
	 * compiles a list of configurations
	 */
	function showConfigs() {

		// initialize variables
		$app =& JFactory::getApplication();
		$db	 =& JFactory::getDBO();
		
		$context			= 'com_ninjaboard.ninjaboard_config_view';
		$filter_order		= $app->getUserStateFromRequest("$context.filter_order", 'filter_order', 'c.name');
		$filter_order_Dir	= $app->getUserStateFromRequest("$context.filter_order_Dir",	'filter_order_Dir',	'');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		$orderby = "\n ORDER BY $filter_order $filter_order_Dir";
		
		// get the total number of records
		$query = "SELECT COUNT(*)"
				. "\n FROM #__ninjaboard_configs AS c"
				. $orderby
				;		
		$db->setQuery($query);
		$total = $db->loadResult();
		
		// create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
												
		$query = "SELECT c.*, d.name AS design_name, t.name AS timezone_name, f.name AS timeformat_name"
				. "\n FROM #__ninjaboard_configs AS c"
				. "\n LEFT JOIN #__ninjaboard_designs AS d ON d.id = c.id_design"
				. "\n LEFT JOIN #__ninjaboard_timezones AS t ON t.id = c.id_timezone"
				. "\n LEFT JOIN #__ninjaboard_timeformats AS f ON f.id = c.id_timeformat"
				. $orderby
				;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		
		// parameter list
		$lists = array();
		
		// table ordering
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir']	= $filter_order_Dir;
		
		ViewConfig::showConfigs($rows, $pagination, $lists);	
	}
	
	/**
	 * cancels edit configuration operation
	 */
	function cancelEditConfig() {

		$app =& JFactory::getApplication();
		
		// check in configuration so other can edit it
		$row =& JTable::getInstance('NinjaboardConfig');
		$row->bind(JRequest::get('post'));
		$row->checkin();
		
		$link = 'index.php?option=com_ninjaboard&task=ninjaboard_config_view';
		$app->redirect( $link );
	}
	
	/**
	 * edit the configuration
	 */
	function editConfig() {
		
		// initialize variables
		$app				=& JFactory::getApplication();
		$db					=& JFactory::getDBO();
		$user				=& JFactory::getUser();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$cid				=  JRequest::getVar('cid', array(0));
		
		if (!is_array($cid)) $cid = array(0);

		$row =& JTable::getInstance('NinjaboardConfig');
		$row->load($cid[0]);

		// is someone else editing this configuration?
		if (JTable::isCheckedOut($user->get('id'), $row->checked_out)) {
			$link = 'index.php?option=com_ninjaboard&task=ninjaboard_config_view';
			$editingUser =& JFactory::getUser($row->checked_out);
			$msg = JText::sprintf('NB_MSGBEINGEDITTED', JText::_('NB_CONFIG'), $row->name, $editingUser->name);
			$app->redirect($link, $msg);
		}
		
		// check out configuration so nobody else can edit it
		$row->checkout($user->get('id'));
		
		// parameter list
		$lists = array();
	
		// build the html radio buttons for default config
		$lists['defaultconfig'] = JHTML::_('select.booleanlist', 'default_config', '', $row->default_config);

		// list designs		
		$query = "SELECT d.*"
				. "\n FROM #__ninjaboard_designs AS d"
				. "\n ORDER BY d.name"
				;
		$db->setQuery( $query );
		$designrows = $db->loadObjectList();
		$lists['designs'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'id_design', 'class="inputbox" size="1"', 'id', 'name', intval($row->id_design));

		// list time zones		
		$query = "SELECT z.*"
				. "\n FROM #__ninjaboard_timezones AS z"
				. "\n ORDER BY z.ordering"
				;
		$db->setQuery( $query );
		$lists['timezones'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'id_timezone', 'class="inputbox" size="1"', 'id', 'name', intval($row->id_timezone));

		// list time formats		
		// TODO - This is commetned out to put in a new method for generating this list, that is easier to read for admins. Remove once testign is complete
		/*  
		$query = "SELECT f.*"
				. "\n FROM #__ninjaboard_timeformats AS f"
				. "\n ORDER BY f.name"
				;
		$db->setQuery( $query );
		$lists['timeformats'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'id_timeformat', 'class="inputbox" size="1"', 'id', 'name', intval($row->id_timeformat));
		*/
		// list time formats		
		$query = "SELECT f.*"
				. "\n FROM #__ninjaboard_timeformats AS f"
				. "\n ORDER BY f.name"
				;
		$db->setQuery($query);
		$timeformatslist = $db->loadObjectList();
		
		$timeformats = array();
		foreach ($timeformatslist as $timeformat) {
			$timeformat->name = NinjaboardHelper::formatDate(time(), $timeformat->timeformat, $ninjaboardConfig->getTimeZoneOffset());
			$timeformats[] = JHTML::_('select.option', $timeformat->timeformat, $timeformat->name, 'id', 'name');
		}
		$lists['timeformats'] = JHTML::_('select.genericlist',  $timeformats, 'time_format', 'class="inputbox" size="1"', 'id', 'name', intval($row->id_timeformat));
		
		
		
		// list BB editors
		$query = "SELECT element, name"
				. "\n FROM #__plugins"
				. "\n WHERE folder = 'editors'"
				. "\n AND published = 1"
				. "\n ORDER BY ordering, name"
				;
		$db->setQuery( $query );
		$lists['editors'] = JHTML::_('select.genericlist',  $db->loadObjectList(), 'editor', 'class="inputbox" size="1"', 'element', 'name', $row->editor);

		// list icons
		$ninjaboardIconSet = new NinjaboardIconSet($ninjaboardConfig->getIconSetFile());
		$postIconRows = $ninjaboardIconSet->getIconsByGroup('iconPost');
		$lists['topicicons'] = JHTML::_('select.genericlist',  $postIconRows, 'topic_icon_function', 'class="inputbox" size="1"', 'function', 'title', $row->topic_icon_function);
		$lists['posticons'] = JHTML::_('select.genericlist',  $postIconRows, 'post_icon_function', 'class="inputbox" size="1"', 'function', 'title', $row->post_icon_function);

		// board settings params definitions
		$file = NB_ADMINPARAMS.DS.'config_board_settings.xml';
		$lists['board_settings'] = new JParameter($row->board_settings, $file);

		// latest post settings params definitions
		$file = NB_ADMINPARAMS.DS.'config_latestpost_settings.xml';
		$lists['latestpost_settings'] = new JParameter($row->latestpost_settings, $file);
		
		// feed settings params definitions
		$file = NB_ADMINPARAMS.DS.'config_feed_settings.xml';
		$lists['feed_settings'] = new JParameter($row->feed_settings, $file);
		
		// attachment settings params definitions
		$file = NB_ADMINPARAMS.DS.'config_attachment_settings.xml';
		$lists['attachment_settings'] = new JParameter($row->attachment_settings, $file);
		
		// view settings params definitions
		$file = NB_ADMINPARAMS.DS.'config_view_settings.xml';
		$lists['view_settings'] = new JParameter($row->view_settings, $file);
		
		// view footer settings params definitions
		$file = NB_ADMINPARAMS.DS.'config_view_footer_settings.xml';
		$lists['view_footer_settings'] = new JParameter($row->view_footer_settings, $file);
		
		// user setting default params definitions
		$file = NB_ADMINPARAMS.DS.'config_user_settings_defaults.xml';
		$lists['user_settings_defaults'] = new JParameter($row->user_settings_defaults, $file);
		
		// avatar settings params definitions
		$file = NB_ADMINPARAMS.DS.'config_avatar_settings.xml';
		$lists['avatar_settings'] = new JParameter($row->avatar_settings, $file);
		
		// captcha settings params definitions
		$file = NB_ADMINPARAMS.DS.'config_captcha_settings.xml';
		$lists['captcha_settings'] = new JParameter($row->captcha_settings, $file);
				
		ViewConfig::editConfig($row, $lists);			
	}
	
	/**
	 * save the configuration
	 */	
	function saveConfig( $task ) {

		// Spoofing protection.
		JRequest::checkToken() or jexit('Invalid Token');

		// initialize variables
		$app	=& JFactory::getApplication();
		$db 	=& JFactory::getDBO();
		$row 	=& JTable::getInstance('NinjaboardConfig');
		$post	=  JRequest::get('post');

		if (!$row->bind($post)) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if (!$row->default_config) {
			// We need at least ONE default configuration set!
			$db->setQuery(
				'SELECT `default_config` FROM #__ninjaboard_configs'
			);
			if (!$records = $db->LoadResultArray()) JError::raiseError(1001, $db->getErrorMsg());
			else {
				if (count($records) == 1) {
					echo "<script> alert('".JText::_('ERROR_DEFAULT_CONFIG')."'); window.history.go(-1); </script>\n";
					exit();
				}
			}
		}
		else {
			// set all other configs to false
			$db->setQuery(
				'UPDATE #__ninjaboard_configs'
				. ' SET default_config = 0'
				. ' WHERE id <> '.(int) $row->id
			);
			if (!$db->query()) {
				JError::raiseError(1002, $db->getErrorMsg());
			}
		}		
		// save params
		$board_settings = JRequest::getVar('board_settings', array(), 'post', 'array');
		if (is_array($board_settings)) {
			$txt = array();
			foreach ($board_settings as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->board_settings = implode("\n", $txt);
		}

		// save params
		$latestpost_settings = JRequest::getVar('latestpost_settings', array(), 'post', 'array');
		if (is_array($latestpost_settings)) {
			$txt = array();
			foreach ($latestpost_settings as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->latestpost_settings = implode("\n", $txt);
		}
		
		// save params
		$feed_settings = JRequest::getVar('feed_settings', array(), 'post', 'array');
		if (is_array($feed_settings)) {
			$txt = array();
			foreach ($feed_settings as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->feed_settings = implode("\n", $txt);
		}
		
		// save params
		$view_settings = JRequest::getVar('view_settings', array(), 'post', 'array');
		if (is_array($view_settings)) {
			$txt = array();
			foreach ($view_settings as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->view_settings = implode("\n", $txt);
		}
		
		// save params
		$view_footer_settings = JRequest::getVar('view_footer_settings', array(), 'post', 'array');
		if (is_array($view_footer_settings)) {
			$txt = array();
			foreach ($view_footer_settings as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->view_footer_settings = implode("\n", $txt);
		}
		
		// save params
		$user_settings_defaults = JRequest::getVar('user_settings_defaults', array(), 'post', 'array');
		if (is_array($user_settings_defaults)) {
			$txt = array();
			foreach ($user_settings_defaults as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->user_settings_defaults = implode("\n", $txt);
		}
		
		// save params
		$attachment_settings = JRequest::getVar('attachment_settings', array(), 'post', 'array');
		if (is_array($attachment_settings)) {
			$txt = array();
			foreach ($attachment_settings as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->attachment_settings = implode("\n", $txt);
		}
		
		// save params
		$avatar_settings = JRequest::getVar('avatar_settings', array(), 'post', 'array');
		if (is_array($avatar_settings)) {
			$txt = array();
			foreach ($avatar_settings as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->avatar_settings = implode("\n", $txt);
		}
		
		// save params
		$captcha_settings = JRequest::getVar('captcha_settings', array(), 'post', 'array');
		if (is_array($captcha_settings)) {
			$txt = array();
			foreach ($captcha_settings as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->captcha_settings = implode("\n", $txt);
		}

		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$row->checkin();

		switch ($task) {
			case 'ninjaboard_config_apply':
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_config_edit&cid[]='. $row->id .'&hidemainmenu=1';
				break;
			case 'ninjaboard_config_save':
			default:
				$link = 'index.php?option=com_ninjaboard&task=ninjaboard_config_view';
				break;
		}
		
		$msg = JText::sprintf('NB_MSGSUCCESSFULLYSAVED', JText::_('NB_CONFIG'), $row->name);
		$app->redirect($link, $msg);
	}
	
	/**
	 * delete the configuration
	 */	
	function deleteConfig() {

		// Spoofing protection.
		JRequest::checkToken() or jexit('Invalid Token');

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);

		if (count($cid)) {
		
			// how many categories are there to delete?
			if (count($cid) == 1) {
				$row =& JTable::getInstance('NinjaboardConfig'); $row->load($cid[0]);
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_CONFIG'), $row->name);
			} else {
				$msg = JText::sprintf('NB_MSGSUCCESSFULLYDELETED', JText::_('NB_CONFIGS'), '');
			}

			$query = "DELETE FROM #__ninjaboard_configs"
					. "\n WHERE id = " . implode(' OR id = ', $cid)
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			}
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_CONFIG'), JText::_('NB_DELETE'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_config_view', $msg, $msgType);
	}
	
	/**
	 * set configuration as default
	 */	
	function defaultConfig() {

		// initialize variables
		$app		=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$cid		=  JRequest::getVar('cid', array(), 'post', 'array');
		$msgType	=  '';
		
		JArrayHelper::toInteger($cid);

		if (count($cid)) {
			
			// set default config
			NinjaboardHelper::setDefaultConfig($cid[0]);
			
			// we have to update all depended data
			$query = "SELECT c.id, c.id_design, d.id_smiley_set, d.id_button_set, d.id_icon_set"
					. "\n FROM #__ninjaboard_configs AS c"
					. "\n INNER JOIN #__ninjaboard_designs AS d ON d.id = c.id_design"
					. "\n WHERE c.default_config = 1"
					;
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			
			// set the default design for this config
			NinjaboardHelper::setDefaultDesign($rows[0]->id_design);
			
			// set the default smiley-set for the design
			NinjaboardHelper::setDefaultSmileySet($rows[0]->id_smiley_set);
			
			// set the default button-set for the design
			NinjaboardHelper::setDefaultButtonSet($rows[0]->id_button_set);
			
			// set the default icon-set for the design
			NinjaboardHelper::setDefaultIconSet($rows[0]->id_icon_set);		
		} else {
			$msg = JText::sprintf('NB_MSGNOSELECTION', JText::_('NB_CONFIG'), JText::_('NB_DEFAULT'));
		}

		$app->redirect('index.php?option=com_ninjaboard&task=ninjaboard_config_view', $msg, $msgType);
	}
}
?>
