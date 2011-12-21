<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: configurable.php 1357 2011-01-10 18:45:58Z stian $
 * @package		Ninjaboard
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */ 

/**
 * Behavior that makes it possible to override settings per row for params
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @package     Ninjaboard
 * @subpackage 	Behaviors
 */
class ComNinjaboardDatabaseBehaviorConfigurable extends KDatabaseBehaviorAbstract
{
	/**
	 * Decodes json data on each field
	 * 
	 * @return boolean	true.
	 */
	protected function _afterTableSelect(KCommandContext $context)
	{
		//We should only run this on row objects
		if($context->mode == KDatabase::FETCH_FIELD) return;

		$rows		= $context['data'];
		$identifier	= clone $rows->getTable();
		$identifier->path = array('model');
		$identifier->name = 'settings';
		$defaults	= KFactory::get($identifier)->getParams()->toArray();


		if(is_a($rows, 'KDatabaseRowInterface')) $rows = array($rows);


		foreach($rows as $row)
		{
			//if(is_array($row->params)) continue;
			//echo $row->params;
			//if(is_array($row->params)) die('<pre>'.var_export(is_string($row->params) && !is_null($row->params), true).'</pre>');
			//$true = false;
			//if(is_array($row->params)) $true = true;
			$params = is_string($row->params) ? json_decode($row->params, true) : $row->params;

			//if(!is_array($params)) $params = array();
			//if($true) die('<pre>'.__CLASS__.' '.var_export($params, true).'</pre>');
			$params = new KConfig($params);
			//@TODO Make this configurable, instead of hardcoding the defaults to only apply when JSite
			if(KFactory::get('lib.joomla.application')->isSite()) $params->append($defaults);
			$row->params = $params;
		}
	}
}