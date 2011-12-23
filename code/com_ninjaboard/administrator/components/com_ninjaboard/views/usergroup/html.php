<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
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
	
		$objects = $this->getService('com://admin/ninjaboard.permissions')->getObjects();
		foreach ($objects as $object) 
		{
			$names[] = 'com_ninjaboard.usergroup.'.$this->usergroup->id.'.'.$object;
		}
	
		$this->permissions = $this->getService('ninja:template.helper.access', array(
			'name'		=> $names,
			'id'		=> $this->getService('ninja:template.helper.document')->formid('permissions'),
			'inputName'	=> 'permissions',
			'inputId'	=> 'permissions',
			'render'	=> 'usergroups',
			'objects'	=> $objects
		));

		return parent::display();
	}
}