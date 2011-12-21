<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: json.php 2470 2011-11-01 14:22:28Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::loadIdentifier('ninja:view.json');

class ComNinjaboardViewMessagesJson extends NinjaViewJson
{
	/**
	 * Return the views output
		 *
	 *  @return string 	The output of the view
	 */
	public function display()
	{
		$model		= $this->getModel();
		$template	= $this->getService('com://site/ninjaboard.view.message.html')->getTemplate();
		$params 	= $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$me			= $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$data		= array();

		foreach($model->getList() as $message)
		{
			$result			= $message->getData();
			$result['html']	= $template->loadIdentifier('com://site/ninjaboard.view.message.list', array(
				'message'	=> $message,
				'params'	=> $params,
				'me'		=> $me
			))->render(true);
			$data[] = $result;
		}
		
		return json_encode($data);
	}
}