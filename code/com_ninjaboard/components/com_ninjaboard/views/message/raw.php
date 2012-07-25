<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

KLoader::loadIdentifier('com://site/ninjaboard.view.message.html');

class ComNinjaboardViewMessageRaw extends ComNinjaboardViewMessageHtml
{
	//Extends the html view in order to assign other variables than just $message to the layout
}