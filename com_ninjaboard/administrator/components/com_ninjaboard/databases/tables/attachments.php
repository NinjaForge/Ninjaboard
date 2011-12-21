<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: attachments.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	 	http://ninjaforge.com
 */

class ComNinjaboardDatabaseTableAttachments extends KDatabaseTableDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{	
		parent::__construct($config);

		$this->_column_map = array_merge(
			$this->_column_map,
			array(
				'name'	=> 'params',
				'post'	=> 'post_id'
			)
		);
	}
}