<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: message.php 2470 2011-11-01 14:22:28Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Message Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerMessage extends ComNinjaboardControllerAbstract
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

        $this->registerActionAlias('apply', 'save');

		//Register validation event
		//@TODO we shouldn't have to attach to the save and apply events. But KControllerView expects 'edit' to succeed.
		//$this->registerCallback(array('before.add', 'before.edit', 'before.save', 'before.apply'), array($this, 'validate'));
		
		$this->registerCallback('after.browse', array($this, 'setRead'));
	}
	
	public function setRead()
	{
	    $model = $this->getModel();
	    $state = $model->getState();

	    if($state->conversation_id)
	    {
	        $me    = $this->getService('com://admin/ninjaboard.model.people')->getMe();
	        $query = $this->getService('koowa:database.adapter.mysqli')->getQuery();
	        $table = $this->getService('com://admin/ninjaboard.database.table.message_recipients');

	        $query->join('left', 'ninjaboard_messages AS message', 'message.ninjaboard_message_id = tbl.ninjaboard_message_id')
	              ->where('message.created_by', 'LIKE', $state->conversation_id)
	              ->where('tbl.user_id', '=', $me->id)
	              ->where('tbl.is_read', '=', 0);

	        $rows  = $table->select($query, KDatabase::FETCH_ROWSET);
	        
	        //Mark them all as read
	        $rows->setData(array('is_read' => 1))->save();
	    }
	}

	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'persistent'	=> false,
        ));

        parent::_initialize($config);
    }
	
	/*
	 * Delete not supported yet
	 */
	protected function _actionDelete(KCommandContext $context)
	{
	    return false;
	}
	
	/*
	 * Edit not allowed
	 */
	protected function _actionEdit(KCommandContext $context)
	{
	    return false;
	}

	protected function _actionAdd(KCommandContext $context)
	{
	    $result = parent::_actionAdd($context);
	    $me     = $this->getService('com://admin/ninjaboard.model.people')->getMe();
	    $filter = $this->getService('koowa:filter.int');
	    
	    
	    
	    $params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
	    $app	= JFactory::getApplication();
	    $root	= KRequest::url()->get(KHttpUrl::BASE ^ KHttpUrl::PATH);
	    //@TODO Should link to the message directly
	    $link	= $root.JRoute::_('index.php?option=com_ninjaboard&view=messages');
	    
	    $sitename 	= $app->getCfg( 'sitename' );
		$mailfrom 	= $app->getCfg( 'mailfrom' );
		$fromname 	= $app->getCfg( 'fromname' );

		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $me->display_name;
			$mailfrom = $me->email;
		}
		
		$subject = sprintf(JText::_('%s sent you a message on %s'), $me->display_name, $sitename);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		$text    = $this->getService('ninja:helper.bbcode')->parse(array('text' => $result->text));
		
	    $recipients = explode(',', $result->to);
	    
	    foreach($recipients as $tmp)
	    {
	        $recipient = $filter->sanitize($tmp);
	        if($recipient != $tmp) continue;

	        //Can't be the current user
	        if($recipient == $me->id) continue;

	        $model = $this->getService('com://admin/ninjaboard.model.message_recipients');
	        $model->getItem()
	              ->setData(array(
	                  'ninjaboard_message_id' => $result->id,
	                  'user_id'               => $recipient
	              ))
	              ->save();

	        if($params->messaging_settings->enable_messaging)
	        {
	            $person = $this->getService('com://admin/ninjaboard.model.people')->id($recipient)->getItem();
	            if(!$person->notify_on_private_message) continue;
	            
	            $notification = str_replace('/n', "\n", JText::_( 'NOTIFY_PM' ));
	            if($notification == 'NOTIFY_PM') $notification = "%s\n\n\n\nTo manage all your messages, go to %s\n\n- %s";
	            $notification = sprintf ($notification , $text, $link, $fromname);
	            $notification = html_entity_decode($notification, ENT_QUOTES);
	            //The last parameter sets the JMail $mode to HTML instead of the default plaintext
	            JUtility::sendMail($mailfrom, $fromname, $person->email, $subject, $notification, true);
	        }
	    }
	    
	    return $result;
	}

	protected function _actionSave(KCommandContext $context)
	{
		$result = parent::_actionSave($context);

		$this->_redirect = 'index.php?option=com_ninjaboard&view=messages';

		return $result;
	}
	
	/*
	 * Generic cancel action
	 *
	 * @return 	void
	 */
	protected function _actionCancel(KCommandContext $context)
	{
		$this->_redirect = 'index.php?option=com_ninjaboard&view=messages';
	}
}