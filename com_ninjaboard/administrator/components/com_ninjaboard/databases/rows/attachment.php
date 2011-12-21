<?php defined( 'KOOWA' ) or die( 'Restricted access' );
 /**
 * @version		$Id: attachment.php 1659 2011-03-21 21:44:56Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

//Prepare MediaHelper
JLoader::register('MediaHelper', JPATH_ROOT.'/components/com_media/helpers/media.php');

class ComNinjaboardDatabaseRowAttachment extends KDatabaseRowDefault
{
	/**
	 * Cached file type, for usage with icons
	 *
	 * @var string
	 */
	protected $_type;

	/**
	 * Cache for wether this attachment exists on the filesystem or not, so we don't hit the filesystem more than once
	 *
	 * @var boolean
	 */
	protected $_exists;
	
	/**
	 * Cache for if this attachment is an image
	 *
	 * @var boolean
	 */
	protected $_is_image;

	/**
     * Specialized in order to dynamically get the type icon
     *
     * @TODO performance optimize this
     *
     * @param  	string 	The column name.
     */
    public function __get($column)
    {
    	if($column == 'type')
		{
			if(!isset($this->_type))
			{
				$this->_type = MediaHelper::getTypeIcon($this->file);
			}

			return $this->_type;
		}

		return parent::__get($column);
	}

	/**
	 * Checks if attachment exists on the filesystem or not
	 *
	 * @return boolean	True if it exists, false if not
	 */
	public function exists()
	{
		if(!isset($this->_exists))
		{
			$this->_exists = JFile::exists(JPATH_ROOT.'/media/com_ninjaboard/attachments/'.$this->file);
		}
		return $this->_exists;
	}

	/**
	 * Returns true if it's an image
	 *
	 * @return boolean	True if image, false if not
	 */
	public function isImage()
	{
		if(!isset($this->_is_image))
		{
			//Dirty hack as MediaHelper::isImage mistakenly thinks jpeg files aren't images
			$this->_is_image = MediaHelper::isImage(str_replace('.jpeg', '.jpg', $this->file));
		}
		return $this->_is_image;
	}
	
	/**
	 * Uses self::isImage as anything not an image is a file
	 *
	 * @return boolean	False if image, true if not
	 */
	public function isFile()
	{
		return !$this->isImage();
	}
}