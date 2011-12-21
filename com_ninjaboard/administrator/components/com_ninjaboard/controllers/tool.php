<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: tool.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Tools Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerTool extends ComNinjaControllerView
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$config->append(array(
			'request' => array(
				'import' => 'demo'
			)
		));
	
		parent::__construct($config);

		$this->registerFunctionBefore('browse',	'raiseNotice');
		
		//If there's a shortcut, run the fireShortcut function
		if(isset($this->_request->shortcut)) $this->registerFunctionAfter('browse', 'fireShortcut');
		
		$cache = JPATH_ROOT.'/cache/com_'.$this->getIdentifier()->package . '/maintenance.forums.txt';
		
		if(JFile::exists($cache))
		{
			JFile::delete($cache);
		}
	}
	
	/**
	 * Fires an importer onload.
	 * Used by various help messages to improve the user experience
	 * by linking to import actions from any view.
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	public function fireShortcut()
	{
		$shortcut = KFactory::get('lib.koowa.filter.cmd')->sanitize($this->_request->shortcut);
		KFactory::get('lib.joomla.document')->addScriptDeclaration("
			window.addEvent('domready', function(){
				var button = document.getElement('.placeholder .$shortcut');
				if(button) {
					var delay = $('$shortcut-form') ? 300 : 1000;
					button.fireEvent('click', [], delay);
				}
			});
		");
	}

	public function raiseNotice()
	{
		JError::raiseNotice(0, JText::_("Imported data will replace any existing data. Always remember to backup your site prior to imports."));
	}
	
	/*
	 * Generic import action
	 *
	 * @throws KControllerException
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	protected function _actionImport()
	{
		$request   = $this->getRequest();		
		$converter = KFactory::get($this->getModel())->getItem()->convert();

		if($converter->splittable && !isset($request->offset))
		{
			KFactory::get('lib.koowa.application')->close();
			return;
		}
		
		if(KRequest::type() == 'AJAX')
		{
			/*
			$data = array();
			foreach($converter->data as $name => $rowset) foreach($rowset as $row) $data[$name][] = $row->getData();
			echo json_encode($data);
			//*/
			KFactory::get('lib.koowa.application')->close();
		}
		
		if(isset($request->print_r)) echo '<pre>', print_r($converter, true), '</pre>';
	}
}