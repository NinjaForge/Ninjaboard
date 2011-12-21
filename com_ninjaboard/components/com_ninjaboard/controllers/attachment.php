<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: attachment.php 1409 2011-01-13 02:03:32Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Attachment Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerAttachment extends ComNinjaboardControllerAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_request->format = 'file';

		//Set format if not already set
		if(!KRequest::has('get.format', 'cmd')) KRequest::set('get.format', 'file');
	}
}