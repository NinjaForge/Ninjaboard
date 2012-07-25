<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
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
		
		$subject = sprintf(JText::_('COM_NINJABOARD_SENT_YOU_A_MESSAGE_ON'), $me->display_name, $sitename);
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
}