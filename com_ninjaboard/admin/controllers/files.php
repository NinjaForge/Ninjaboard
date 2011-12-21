<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: files.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

jimport('joomla.application.component.controller');

class NinjaboardControllerFiles extends JController
{
		
	function remove ()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Get component name
		$option = JRequest::getCmd('option');
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		//Depending on the task, set the upload directory
		$uploaddir = JPATH_COMPONENT_SITE.DS.'designs'.DS;
		switch ($this->getTask()) {
			case 'removetemplate':		$uploaddir .= 'templates';	$oldController = 'template';	break;
			case 'removestyle':			$uploaddir .= 'styles';		$oldController = 'style';		break;
			case 'removeemoticonset':	$uploaddir .= 'emoticons';	$oldController = 'emoticonset';	break;
			case 'removebuttonset':		$uploaddir .= 'buttons';	$oldController = 'buttonset';	break;
			case 'removeiconset':		$uploaddir .= 'icons';		$oldController = 'iconset';		break;
		}		
		
		//get the names of the files to be deleted
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );

		//loop through our file list
		for ($i=0, $n=count($cid); $i < $n; $i++) {
			$folder = substr($cid[$i], 0, strpos($cid[$i], '.xml'));
			if (!JFolder::delete($uploaddir.DS.$folder))
				$this->setRedirect(
					'index.php?option='.$option.'&task=display&controller='.$oldController,
					JText::_('Error deleting :').$cid[$i].'. '.JText::_('Operation aborted')
				);	
		}
		
		$this->setRedirect('index.php?option=' . $option .'&task=display&controller='.$oldController, JText::_('Files Deleted'));
	}
	
	function saveuploadedfiles() 
	{
		//check the form token
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Get component name
		$option = JRequest::getCmd('option');
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		
		//Depending on the task, set the upload directory
		$uploaddir = JPATH_COMPONENT_SITE.DS.'designs'.DS;
		switch ($this->getTask()) {
			case 'uploadtemplate':		$uploaddir .= 'templates';	$oldController = 'template';	break;
			case 'uploadstyle':			$uploaddir .= 'styles';		$oldController = 'style';		break;
			case 'uploademoticonset':	$uploaddir .= 'emoticons';	$oldController = 'emoticonset';	break;
			case 'uploadbuttonset':		$uploaddir .= 'buttons';	$oldController = 'buttonset';	break;
			case 'uploadiconset':		$uploaddir .= 'icons';		$oldController = 'iconset';		break;
		}		
		
		// Now let's deal with the upload
		// possible PHP upload errors
		$errors = array(
			1 => JText::_('php ini max file size exceeded'),
			2 => JText::_('html form max file size exceeded'),
			3 => JText::_('File upload was only partial'),
			4 => JText::_('No file was attached')
		); 
		
		
		// At first we check that the file we are working on really was the subject of an HTTP upload
		if (! @is_uploaded_file($_FILES['file']['tmp_name'])) {
		    $this->setRedirect(
				'index.php?option='.$option.'&task=display&controller='.$oldController, JText::_('Not an HTTP upload')
			);
		    $this->redirect();
		}

		// Then we check for PHP's built-in uploading errors
		if (!($_FILES['file']['error'] == 0)) {
		    $this->setRedirect(
				'index.php?option='.$option.'&task=display&controller='.$oldController, $errors[$_FILES['file']['error']]
			);
		    $this->redirect();
		}
		
		// Split up file (respectiveley new folder) name and extension.
		list($name, $ext) = split(':', 
			preg_replace('/^(.*?)(\.(?:tar\.)?(?:gz|bz2|zip))$/', '$1:$2', JFile::makeSafe($_FILES['file']['name']))
		);

		// validation...
		// compare our file's extension with our array of allowed extensions
		if (!in_array($ext, array('.zip', '.tar.gz', '.tar.bz2'))) {
			$this->setRedirect(
				'index.php?option='.$option.'&task=display&controller='.$oldController,
				JText::_('ALLOWED_ARCHIVES')
			);
		    $this->redirect();
		}
		
		//check that we don't already have a file with the same name
		$target = $uploaddir.DS.$name;
		if (JFolder::exists($target)) {
			$this->setRedirect(
				'index.php?option='.$option.'&task=display&controller='.$oldController,
				JText::_('File').' '.$name.' '.JText::_('already exists')
			);
		    $this->redirect();
		}
		

		if(JFile::upload($_FILES['file']['tmp_name'], $target.$ext)) {
		    $this->setRedirect(
				'index.php?option='.$option.'&task=display&controller='.$oldController,
				JText::_('File').' '.$name.' '.JText::_('has been uploaded successfully')
			);
		}
		else {
		    $this->setRedirect(
				'index.php?option='.$option.'&&task=display&controller='.$oldController,
				JText::_('There was an error uploading the file').' '.$name.JText::_('please try again')
			);
		}	
		
		// And finally extract the archive the file, using the filename as the directory name	
		if ( !JArchive::extract($target.$ext, $uploaddir)) {
			$this->setRedirect(
				'index.php?option='.$option.'&task=display&controller='.$oldController,
				JText::_('Error extracting File')
			);
		    $this->redirect();
		}
		
		if (!unlink($target.$ext)) {
			$this->setRedirect(
				'index.php?option='.$option.'&task=display&controller='.$oldController,
				JText::_('Error deleting file').': '.$target.'. '.JText::_('Operation aborted')
			);
		    $this->redirect();
		}
	}	
}


?>
