<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaboardViewAttachmentFile extends KViewFile
{
	/**
	 * Default mimetype
	 *
	 * @var string
	 */
	public $mimetype = ' ';

	public function display()
	{
		require_once JPATH_ROOT.'/components/com_media/helpers/media.php';

		$params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$params = $params['attachment_settings'];



		$attachment = $this->getModel()->getItem();
		if(!$attachment->id) return $this->notFound();
		
		$post = $this->getService('com://admin/ninjaboard.model.posts')->id($attachment->post)->getItem();
		if(!$post->id) return $this->notFound();

		$topic = $this->getService('com://admin/ninjaboard.model.topics')->id($post->ninjaboard_topic_id)->getItem(); 
		if(!$topic->id) return $this->notFound();

		$forum = $this->getService('com://admin/ninjaboard.model.forums')->id($topic->forum_id)->getItem();
		if(!$forum->id) return $this->notFound();


		if($forum->attachment_permissions < 1) return $this->forbidden();


		$path = JPATH_ROOT.'/media/com_ninjaboard/attachments/'.$attachment->file;
		
		if(JFile::getExt($attachment->file) == 'pdf')	$this->mimetype= 'application/pdf';
		if($attachment->isImage())	{
			$this->disposition = $params['disposition'];
			$this->mimetype = 'image/'.$attachment->type;
		} else {
			$this->mimetype = 'text/'.$attachment->type;
		}
		
		$this->output = JFile::read($path);
		$this->filename = $attachment->name;

		return parent::display();
	}

	/**
	 * Displays a 403 forbidden message if the user don't have permissions to see the attachment
	 *
	 * @return boolean false
	 */
	public function forbidden()
	{
		JError::raiseError(403, JText::_("Forbidden. You don't have the permissions to see this attachment."));
		
		return false;
	}

	/**
	 * Displays a 404 not found message
	 *
	 * @return boolean false
	 */
	public function notFound()
	{
		JError::raiseError(404, JText::_('COM_NINJABOARD_ATTACHMENT_NOT_FOUND'));

		return false;
	}
}