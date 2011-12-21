<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewUsergroupHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		$this->usergroup = $this->getModel()->getItem();
	
		$objects = KFactory::get('admin::com.ninjaboard.permissions')->getObjects();
		foreach ($objects as $object) 
		{
			$names[] = 'com_ninjaboard.usergroup.'.$this->usergroup->id.'.'.$object;
		}
	
		$this->permissions = KFactory::tmp('admin::com.ninja.helper.access', array(
			'name'		=> $names,
			'id'		=> KFactory::get('admin::com.ninja.helper.default')->formid('permissions'),
			'inputName'	=> 'permissions',
			'inputId'	=> 'permissions',
			'render'	=> 'usergroups',
			'objects'	=> $objects
		));
		
		return parent::display();
	}
}