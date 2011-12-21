<?php
/**
 * @version $Id: terms.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Terms Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelTerms extends NinjaboardModel
{
	/**
	 * terms table object
	 *
	 * @var object
	 */
	var $_terms = null;

	/**
	 * get terms
	 *
	 * @return object
	 */
	function getTerms() {

		// load the forum
		if (empty($this->_terms)) {
			$db		=& JFactory::getDBO();
			$locale = NinjaboardHelper::getLocale();
			
			do {
				$query = "SELECT t.id"
						. "\n FROM #__ninjaboard_terms AS t"
						. "\n WHERE t.locale = ".$db->Quote($locale)
						. "\n AND t.published = 1"
						;
				$db->setQuery($query);
				$termsId = $db->loadResult();

				if ($termsId) {
					$tryAgain = 0;
				} else {
					if ($locale == 'en-GB') {
						$tryAgain = 0;
					} else {
						$locale = 'en-GB'; 
						$tryAgain = 1;					
					}
				}
			} while($tryAgain);
			
			$this->_terms =& JTable::getInstance('NinjaboardTerms');
			$this->_terms->load($termsId);
		}

		return $this->_terms;
	}

}
?>