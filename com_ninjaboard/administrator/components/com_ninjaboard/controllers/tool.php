<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: tool.php 2186 2011-07-11 22:27:36Z stian $
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

        // Make sure that only super admins can do imports
        $this->registerCallback(array('before.display', 'before.import'), array($this, 'authorize'));

		$this->registerCallback('before.display', array($this, 'raiseNotice'));
		
		//If there's a shortcut, run the fireShortcut function
		if(isset($this->_request->shortcut)) $this->registerCallback('after.display', array($this, 'fireShortcut'));
		
		$cache = JPATH_ROOT.'/cache/com_'.$this->getIdentifier()->package;
		
		if(JFolder::exists($cache))
		{
			JFolder::delete($cache);
		}
	}

	public function authorize(KCommandContext $context)
	{
	    $user = KFactory::get('lib.joomla.user');
	    $app  = KFactory::get('lib.joomla.application');
	    if(!$user->authorize('com_config', 'manage'))
	    {
	        $app->redirect('index.php?option=com_ninjaboard', JText::_("You don't have permissions to use Ninjaboard administration tools."));

	        return false;
	    }
	}
	
	protected function _actionDisplay(KCommandContext $context)
	{	
	    /*
	    //This is used for xdebug to profile imports
	    $this->_request->import = 'kunena';
	    KRequest::set('post.offset', 'int', 0);
	    $this->execute('import');
	    //*/
	    
	    if(KRequest::type() != 'AJAX')
	    {
	    	$view = $this->getView();
		
		    return $view->display();
		}
		
		return '';
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
					button.fireEvent('mousedown', [], delay).fireEvent('click', [], delay);
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
		$this->getModel()->getItem()->convert();
	}
}