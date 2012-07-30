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
 * JFormFieldForums Class - for displaying a select list of forums
 */
class JFormFieldForums extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	public $type = 'Forums';

   /**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribue to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		if (file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')){
			$options 	= array();
			$size 		= $this->size ? $this->size : 5;
			$list 		= KService::get('com://admin/ninjaboard.model.forums')->enabled(1)->getList();

			foreach ($list as $item) {
				$options[] = JHTML::_('select.option', $item->id, str_repeat('&#160;', ($item->level - 1) * 6).$item->title, 'id', 'title');
			}
			
			return JHTML::_('select.genericlist',  $options, $this->name.'[]',  ' multiple="multiple" size="' . $size .'" class="inputbox"', 'id', 'title', $this->value, $this->name);
	   } else {
		   return JText::_('MOD_NINJABOARD_LATEST_POSTS_NINJABOARD_NOT_INSTALLED');
	   }
	}
}