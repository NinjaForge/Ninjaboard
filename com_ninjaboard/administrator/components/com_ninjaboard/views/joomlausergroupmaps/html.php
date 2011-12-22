<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewJoomlausergroupmapsHtml extends ComNinjaboardViewHtml
{
	public function display()
	{		
		// Display the toolbar
		$toolbar = $this->_createToolbar()->reset();
		
		$this->toolbar = KFactory::get($this->getToolbar(), array('isGrid' => false))
							->append(KFactory::get('admin::com.ninja.toolbar.button.apply', array('isGrid' => false)));

		KFactory::get('admin::com.ninja.helper.default')->css('/' . $this->getIdentifier()->application . '.' . $this->getName() . '.css');
		KFactory::get('admin::com.ninja.helper.default')->js('/raphael.js');
		KFactory::get('admin::com.ninja.helper.default')->js('/Mapper.js');
		
		$acl		= JFactory::getACL();
		//die('<pre>'.print_r(get_class_methods($acl), true).'</pre>');
		$acltree    = (array)$acl->get_group_children_tree( null, 'USERS', false );
		array_unshift($acltree, (object) array('value' => 0, 'text' => 'Unregistered', 'disabled' => false));
		$this->acltree = $acltree;

		$this->usergroups = KFactory::tmp('admin::com.ninjaboard.model.usergroups')->limit(0)->getList();
		
		$maps = array();
		foreach(KFactory::tmp($this->getModel()->getIdentifier())->limit(0)->getList() as $map)
		{
			$maps[$map->id] = $map->ninjaboard_gid;
		}
		$this->maps = $maps;
		

		$display = parent::display();

		if(KRequest::type() == 'AJAX')
		{
			echo '<script type="text/javascript">
			'.$this->_document->_script['text/javascript'].'
			</script>';
		}
		
		return $display;
	}
	
	public function _createToolbar()
	{
		$identifier	= $this->getToolbar();
		$name		= $identifier->name;
		$package	= $identifier->package;
		
		KFactory::get('admin::com.ninja.helper.default')->css('/toolbar.css');
		$img = KInflector::isPLural($name) 
						? KFactory::get('admin::com.ninja.helper.default')->img('/48/'.$name.'.png')
						: KFactory::get('admin::com.ninja.helper.default')->img('/48/'.KInflector::pluralize($name).'.png');
		if(!$img)
		{
			$img = KInflector::isSingular($name) 
						? KFactory::get('admin::com.ninja.helper.default')->img('/48/'.$name.'.png')
						: KFactory::get('admin::com.ninja.helper.default')->img('/48/'.KInflector::singularize($name).'.png');
		}
		if($img) 
		{
			KFactory::get('admin::com.ninja.helper.default')->css('.header.icon-48-'.$name.' { background-image: url(' . $img . '); }');
		}
		return KFactory::get($identifier, array('icon' => $name));
	}
}