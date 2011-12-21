<?php
/**
 * @version $Id: view.html.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Ninjaboard User Posts View
 *
 * @package Ninjaboard
 */
class NinjaboardViewUserPosts extends JView
{

	function display($tpl = null) {
		global $mainframe;

		// initialize variables
		$document		=& JFactory::getDocument();
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$breadCrumbs	=& NinjaboardBreadCrumbs::getInstance();

		$userId = JRequest::getVar('id', 0, '', 'int');
		$ninjaboardUserPosts	=& NinjaboardUser::getInstance($userId);
		$this->assignRef('ninjaboardUserPosts', $ninjaboardUserPosts);
		
		$model	=& $this->getModel();
		$posts	= new NinjaboardPost($model->getUserPosts($userId));
		$this->assignRef('posts', $posts);

		// request variables
		$limit		= JRequest::getVar('limit', $ninjaboardConfig->getBoardSettings('posts_per_page'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		
		$total = count($posts->posts);
		$this->assignRef('total', $total);
		
		$showPagination = false;
		if ($total > $limit) {
			$showPagination = true;
		}
		$this->assign('showPagination', $showPagination);

		jimport('joomla.html.pagination');
		$this->pagination = new JPagination($total, $limitstart, $limit);

		// handle page title
		$document->setTitle(JText::sprintf('NB_POSTSFROMUSER', $ninjaboardUserPosts->get('name')));
		
		// handle bread crumb
		$breadCrumbs->addBreadCrumb(JText::sprintf('NB_POSTSFROMUSER', $ninjaboardUserPosts->get('name')), '');

		parent::display($tpl);
	}

}
?>