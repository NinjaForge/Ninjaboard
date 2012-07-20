<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Avatars model
 *
 * Gets the avatars, allowing gravatar, cb integration and more through a single interface
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelAvatars extends ComNinjaboardModelPeople
{
	/**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
	public function __construct($config)
	{
		parent::__construct($config);
		
		$this->_state
					->insert('thumbnail', 'cmd', 'large');
	}

	/**
     * Method to get a item object which represents a table row
     *
     * @return KDatabaseRow
     */
	public function getItem()
	{
		if(!$this->_item)
		{
			$this->_item = parent::getItem();
			$id			 = $this->_item->id ? $this->_item->id : $this->_state->id;
			$email		 = $this->_item->emaile ? $this->_item->email : JFactory::getUser($id)->email;
			
			$settings = $this->getService('com://site/ninjaboard.model.settings')->getParams();
			$settings = $settings['avatar_settings'];

			// set our default avatar and use it if we have not uploaded one
			$default  			= '/media/com_ninjaboard/images/avatar.png';
			$this->_item->image = $this->_item->avatar ? $this->_item->avatar : $default;
			
			$size = $this->_state->thumbnail == 'small' ? 'small' : 'large';
			
			//If we have an avatar and its an external url then try and download it
			if($this->getService('koowa:filter.url')->validate($this->_item->image)) {
				// Prepare curl
				$curl = $this->getService('ninja:helper.curl');
				$opt  = array(
								CURLOPT_RETURNTRANSFER => true
						);
				$curl->addSession($this->_item->image, $opt );
		
				$image = $curl->exec();
				if($image != '404 Not Found') {
					$dest  = '/media/com_ninjaboard/images/avatars/'.$id.'/avatar.png';
					if (JFile::write(JPATH_ROOT.$dest, $image)) $this->_item->image = $dest;
				}
			} elseif(!JFile::exists(JPATH_ROOT.$this->_item->image) || $this->_item->image == $default) {
				if($settings['enable_gravatar']) {
					//Gravatars are square, so use the largest value as size
					$gsize = max($settings[$size.'_thumbnail_width'], $settings[$size.'_thumbnail_height']);
					$gravatar  = $this->getService('com://site/ninjaboard.template.helper.avatar')->gravatar(array(
																											'email' => $email,
																											'size'  => $gsize
																										));
					// Prepare curl
					$curl = $this->getService('ninja:helper.curl');
					$opt  = array(
									CURLOPT_RETURNTRANSFER => true
							);
					$curl->addSession($gravatar, $opt );
				
					$image = $curl->exec();
					if($image != '404 Not Found') {
						$dest  = '/media/com_ninjaboard/images/avatars/'.$id.'/gravatar.png';
						if (JFile::write(JPATH_ROOT.$dest, $image)) $this->_item->image = $dest;
					}
				}
			}

			$this->_item->image = $this->getService('ninja:helper.image', array('image' => JPATH_ROOT.$this->_item->image));

			$from	= $this->_item->image->width / $this->_item->image->height;
			$to		= $settings[$size.'_thumbnail_width'] / $settings[$size.'_thumbnail_height'];
			if($from > $to) {
				$this->_item->image->resize(false, $settings[$size.'_thumbnail_height'], NinjaHelperImage::HEIGHT);
			} else {
				$this->_item->image->resize($settings[$size.'_thumbnail_width'], false, NinjaHelperImage::WIDTH);
			}
			
			$this->_item->image
								->crop($settings[$size.'_thumbnail_width'], $settings[$size.'_thumbnail_height'])
								->quality($settings['thumbnail_quality']);
		}

		return $this->_item;
	}
	
	/**
     * Method to get a image object
     *
     * @return NinjaHelperImage
     */
	public function getImage()
	{
		return $this->getItem()->image;
	}
}