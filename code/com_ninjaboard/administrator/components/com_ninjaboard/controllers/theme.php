<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Theme Controller
 *
 * @TODO implement a filesystem rowset and row instead of overriding _actionGet
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerTheme extends NinjaControllerDefault
{
	/**
	 * Get action
	 * 
	 * This function translates a GET request into a read or browse action. If the view name is 
	 * singular a read action will be executed, if plural a browse action will be executed.
	 * 
	 * If the result of the read or browse action is not a row or rowset object the fucntion will
	 * passthrough the result, request the attached view to render itself.
	 *
	 * @param	KCommandContext	A command context object
	 * @return 	string|false 	The rendered output of the view or FALSE if something went wrong
	 */
	protected function _actionGet(KCommandContext $context)
	{
		//Check if we are reading or browsing
	    $action = KInflector::isSingular($this->getView()->getName()) ? 'read' : 'browse';
	    
	    //Execute the action
		$result = $this->execute($action, $context);
		
		$view = $this->getView();
		
		if($view instanceof KViewTemplate) {
			$view->getTemplate()->addFilter(array($this->getService('ninja:template.filter.document')));
		}
		
		
		return $view->display();
	}
}