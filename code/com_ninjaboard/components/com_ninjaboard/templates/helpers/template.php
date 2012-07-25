<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Template Helper
 *
 * @package Ninjaboard
 */
 class ComNinjaboardTemplateHelperTemplate extends KTemplateHelperAbstract
 {
 	/**
 	 * Renders a spacing block, used to adjust the whitespace between blocks
 	 *
 	 * @author Stian Didriksen <stian@ninjaforge.com>
 	 */
 	public function space($config = array())
 	{
 		$config = new KConfig($config);
 		
 		$config->append(array(
 			'type'	=> 'general'
 		));
 	
 		$params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
 		return '<div class="ninjaboard-spacer" style="height:'.@$params['template'][$config->type.'_spacing'].'"></div>';
 	}
 }