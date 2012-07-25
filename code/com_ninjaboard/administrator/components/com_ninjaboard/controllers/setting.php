<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Settings Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerSetting extends NinjaControllerSetting
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->registerCallback('before.add',	array($this, 'setTheme'));
		$this->registerCallback('before.edit',	array($this, 'setTheme'));
	}

	/**
	 * Temporary workaround for setting the theme when needed
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  KCommandContext $context
	 */
	public function setTheme(KCommandContext $context)
	{
		//If the theme isn't set in the params, don't do anything
		if(!isset($context->data->params->board_details->theme)) return $this;
		
		$context->data->theme = $context->data->params->board_details->theme;
	}
}