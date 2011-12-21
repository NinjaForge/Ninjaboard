<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: attachment.php 1487 2011-01-22 00:34:45Z stian $
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
		KRequest::set('get.format', 'file');

		parent::__construct($config);
	}
}