<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: dashboard.php 1604 2011-02-23 13:53:07Z betweenbrain $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Default Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerDashboard extends ComNinjaControllerDashboard
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		if( !isset($this->_request->tmpl) || ( isset($this->_request->tmpl) && $this->_request->tmpl != 'component' ) )
		{
			$this->registerCallback('before.browse', array($this, 'checkMigration'));
			$this->registerCallback('before.browse', array($this, 'checkInstall'));
		}
	}
	
	/**
	 * Checks if the site have migrated from 0.5, and doesn't have any data yet.
	 * If true, then it'll display a message pointing to the Tools screen and the NB importer.
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	public function checkMigration()
	{
		try {
			$migrated = KFactory::get('admin::com.ninjaboard.model.forums_backups')->getTotal();
		} catch(KDatabaseTableException $e) {
			// Do nothing if the table don't exist
			return $this;
		}
		
		//Do nothing if there's no migrated data
		if(!$migrated) return $this;

		//Do nothing if there's already data in Ninjaboard
		$existing = KFactory::get('admin::com.ninjaboard.model.forums')->getTotal();
		if($existing) return $this;
		
		JError::raiseNotice(0, sprintf(
			JText::_('Congratulations on the upgrade! You\'ll find that all your 0.5 data were backed up during the 1.0 install. %s'),
			'<a href="'.JRoute::_('&option=com_ninjaboard&view=tools&shortcut=legacy').'">'.
			JText::_('Click here to migrate.').
			'</a>'
		));
	}

	/**
	 * Checks if the site is a clean and fresh install
	 * If true, then it'll display a message pointing to the Tools screen and the Sample Content importer.
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	public function checkInstall()
	{
		//Do nothing if there's already data in Ninjaboard
		$existing = KFactory::get('admin::com.ninjaboard.model.forums')->getTotal();
		if($existing) return $this;
	
		// Check if migration table exists, and if it contain data
		$migrated = 0;
		try {
			$migrated = KFactory::get('admin::com.ninjaboard.model.forums_backups')->getTotal();
		} catch(KDatabaseTableException $e) {
			//Do nothing
		}
		
		//We can't auto import sample data
		if($migrated) return;
		
		foreach(array('assets', 'forums', 'joomlausergroupmaps', 'posts', 'ranks', 'topics', 'usergroups') as $model) {
			if(KFactory::tmp('admin::com.ninjaboard.model.'.$model)->getTotal()) return;
		}
		
		KFactory::get('admin::com.ninjaboard.controller.tool')->execute('import');
		JError::raiseNotice(0, JText::_('In order to get you started with using Ninjaboard, Sample Content was just imported.'));
	}
}