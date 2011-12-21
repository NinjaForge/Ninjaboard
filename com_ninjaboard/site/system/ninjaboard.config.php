<?php
/**
 * @version $Id: ninjaboard.config.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Configuration
 *
 * @package Ninjaboard
 */
class NinjaboardConfig
{
	/**
	 * config data
	 *
	 * @var array
	 */
	var $_config = null;

	/**
	 * board settings data
	 *
	 * @var array
	 */
	var $_board_settings = null;
	
	/**
	 * latest posts settings data
	 *
	 * @var array
	 */
	var $_latestpost_settings = null;
	
	/**
	 * latest posts settings data
	 *
	 * @var array
	 */
	var $_feed_settings = null;
			
	/**
	 * view settings data
	 *
	 * @var array
	 */
	var $_view_settings = null;
	
	/**
	 * view footer settings data
	 *
	 * @var array
	 */
	var $_view_footer_settings = null;
			
	/**
	 * user settings defaults data
	 *
	 * @var array
	 */
	var $_user_settings_defaults = null;
	
	/**
	 * attachment settings
	 *
	 * @var array
	 */
	var $_attachment_settings = null;
		
	/**
	 * avatar settings
	 *
	 * @var array
	 */
	var $_avatar_settings = null;
	
	/**
	 * captcha settings
	 *
	 * @var array
	 */
	var $_captcha_settings = null;
					
	function NinjaboardConfig() {
	
		// initialize variables
		$db			=& JFactory::getDBO();
			
		$query = "SELECT c.name AS config_name, c.editor AS config_editor, c.board_settings, c.latestpost_settings,"
				. "\n c.view_settings, c.view_footer_settings, c.user_settings_defaults, c.attachment_settings,"
				. "\n c.feed_settings, c.avatar_settings, c.captcha_settings, c.topic_icon_function, c.post_icon_function,"
				. "\n d.name AS design_name, d.template AS template_file, d.style AS style_file,"
				. "\n d.emoticon_set AS emoticon_set_file, d.icon_set AS icon_set_file, d.button_set AS button_set_file,"
				. "\n tz.name AS time_zone_name, tz.offset AS time_zone_offset,"
				. "\n tf.name AS time_format_name, tf.timeformat AS time_format"
				. "\n FROM #__ninjaboard_configs AS c"
				. "\n INNER JOIN #__ninjaboard_designs AS d ON d.id = c.id_design"
				. "\n INNER JOIN #__ninjaboard_timezones AS tz ON tz.id = c.id_timezone"
				. "\n INNER JOIN #__ninjaboard_timeformats AS tf ON tf.id = c.id_timeformat"
				. "\n WHERE c.default_config = 1"
				. "\n AND d.default_design = 1"
				;		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$this->_config = $rows[0];
		$this->_board_settings = new JParameter($this->_config->board_settings);
		$this->_latestpost_settings = new JParameter($this->_config->latestpost_settings);
		$this->_feed_settings = new JParameter($this->_config->feed_settings);
		$this->_view_settings = new JParameter($this->_config->view_settings);
		$this->_view_footer_settings = new JParameter($this->_config->view_footer_settings);
		$this->_user_settings_defaults = new JParameter($this->_config->user_settings_defaults);
		$this->_attachment_settings = new JParameter($this->_config->attachment_settings);
		$this->_avatar_settings = new JParameter($this->_config->avatar_settings);
		$this->_captcha_settings = new JParameter($this->_config->captcha_settings);
	}
	
	/**
	 * get instance
	 *
	 * @access 	public
	 * @return object
	 */
	function &getInstance() {
	
		static $ninjaboardConfig;

		if (!is_object($ninjaboardConfig)) {
			$ninjaboardConfig = new NinjaboardConfig();
		}

		return $ninjaboardConfig;
	}

	/**
	 * get config name
	 * 
	 * @access public
	 * @return string
	 */
	function getConfigName() {
		return $this->_config->config_name;
	}
		
	/**
	 * get design name
	 * 
	 * @access public
	 * @return string
	 */
	function getDesignName() {
		return $this->_config->design_name;
	}
	
	/**
	 * get template file
	 * 
	 * @access public
	 * @return string
	 */
	function getTemplateFile() {
		return $this->_config->template_file;
	}
		
	/**
	 * get layout name
	 * 
	 * @access public
	 * @return string
	 */
	function getLayoutName() {
		return basename($this->_config->template_file, ".xml");
	}
	
	/**
	 * get style file
	 * 
	 * @access public
	 * @return string
	 */
	function getStyleFile() {
		return $this->_config->style_file;
	}

	/**
	 * get emoticon set image source
	 * 
	 * @access public
	 * @return string
	 */
	function getEmoticonSetFile() {
		return $this->_config->emoticon_set_file;
	}
	
	/**
	 * get button set image source
	 * 
	 * @access public
	 * @return string
	 */
	function getButtonSetFile() {
		return $this->_config->button_set_file;
	}
		
	/**
	 * get icon set image source
	 * 
	 * @access public
	 * @return string
	 */
	function getIconSetFile() {
		return $this->_config->icon_set_file;
	}
		
	/**
	 * get default topic icon
	 * 
	 * @access public
	 * @return string
	 */
	function getDefaultTopicIcon() {
		return $this->_config->topic_icon_function;
	}
		
	/**
	 * get default post icon
	 * 
	 * @access public
	 * @return string
	 */
	function getDefaultPostIcon() {
		return $this->_config->post_icon_function;
	}

	/**
	 * get time zone name
	 * 
	 * @access public
	 * @return string
	 */
	function getTimeZoneName() {
		return $this->_config->time_zone_name;
	}
				
	/**
	 * get time zone offset
	 * 
	 * @access public
	 * @return string
	 */
	function getTimeZoneOffset() {
		return $this->_config->time_zone_offset;
	}
		
	/**
	 * get time format
	 * 
	 * @access public
	 * @return string
	 */
	function getTimeFormat() {
		return $this->_config->time_format;
	}
	
	/**
	 * get editor
	 * 
	 * @access public
	 * @return string
	 */
	/* TODO - delete this
	  
	 function getEditor() {
		return $this->_config->config_editor;
	}*/
	
	/**
	 * get board settings
	 * 
	 * @access public
	 * @return string
	 */
	function getBoardSettings($value) {
		return $this->_board_settings->get($value);
	}
	
	/**
	 * get latest posts settings
	 * 
	 * @access public
	 * @return string
	 */
	function getLatestPostSettings($value) {
		return $this->_latestpost_settings->get($value);
	}
	
	/**
	 * get feed settings
	 * 
	 * @access public
	 * @return string
	 */
	function getFeedSettings($value) {
		return $this->_feed_settings->get($value);
	}
		
	/**
	 * get view settings
	 * 
	 * @access public
	 * @return string
	 */
	function getViewSettings($value) {
		return $this->_view_settings->get($value);
	}
	
	/**
	 * get view footer settings
	 * 
	 * @access public
	 * @return string
	 */
	function getViewFooterSettings($value) {
		return $this->_view_footer_settings->get($value);
	}
			
	/**
	 * get user settings defaults
	 * 
	 * @access public
	 * @return string
	 */
	function getUserSettingsDefaults($value) {
		return $this->_user_settings_defaults->get($value);
	}

	/**
	 * get attachment settings
	 * 
	 * @access public
	 * @return string
	 */
	function getAttachmentSettings($value) {
		return $this->_attachment_settings->get($value);
	}
			
	/**
	 * get avatar settings
	 * 
	 * @access public
	 * @return string
	 */
	function getAvatarSettings($value) {
		return $this->_avatar_settings->get($value);
	}
		
	/**
	 * get captcha settings
	 * 
	 * @access public
	 * @return string
	 */
	function getCaptchaSettings($value) {
		return $this->_captcha_settings->get($value);
	}
								
}
?>
