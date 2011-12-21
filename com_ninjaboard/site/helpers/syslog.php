<?php defined('_JEXEC') or die('Restricted access');
/**
 * @version $Id: syslog.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 * ##########################################################################
 * vim: tabstop=4
 */

jimport('joomla.error.log');

/**
 * Ninjaboard Syslog Helper
 *
 * @package Ninjaboard
 */
class NSyslogHelper
{
	# Public error flag.
	public $error = null;

	# Internal JLog reference.
	private $_log = null;

	# Caller classname::function.
	private $_method = null;


	# NSyslogHelper class contructor.
	private function NSyslogHelper($logfile, $method, $format)
	{
		$this->_log		=& JLog::getInstance($logfile, array('format' => $format));
		$this->_method	=  $method;
		$this->_error	=  false;
	}

	/**
	 * Returns the NSyslogHelper object.
	 *
	 * @access	public
	 * @param	string		Logfile name without file extension
	 * @param	string		Name of the calling method (class::function)
	 * @param	string		JLog options (logfile entry format)
	 */
	public function & getInstance($logfile = 'ninjaforge.syslog', $method = null, $format = null)
	{
		static $syslog;

		# Log entry format.
		$format = $format ? $format : "{DATE} {TIME} {LEVEL}\t{C-IP}\t{METHOD}({LINE})\t{MESSAGE}";

		if (! is_object($syslog))
			$syslog = new NSyslogHelper($logfile.'.php', $method, $format);

		return $syslog;
	}

	/**
	 * Writes syslog entries to the logfile.
	 *
	 * @access	public
	 * @param	string	Logfile entry
	 * @param	string	Loglevel
	 * @param	integer	Line on which syslog was triggered.
	 * @return	void
	 *
	 * @note	The loglevel should be one out of
	 *			info, notice, warning, error, alert, crit, emerg or debug,
	 *			depending on the desired action that shoul be taken.
	 */
	public function write($msg, $level = 'info', $line = null)
	{
		$this->error = $level == 'error' ? true : false;

		$this->_log->addEntry(
			array(
				'line'		=> $line,
				'level'		=> $level,
				'method'	=> $this->_method,
				'message'	=> preg_replace('/(?:\r\n|\n)/', ' ', $msg))
		);
	}
}
