<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 2470 2011-11-01 14:22:28Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewToolsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->db = JFactory::getDBO();		
		$configs = JFactory::getConfig();
		$this->config = new KConfig;
		foreach(array('host', 'user', 'db', 'dbprefix', 'password') as $config)
		{
			$this->config->$config = $configs->getValue($config);
		}

		$this->menu = $this->getService('ninja:template.helper.placeholder', array('notice' => 'Select what you want to import.'));

		$this->converters = $this->getModel()->limit(0)->getList();
		foreach($this->converters as $name => $converter)
		{
			if(!$converter->button) continue;

			$attribs = array_merge(array('data-name' => $name), $converter->getAttributes());
			$this->menu->append($name, $attribs, $converter->getTitle());
		}

		return parent::display();
	}
}