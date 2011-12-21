<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: setting.php 1360 2011-01-10 20:02:08Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Settings Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerSetting extends ComNinjaControllerSetting
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

		$this->registerCallback('before.browse', array($this, 'setDefault'));
	}

	/**
	 * Makes sure that at least one settings profile is set as default
	 *
	 * @param  KCommandContext $context
	 */
	public function setDefault(KCommandContext $context)
	{
		$table = $this->getModel()->getTable();
		
		//Don't do anything if there are no settings rows
		if($table->count(array()) === 0) return;
		
		//Don't do anything if there already exists rows that are enabled and default
		if($table->count(array('enabled' => true, 'default' => true)) > 1) return;
		
		//Don't do anything if there are no enabled rows
		if($table->count(array('enabled' => true)) === 0) return;
		
		//Undefault any other default setting
		$table->select(array('default' => true), KDatabase::FETCH_ROWSET)->setData(array('default' => false))->save();
		
		//Set one row as the default
		$table->select(array('enabled' => true), KDatabase::FETCH_ROW)->setData(array('default' => true))->save();
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