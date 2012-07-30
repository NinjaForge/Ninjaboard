<?php
/**
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_latest_posts
 * @copyright	Copyright (C) 2010 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 defined( '_JEXEC' ) or die( 'Restricted access' );

 jimport('joomla.html.html');
 jimport('joomla.form.formfield');

/**
 * JFormFiledIntegration Class - for displaying a select list of forums
 */
class JFormFieldIntegration extends JFormField
{
   /**
	 * The form field type.
	 *
	 * @var    string
	 */
	public $type = 'Integration';

    /**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribue to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$options = array();

		if (file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')){
			$options[] = JHTML::_('select.option', 'nb', 'Ninjaboard', 'id', 'title');
			if (file_exists(JPATH_SITE.'/components/com_community/community.php'))
				$options[] = JHTML::_('select.option', 'js', 'JomSocial', 'id', 'title');

			if (file_exists(JPATH_SITE.'/components/com_comprofiler/comprofiler.php'))
				$options[] = JHTML::_('select.option', 'cb', 'Community Builder', 'id', 'title');

			if (file_exists(JPATH_SITE.'/components/com_cbe/cbe.php'))
				$options[] = JHTML::_('select.option', 'cbe', 'Community Builder Enhanced', 'id', 'title');

		  return JHTML::_('select.genericlist',  $options, $this->name,  '" class="inputbox"', 'id', 'title', $this->value, $this->name);
	   } else {
		   return JText::_('MOD_NINJABOARD_LATEST_POSTS_NINJABOARD_NOT_INSTALLED');
	   }
	}
}