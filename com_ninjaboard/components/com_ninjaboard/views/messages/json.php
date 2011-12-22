<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::load('admin::com.ninja.view.json');

class ComNinjaboardViewMessagesJson extends ComNinjaViewJson
{
	/**
	 * Return the views output
		 *
	 *  @return string 	The output of the view
	 */
	public function display()
	{
		$model		= $this->getModel();
		$template	= KFactory::get('site::com.ninjaboard.view.message.html')->getTemplate();
		$params 	= KFactory::get('admin::com.ninjaboard.model.settings')->getParams();
		$me			= KFactory::get('admin::com.ninjaboard.model.people')->getMe();
		$data		= array();

		foreach($model->getList() as $message)
		{
			$result			= $message->getData();
			$result['html']	= $template->loadIdentifier('site::com.ninjaboard.view.message.list', array(
				'message'	=> $message,
				'params'	=> $params,
				'me'		=> $me
			))->render(true);
			$data[] = $result;
		}
		
		return json_encode($data);
	}
}