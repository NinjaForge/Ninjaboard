<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewUserHtml extends ComNinjaboardViewHtml
{
	public function display()
	{
		//Get User details from Joomla!
		$joomla = array(
			'Name' => 'name',
			'Username' => 'username',
			'E-Mail' => 'email',
			'Group' => 'usertype',
			'Register Date' => 'registerDate',
			'Last Visit Date' => 'lastvisitDate'
		);
		$this->assign('joomla', $joomla);
		
		$this->assign('ifedit', range(0, 1));
		
		
		//Get user
		$usertype = JFactory::getUser()->get('usertype');
		
		$this->assign('avatar', null);
		
		$user = $this->getModel()->getItem();
		$usergroups = array();

		foreach($user->usergroups as $group)
		{
			$usergroups[] = $group->id;
		}
		$this->usergroups = $usergroups;
		if($user->inherits)
		{
			$this->usergroups = 0;
			$inherits = array();
			foreach($user->usergroups as $usergroup)
			{
				$link  = '<a href="'.$this->createRoute('view=usergroup&id='.$usergroup->id).'">';
				$link .= $usergroup->title;
				$link .= '</a>';
				$inherits[] = $link;
			}
			$this->inherits = implode(', ', $inherits);
		}

		// Display the layout
		return parent::display();
	}

	protected function createElement($field, $fieldvalue = '0')
	{		
		return $field;
	}	
}