<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: raw.php 2469 2011-11-01 14:09:17Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::loadIdentifier('com://site/ninjaboard.view.message.html');

class ComNinjaboardViewMessageRaw extends ComNinjaboardViewMessageHtml
{
	//Extends the html view in order to assign other variables than just $message to the layout
}