<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: abstract.php 1409 2011-01-13 02:03:32Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::load('admin::com.ninja.controller.view');

/**
 * Ninjaboard Abstract Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerAbstract extends ComNinjaControllerView
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		if(!$config->request) $config->request = new KConfig;
	
		$config->request->append(array(
			'layout' => 'default'
		));
		
		parent::__construct($config);
	}

	/**
	 * Get's the redirect URL from the referrer and saves in the session.
	 *
	 * @return void
	 */
	public function checkReferrer()
	{
		return;
	}
}