<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
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
						->insert('post', 'int');
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
			foreach(parent::getList() as $item)
			{
				//@TODO store broken links in a separate array allowing us to list missing/deleted attachments
				if(!$item->exists()) continue;

				if($item->isImage())
				{
					$this->_images[] = $item;
				}
				elseif($item->isFile())
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