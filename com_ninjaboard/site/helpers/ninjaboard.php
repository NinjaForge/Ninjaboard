<?php
/**
 * @version $Id: ninjaboard.php 959 2010-09-21 14:33:17Z stian $
 * @package NinjaForge.Ninjaboard
 * @copyright Copyright (C) 2007-2009 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Helper
 *
 * @package Ninjaboard
 */
class NinjaboardHelper
{

	/**
	 * get a ninjaboard user object
	 */
	function &getNinjaboardUser()
	{
		// if there is a userid in the session, load the application user object with the logged in user.
		$user = &JFactory::getUser();
		
		$userId = 0;

		if (is_object($user))
			$userId = (int)$user->get('id');
		
		$instance =& NinjaboardUser::getInstance($userId);

		return $instance;
	}
	
	/**
	 * check if a ninjaboard user is logged in
	 */	
	function isUserLoggedIn()
	{
		$ninjaboardUser =& NinjaboardHelper::getNinjaboardUser();

	    return $ninjaboardUser->get('id') ? true : false;
	}
	
	/**
	 * get a NinjaboardEditor object
	 *
	 * @access public
	 * @return object NinjaboardEditor object
	 */
	/* TODO - delete this
	 * function &getEditor() {
		jimport('joomla.html.editor');

		// get the editor configuration setting
		$nbConfig =& NinjaboardConfig::getInstance();
		$editor   =  $nbConfig->getEditor();

		$instance =& NinjaboardEditor::getInstance($editor);

		return $instance;
	}*/

	function getStyleSheet($live = false)
	{
		$ninjaboardConfig =& NinjaboardConfig::getInstance();
		
		// get style name
		$style = basename($ninjaboardConfig->getStyleFile(), ".xml");
			
		if ($live)
		{
			$ds = '/'; $styleSheetPath = NB_STYLES_LIVE;
		}
		else
		{
			$ds = DS; $styleSheetPath = NB_STYLES;
		}
		
		return $styleSheetPath.$ds.$style.$ds.$style.'.css';
	}
		
	function getStyleSheetPath($live = false)
	{
		$ninjaboardConfig =& NinjaboardConfig::getInstance();

		if ($live)
		{
			$ds = '/'; $styleSheetPath = NB_STYLES_LIVE;
		}
		else
		{
			$ds = DS; $styleSheetPath = NB_STYLES;
		}
				
		return $styleSheetPath.$ds.basename($ninjaboardConfig->getStyleFile(), ".xml");
	}
	
	function getTemplatePath($live = false)
	{
		$ninjaboardConfig =& NinjaboardConfig::getInstance();

		if ($live)
		{
			$ds = '/'; $templatePath = NB_TEMPLATES_LIVE;
		}
		else
		{
			$ds = DS; $templatePath = NB_TEMPLATES;
		}

		return $templatePath.$ds.basename($ninjaboardConfig->getTemplateFile(), ".xml");
	}
	
	function getAction()
	{
		static $action;
		
		$ninjaboardConfig =& NinjaboardConfig::getInstance();
		
		if (!isset($action))
		{
			switch (JRequest::getVar('view'))
			{
				case 'board':
					$action = JText::_($ninjaboardConfig->getBoardSettings('breadcrumb_index'));
					break;
				case 'editpost':
					$action = JText::_('NB_EDITPOST');
					break;
				case 'editprofile':
					$action = JText::_('NB_EDITPROFILE');
					break;
				case 'edittopic':
					$action = JText::_('NB_EDITTOPIC');
					break;
				case 'forum':
					$forum =& JTable::getInstance('NinjaboardForum');
					$forum->load((int)JRequest::getVar('forum', 0, '', 'int'));
					$action = JText::_('NB_VIEWINGFORUM') . JText::_(' '. $forum->name);
					break;
				case 'information':
					$action = JText::_('NB_INFORMATION');
					break;
				case 'register':
					$action = JText::_('NB_REGISTER');
					break;
				case 'requestlogin':
					$action = JText::_('NB_REQUESTLOGIN');
					break;
				case 'resetlogin':
					$action = JText::_('NB_RESETLOGIN');
					break;
				case 'search':
					$action = JText::_('NB_SEARCH');
					break;
				case 'terms':
					$action = JText::_('NB_TERMS');
					break;
				case 'topic':
					$db =& JFactory::getDBO();
					$query = "SELECT p.subject"
							. "\n FROM #__ninjaboard_topics AS t"
							. "\n INNER JOIN #__ninjaboard_posts AS p ON p.id = t.id_first_post"
							. "\n WHERE t.id = ". (int)JRequest::getVar('topic', 0, '', 'int');
							;
					$db->setQuery($query);
					$action = JText::_('NB_VIEWINGTOPIC') . JText::_(' '. $db->loadResult());
					break;
				case 'whosonline':
					$action = JText::_('NB_WHOSONLINE');
					break;
				case 'userposts':
					$ninjaboardUserPosts	=& NinjaboardUser::getInstance(JRequest::getVar('id', 0, '', 'int'));
					$action = JText::sprintf('NB_POSTSFROMUSER', $ninjaboardUserPosts->get('name'));
					break;
				case 'profile':
					$ninjaboardUserProfile =& NinjaboardUser::getInstance(JRequest::getVar('id', 0, '', 'int'));
					$action = JText::sprintf('NB_VIEWINGPROFILEFROM', $ninjaboardUserProfile->get('name'));
					break;
				default:
					$action = JText::_('NB_UNKNOWNACTION');
			}		
		}
		return $action;
	}
						
	function Date($date)
	{
		jimport('joomla.utilities.date');
		
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		
		$instance = new JDate($date);
		$timeFormat = '';

		if ($ninjaboardUser->get('id') && $ninjaboardUser->get('time_format'))
		{
			$instance->setOffset($ninjaboardUser->get('time_zone'));

			$timeFormat = $ninjaboardUser->get('time_format');
		}
		else
		{
			$instance->setOffset($ninjaboardConfig->getTimeZoneOffset());

			$timeFormat = $ninjaboardConfig->getTimeFormat();
		}
		return $instance->toFormat($timeFormat);
	}

	function formatDate($date, $timeformat, $timeZoneOffset=0)
	{
		jimport('joomla.utilities.date');

		$instance = new JDate($date);

		$instance->setOffset($timeZoneOffset);
		
		return $instance->toFormat($timeformat);
	}
						
	function getTimeZoneName()
	{
		// initialize variables
		$db			=& JFactory::getDBO();
		$nbConfig	=& NinjaboardConfig::getInstance();
		$nbUser		=& NinjaboardHelper::getNinjaboardUser();

		if ($nbUser->get('id')) {			
			$query = "SELECT z.name"
					. "\n FROM #__ninjaboard_timezones AS z"
					. "\n WHERE z.offset = ". $nbUser->get('time_zone')
					;
			$db->setQuery($query);
			$timeZoneName = $db->loadResult();
			
			if (!$timeZoneName) {
				$timeZoneName = $nbConfig->getTimeZoneName();
			}
		} else {
			$timeZoneName = $nbConfig->getTimeZoneName();
		}
		
		return $timeZoneName;
	}
		
	function getLocale($site = 'site')
	{
		static $ninjaboardLocale;

        $user =& JFactory::getUser();

        // Determine if the actual user is logged in ...
        if (! $user->guest)
		{
            // ... and has configured his own language!
            $locale = $user->getParam($site == 'site' ? 'language' : 'admin_language');

            if ($locale)
			    $ninjaboardLocale = $locale;
        }

		if (!isset($ninjaboardLocale))
		{
			$params   = JComponentHelper::getParams('com_languages');
			$nbLocale = $params->get($site, 'en-GB');
		}

		return $nbLocale;
	}
	
	function getLastPostIdTopic($topicId)
	{
		$db	=& JFactory::getDBO();
		
		$db->setQuery('
			SELECT MAX(p.id)
			FROM #__ninjaboard_posts AS p
			WHERE p.id_topic = '.$topicId.'
			GROUP BY p.id_topic
		');
		
		return $db->loadResult();
	}
	
	function getLastPostIdForum($forumId)
	{
		$db	=& JFactory::getDBO();
		
		$db->setQuery('
			SELECT MAX(p.id)
			FROM #__ninjaboard_posts AS p
			WHERE p.id_forum = '.$forumId.'
			GROUP BY p.id_forum"
		');
		
		return $db->loadResult();
	}
	
	function getLastPostTimeByIP($ip)
	{
		$db	=& JFactory::getDBO();
		
		$db->setQuery('
			SELECT MAX(p.date_post)
			FROM #__ninjaboard_posts AS p
			WHERE p.ip_poster = '.$db->Quote($ip).'
			GROUP BY p.ip_poster
		');
		
		return $db->loadResult();
	}
	
	function createElement($field, $fieldvalue = '0')
	{
		$inputClass = 'class="nbInputBox'. $field->required ? ' required"' : '"';
		$disabled   = $field->disabled ? 'disabled="disabled"' : '';
				
		switch ($field->element)
		{
			case '0':	# TextBox			
				$field->element =
					'<input type="text" name="'.$field->name.'" id="'.$field->name.'" '.$inputClass.' size="'.$field->length.

					'" value="'.$fieldvalue.'" maxlength="'.$field->size.'" '.$disabled.' />';
				break;

			case '1':	# TextArea		
				$field->element = 
					'<textarea name="'.$field->name.'" rows="'.$field->rows.'" cols="'.$field->columns.

					'" id="'.$field->name.'" class="nbInputBox" '.$disabled.'>'.$fieldvalue.'</textarea>';
				break;				

			case '2':	# CheckBoxes (not implemented yet)
				break;

			case '3':	# RadioButtons

				$field->element =
					JHTML::_('select.radiolist', 
						NinjaboardHelper::_getProfileFieldListOptions($field->id_profile_field_list),
						$field->name,
						'class="nbInputBox" '.$disabled,
						'value',
						'name',
						$field->value
					);
				break;

			case '4':	# ListBox

				$field->element =
					JHTML::_('select.genericlist',
						NinjaboardHelper::_getProfileFieldListOptions($field->id_profile_field_list),
						$field->name,
						'class="nbInputBox" size="'.$field->size.'" '.$disabled,
						'value',
						'name',
						$field->value
					);
				break;

			case '5':	# ComboBox	

				$field->element =
					JHTML::_('select.genericlist',
						NinjaboardHelper::_getProfileFieldListOptions($field->id_profile_field_list),
						$field->name,
						'class="nbInputBox" size="1" '.$disabled,
						'value',
						'name',
						$field->value
					);
				break;															

			default:
		}			
		return $field;
	}	
	
	function _getProfileFieldListOptions($idProfileFieldList)
	{
		$db	=& JFactory::getDBO();
			
		// Get profile field list values		

		$db->setQuery('
			SELECT v.*
			FROM #__ninjaboard_profiles_fields_lists_values AS v
			WHERE v.id_profile_field_list = '.$idProfileFieldList.'
			AND v.published = 1
			ORDER BY v.ordering
		');
		return $db->loadObjectList();
	}

	function setDefaultConfig($id_config)
	{
		$db		=& JFactory::getDBO();
		$syslog	=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		
		// set selected config to true

		$db->setQuery('
			UPDATE #__ninjaboard_configs
			SET default_config = 1
			WHERE id = '. $id_config
		);
		
		if ($db->query())
		{
			// set all other configs to false

			$db->setQuery('
				UPDATE #__ninjaboard_configs
				SET default_config = 0
				WHERE id <> '. $id_config
			);		

			if (!$db->query())
			{
				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				JError::raiseError(1001, JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()));
			}
		}
		else
		{
			$syslog->write($db->getErrorMsg(), 'error', __LINE__);

			JError::raiseError(1001, JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()));
		}				
	}

	function setDefaultDesign($id_design)
	{
		$db		=& JFactory::getDBO();
		$syslog	=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
			
		// set default design for to true

		$db->setQuery('
			UPDATE #__ninjaboard_designs
			SET default_design = 1
			WHERE id = '. $id_design
		);

		if ($db->query())
		{
			// set all other designs to false

			$db->setQuery('
				UPDATE #__ninjaboard_designs
				SET default_design = 0
				WHERE id <> '. $id_design
			);
			
			if (!$db->query())
			{
				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				JError::raiseError(1001, JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()));
			}
		}
		else
		{
			$syslog->write($db->getErrorMsg(), 'error', __LINE__);

			JError::raiseError(1001, JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()));
		}		
	}

	function setDefaultTemplate($templateFile)
	{
		$db		=& JFactory::getDBO();
		$syslog	=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
			
		// set default design for to true

		$db->setQuery('
			UPDATE #__ninjaboard_designs
			SET template = '.$db->Quote($templateFile).'
			WHERE default_design = 1
		');

		if (!$db->query())
		{
			$syslog->write($db->getErrorMsg(), 'error', __LINE__);

			JError::raiseError(1001, JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()));
		}
	}

	function setDefaultStyle($styleFile)
	{
		$db		=& JFactory::getDBO();
		$syslog	=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
			
		// set default style for to true

		$db->setQuery('
			UPDATE #__ninjaboard_designs
			SET style = '.$db->Quote($styleFile).'
			WHERE default_design = 1
		');

		if (!$db->query())
		{
			$syslog->write($db->getErrorMsg(), 'error', __LINE__);

			JError::raiseError(1001, JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()));
		}
	}

	function setDefaultEmoticonSet($emoticonSetFile)
	{
		$db		=& JFactory::getDBO();
		$syslog	=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
			
		// set default emoticon set to true

		$db->setQuery('
			UPDATE #__ninjaboard_designs
			SET emoticon_set = '.$db->Quote($emoticonSetFile).'
			WHERE default_design = 1
		');

		if (!$db->query())
		{
			$syslog->write($db->getErrorMsg(), 'error', __LINE__);

			JError::raiseError(1001, JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()));
		}
	}

	function setDefaultButtonSet($buttonSetFile)
	{
		$db		=& JFactory::getDBO();
		$syslog	=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
			
		// set default button set to true

		$db->setQuery('
			UPDATE #__ninjaboard_designs
			SET button_set = '.$db->Quote($buttonSetFile).'
			WHERE default_design = 1
		');

		if (!$db->query())
		{
			$syslog->write($db->getErrorMsg(), 'error', __LINE__);

			JError::raiseError(1001, JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()));
		}
	}

	function setDefaultIconSet($iconSetFile)
	{
		$db		=& JFactory::getDBO();
		$syslog	=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
			
		// set default icon set to true

		$db->setQuery('
			UPDATE #__ninjaboard_designs
			SET icon_set = '.$db->Quote($iconSetFile).'
			WHERE default_design = 1
		');

		if (!$db->query())
		{
			$syslog->write($db->getErrorMsg(), 'error', __LINE__);

			JError::raiseError(1001, JText::sprintf('NB_ERROR_SQL', $db->getErrorNum()));
		}
	}

	function checkInField(&$field, $id)
	{
		$db		=& JFactory::getDBO();
		$app	=& JFactory::getApplication();
		$old	=& JTable::getInstance('NinjaboardProfileField');
		$syslog	=& NSyslogHelper::getInstance('ninjaboard.syslog', __METHOD__);
		
		switch ($field->element)
		{
			case '0':	// TextBox
				$fieldtype = $field->type == 'varchar' || $field->type == 'integer' ?  $field->type.'('.$field->size.')' : $field->type;			
				break;

			case '1':	// TextArea
				$field->type	= 'text';
				$fieldtype		= $field->type;		
				break;

			case '2':	// CheckBox
			case '3':	// RadioButton
			case '4':	// ListBox
			case '5':	// ComboBox												
			default:
				$field->size	= 3;
				$field->type	= 'integer';
				$fieldtype		= $field->type.'('.$field->size.')';
		}			
		
		$old->load($id);
								
		// check in the field
		$db->setQuery(
			'ALTER TABLE #__ninjaboard_profiles CHANGE '.$old->name.' '.$field->name.' '.$fieldtype
		);

		if (!$db->query())
		{
			$db->setQuery(
				'ALTER TABLE #__ninjaboard_profiles ADD '.$field->name.' '.$fieldtype
			);

			if (!$db->query())
			{
				$syslog->write($db->getErrorMsg(), 'error', __LINE__);

				$app->redirect(
					'index.php?option=com_ninjaboard&task=ninjaboard_profilefield_edit&cid[]='. $id .'&hidemainmenu=1',
					JText::sprintf('NB_ERROR_SQL', $db->getErrorNum())
				);

				return false;
			}
		}
	}
				
	function parseXMLFile($folderName, $fileName, $type)
	{
		$xml = JApplicationHelper::parseXMLInstallFile($folderName.DS.$fileName);

		if ($xml['type'] != $type)
			return false;

		$data = new StdClass();
		
		foreach($xml as $key => $value)
			$data->$key = $value;
		
		$data->checked_out	= 0;
		$data->directory	= $folderName;
		$data->file_name	= $fileName;

		return $data;	
	}

	function &getPostPreview(&$post)
	{
		// initialize variables		
		$ninjaboardConfig	=& NinjaboardConfig::getInstance();
		$ninjaboardUser		=& NinjaboardHelper::getNinjaboardUser();
		$ninjaboardEngine	=& NinjaboardEngine::getInstance();
		$ninjaboardIconSet 	=& NinjaboardIconSet::getInstance($ninjaboardConfig->getIconSetFile());
				
		// duplicate the object so we do not manipulate original data
		$postPreview = clone $post;
		
		$ninjaboardEngine->convertToHtml($postPreview);
			
		$postPreview->author	= $ninjaboardUser->get('name');
		$postPreview->postDate	= NinjaboardHelper::Date($post->date_post);
		
		// post icon
		$postPreview->postIcon = $ninjaboardIconSet->iconByFunction[$postPreview->icon_function];
	
		return $postPreview;
	}
	
	function getForumURL($forumId)
	{
		$forum =& JTable::getInstance('NinjaboardForum');

		$forum->load($forumId);
		
		$forumURL = $forum->id .'-'. JFilterOutput::stringURLSafe(str_replace(" ", "", $forum->name));

		return $forumURL;
	}
	
	function getTopicURL($topicId)
	{
		$topic		=& JTable::getInstance('NinjaboardTopic');
		$firstPost	=& JTable::getInstance('NinjaboardPost');

		$topic->load($topicId);
		$firstPost->load($topic->id_first_post);
		
		$topicURL = $topic->id .'-'. JFilterOutput::stringURLSafe(str_replace(" ", "", $firstPost->subject));

		return $topicURL;
	}
	
	function saveAttachment($postId, $userId, $index) {
		
	// first let's set some variables		
	// make a note of the directory that will recieve any uploaded files
		$uploadsDirectory = JPATH_COMPONENT.DS.'attachments';
	
	//Almost all of our redirects will use the same path so to make things more readable
	//and increase maintainability lets put it in a variable isntead of writing it every time	
		$redirectPath = 'index.php?option=com_ninjaboard&view=board';		
		
	// Check whcih file types are set to be valid uploads in the config
		$ninjaboardConfig =& NinjaboardConfig::getInstance();
		$validExtns = explode(',',$ninjaboardConfig->getAttachmentSettings('attach_file_types'));
		
	// Now let's deal with the upload					
	// possible PHP upload errors
		$errors = array(1 => JText::_('PHP ini max file size exceeded'),
		                2 => JText::_('HTML form max file size exceeded'),
		                3 => JText::_('File upload was only partial'),
		                4 => JText::_('No file was attached')); 
		
	// check for PHP's built-in uploading errors
		if (!($_FILES['attachmentList']['error'][$index] == 0)){
		    $this->setRedirect($redirectPath, $errors[$_FILES['attachmentList']['error'][$index]]);
		    $this->redirect();
		}
		
	// check that the file we are working on really was the subject of an HTTP upload
	
		$tmp_file_path = $_FILES['attachmentList']['tmp_name'][$index];
		if (!(@is_uploaded_file($tmp_file_path))){
		    $this->setRedirect($redirectPath, JText::_('Not an HTTP upload'));
		    $this->redirect();
		}
		
	//Do some filename cleaning and preparing.
	//To prevent duplicate file names, and also orphaned files, we will append a date time stamp onto the files
	//and then store the file name to make them traceable and unique
		$timestamp = date("YmdHis");
    
	//Clean the filename
		$realFileName = self::cleanFileName( $_FILES['attachmentList']['name'][$index] );
        $newFileName = $timestamp.$realFileName;
            
	// validation... since we only need specific types of files
	// we should run a check to prevent wrong file types from being uploaded
		$fileExt      = substr($newFileName, (strrpos($newFileName, '.')+1));	
	
	//compare our file's extension with our array of allowed extensions
		if (!in_array($fileExt, $validExtns )){
			$this->setRedirect($redirectPath,JText::printf('Invalid attachment file type. Valid filetypes are:',$ninjaboardConfig->getAttachmentSettings('attach_file_types')));
		    $this->redirect();
		}
		
	//check that we don't already have a file with the same name
		if (file_exists($uploadsDirectory.DS.basename($newFileName)))
		{
			
		//delete the existing file so we can upload the new one
			if (!unlink($uploadsDirectory.DS.basename($newFileName)))
			{
				$this->setRedirect($redirectPath, JText::printf('Error deleting file. Operation aborted',$newFileName));
				$this->redirect();
			}
		}
		
	//is the dir writable??
		if ( !is_writable( $uploadsDirectory ) ) {
			$this->setRedirect($redirectPath, JText::_('Upload permission error. Please check the permissions on the uploads directory'));
		    $this->redirect();
		}
    
		$target_path = $uploadsDirectory.DS.$newFileName; 

		//only do something if our file upload failed
		if(!move_uploaded_file($_FILES['attachmentList']['tmp_name'][$index], $target_path)) {
		    $this->setRedirect($redirectPath, JText::printf('There was an error uploading the file. Please try again',$realFileName));
		    $this->redirect();
		}
		
		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
	//if our move was ultimately successful, save the filename and post details 
		$row =& JTable::getInstance('ninjaboardattachments', 'Table');
	
		$row->id_user = $userId;
		$row->id_post = $postId;
		$row->file_name = $newFileName;
				 				 
		 if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError() );
		}
				
		
	}
	
	function cleanFileName( $filename="" ) {
    
	    $filename = strtolower( trim($filename));
	    
	    $filename = urldecode( $filename);
	    
	    $pat[] = '/[\x21-\x2d]/u';
	    $pat[] = '/[\x5b-\x60]/u';
	    $pat[] = '/[\x7b-\xff]/u';
	    $space_char = "_";
	
	    $filename = preg_replace( $pat, $space_char, $filename );    
	    
	    $filename = ereg_replace("_+", "_", $filename);
		$filename = ereg_replace(" +", "_", $filename);
	    $filename = ereg_replace("(^_|_$)", "", $filename);
	    
	    return ($filename);
	}
	
	function removeAttachment($attachID) {
	// first let's set some variables		
	// make a note of the directory that will recieve any uploaded files
		$uploadsDirectory = JPATH_COMPONENT.DS.'attachments';
	
	//Almost all of our redirects will use the same path so to make things more readable
	//and increase maintainability lets put it in a variable isntead of writing it every time	
		$redirectPath = 'index.php?option=com_ninjaboard&view=board';
		
		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
		$row =& JTable::getInstance('ninjaboardattachments', 'Table');
		
		$row->load($attachID);
		
		$fileName = $row->file_name;
	
	//delete the existing file so we can upload the new one
		if (!unlink($uploadsDirectory.DS.$fileName))
		{
			$this->setRedirect($redirectPath, JText::printf('Error deleting file. Operation aborted',$newFileName));
			$this->redirect();
		}
	
	//if our file removal was successful, remove the details from the table 		
		if (!$row->delete($attachID)) {
				JError::raiseError(500, $row->getError() );
		}			
	}
	
}
?>
