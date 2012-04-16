<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewThemesHtml extends NinjaViewTemplatesHtml
{
	public function display()
	{
		$toolbar = $this->_createToolbar();

		if ($this->getName() == 'dashboard') $toolbar->reset();
		else $toolbar->append('spacer');

		$toolbar->append($this->getService('ninja:toolbar.button.about'));
		
		if(KInflector::isPlural($this->getModel()->getIdentifier()->name))
		{
            $this->_mixinMenubar();
		}
		
		$this->templates = $this->getModel()->limit(0)->getList();

		return parent::display();
	}
}