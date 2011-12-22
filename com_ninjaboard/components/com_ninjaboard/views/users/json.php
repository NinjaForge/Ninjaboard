<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::load('admin::com.ninja.view.json');

class ComNinjaboardViewUsersJson extends ComNinjaViewJson
{
	/**
	 * Return the views output
		 *
	 *  @return string 	The output of the view
	 */
	public function display()
	{
		//If it's not a autocomplete query, don't do anything yet
		//@TODO in the future show a default json view with data
		if(!KRequest::has('get.is_autocomplete')) return;
	
	
		$model	= $this->getModel();		
		$data	= array();
		foreach($model->getList() as $user)
		{
			//Autocomplete format is the following:
			// id, searchable plain text, html (for the textboxlist item, if empty the plain is used), html (for the autocomplete dropdown)
			//We pass the search state as the plain text as every result in this query is a match on the server side
			//if a user match by email for instance, we don't want to reveal the email in order for the autocomplete to match
			$data[] = array($user->id, $model->getState()->search, $user->display_name, '<img src="'.$this->createRoute('view=avatar&id='.$user->id.'&thumbnail=small&format=file').'" /> ' . $user->display_name);
		}
		
		return json_encode($data);
	}
}