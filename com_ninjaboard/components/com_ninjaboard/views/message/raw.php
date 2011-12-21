<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: raw.php 1626 2011-03-02 02:18:55Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::load('site::com.ninjaboard.view.message.html');

class ComNinjaboardViewMessageRaw extends ComNinjaboardViewMessageHtml
{
	//Extends the html view in order to assign other variables than just $message to the layout
}