<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Tools Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerTool extends NinjaControllerDefault
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
        $this->registerCallback(array('before.get', 'before.import'), array($this, 'authorize'));

		$this->registerCallback('before.get', array($this, 'raiseNotice'));
		
		//If there's a shortcut, run the fireShortcut function
		if(isset($this->_request->shortcut)) $this->registerCallback('after.get', array($this, 'fireShortcut'));
		
		$cache = JPATH_ROOT.'/cache/com_'.$this->getIdentifier()->package;
		
		if(JFolder::exists($cache))
		{
			JFolder::delete($cache);
		}
	}

	public function authorize(KCommandContext $context)
	{
	    $user = JFactory::getUser();
	    $app  = JFactory::getApplication();
	    if(!$user->authorize('com_config', 'manage'))
	    {
	        //@TODO implement differently
	        //$app->redirect('index.php?option=com_ninjaboard', JText::_('COM_NINJABOARD_YOU_DONT_HAVE_PERMISSIONS_TO_USE_NINJABOARD_ADMINISTRATION_TOOLS'));

	        //return false;
	    }
	}
	
	protected function _actionGet(KCommandContext $context)
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
	    	
	    	if($view instanceof KViewTemplate) {
	    		$view->getTemplate()->addFilter(array($this->getService('ninja:template.filter.document')));
	    	}
		
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
		$shortcut = $this->getService('koowa:filter.cmd')->sanitize($this->_request->shortcut);
		JFactory::getDocument()->addScriptDeclaration("
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
		JError::raiseNotice(0, JText::_('COM_NINJABOARD_IMPORTED_DATA_WILL_REPLACE_ANY_EXISTING_DATA_ALWAYS_REMEMBER_TO_BACKUP_YOUR_SITE_PRIOR_TO_IMPORTS'));
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