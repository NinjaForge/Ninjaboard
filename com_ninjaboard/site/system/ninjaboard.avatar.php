<?php
/**
 * @version $Id: ninjaboard.avatar.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Avatar
 *
 * @package Ninjaboard
 */
class NinjaboardAvatar
{
	/**
	 * avatar file
	 *
	 * @var string
	 */
	var $avatarFile;
	
	/**
	 * constructor
	 */
	function NinjaboardAvatar($avatarFile) {
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		
		$avatarPath = $ninjaboardConfig->getAvatarSettings('avatar_path');

		if ($avatarFile == '') {
			$avatarFile = '_ninjaboard_noavatar.png';
		}

		if (file_exists(JPATH_SITE.DS.$avatarPath.DS.$avatarFile)) {
			$avatarFile = JURI::root().$avatarPath.DL.$avatarFile;
		} else {
			$avatarFile = '';
		}		
		$this->avatarFile = $avatarFile;
	}

	/**
	 * delete avatar
	 *
	 * @access 	public
	 */	
	function deleteAvatar($avatarFile) {
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$avatarPath = $ninjaboardConfig->getAvatarSettings('avatar_path');
		return @unlink(JPATH_SITE.DS.$avatarPath.DS.$avatarFile);
	}

	/**
	 * upload avatar file
	 *
	 * @access 	public
	 */		
	function uploadAvatarFile($avatarFile, &$ninjaboardUser) {
	
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		
		jimport('joomla.filesystem.file');
		
		if (isset($avatarFile['name']) && $avatarFile['name'] != '') {
			$fileTypes = $ninjaboardConfig->getAvatarSettings('avatar_file_types');
			
			// check file types
			$fileTypes = str_replace(",", "|", $fileTypes); // we have to use comma and replace it! joomla is using "|" since rc4
			if (preg_match("/$fileTypes/i", $avatarFile['name'])) {

				$avatarWidth = $ninjaboardConfig->getAvatarSettings('avatar_width');
				$avatarHeight = $ninjaboardConfig->getAvatarSettings('avatar_height');
				
				// here we go with automatic image resize
				$ninjaboardGD =& NinjaboardGD::getInstance();
				if ($ninjaboardConfig->getAvatarSettings('image_resize') && $ninjaboardGD->isEnabled()) {
					if (!$ninjaboardGD->resizeImage($avatarFile['tmp_name'], $avatarWidth, $avatarHeight)) {
						return;
					}	
				} else {
				
					// check max file size
					if ($avatarFile['size'] > $ninjaboardConfig->getAvatarSettings('avatar_max_file_size')) {
						$ninjaboardUser->setError(JText::sprintf('NB_MSGMAXIMALFILESIZE', $avatarFile['size'], $ninjaboardConfig->getAvatarSettings('avatar_max_file_size')));
						return;
					}

					// check image size
					$imageSize = getimagesize($avatarFile['tmp_name']);
					if ($imageSize[0] > $avatarWidth || $imageSize[1] > $avatarHeight) {	
						$ninjaboardUser->setError(JText::sprintf('NB_MSGREQUIREDWIDTHHEIGHT', $avatarWidth, $avatarHeight));
						return;
					}
				}

				if ($ninjaboardUser->get('avatar_file') == '') {
					$pathParts = pathinfo($avatarFile['name']);
					
					// generate a unique file name
					$fileName = strtolower(JUserHelper::genRandomPassword(10).'.'.$pathParts['extension']);
					while (file_exists($fileName)) {
						$fileName = strtolower(JUserHelper::genRandomPassword(10).'.'.$pathParts['extension']);
					}
					
					$ninjaboardUser->set('avatar_file', $fileName);
				} else {
					$fileName = $ninjaboardUser->get('avatar_file');
				}
				
				$filePath = JPath::clean(JPATH_ROOT.DS.$ninjaboardConfig->getAvatarSettings('avatar_path').DS.$fileName);
				
				// upload avatar
				if (!JFile::upload($avatarFile['tmp_name'], $filePath)) {
					$ninjaboardUser->setError(JText::sprintf('NB_MSGAVATARFILEUPLOADFAILED', $ninjaboardUser->get('name')));
				}
						
			} else {
				$ninjaboardUser->setError(JText::sprintf('NB_MSGFILETYPENOTSUPPORTED', 'file type'));
			}
		}
	}
				
}
?>