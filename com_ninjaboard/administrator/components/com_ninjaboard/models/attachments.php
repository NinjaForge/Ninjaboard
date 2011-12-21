<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: attachments.php 1610 2011-02-27 01:02:15Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Attachments model
 *
 * Fetches posts' attachments
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelAttachments extends ComDefaultModelDefault
{
	
	/**
	 * Only attachments that are images
	 *
	 * @var array
	 */
	protected $_images = array();
	
	/**
	 * Only attachments that are isn't images
	 *
	 * @var array
	 */
	protected $_files = array();

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);

		$this->_state
						->insert('file', 'cmd')
						->insert('post', 'int')
						->insert('id'  , 'int');
	}
	
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);

		if($file = $this->_state->file) $query->where('tbl.file'	, '=', $file, 'and');
		if($post = $this->_state->post) $query->where('tbl.post_id'	, '=', $post, 'and');
	}
	
	public function getList()
	{
		if(!isset($this->_list))
		{
			require_once JPATH_ROOT.'/components/com_media/helpers/media.php';

			foreach(parent::getList() as $item)
			{
				$item->type = MediaHelper::getTypeIcon($item->file);
				if(MediaHelper::isImage($item->file))
				{
					$this->_images[] = $item;
				}
				else
				{
					$this->_files[] = $item;
				}
			}
		}

		return $this->_list;
	}
	
	/**
	 * Get only attachments that are images
	 */
	public function getImages()
	{
		if(!isset($this->_list)) $this->getList();

		return $this->_images;
	}
	
	/**
	 * Get only attachments that isn't images
	 */
	public function getFiles()
	{
		if(!isset($this->_list)) $this->getList();

		return $this->_files;
	}
}