<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category    Ninjaboard
 * @copyright   Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://ninjaforge.com
 */

/**
 * Ninjaboard Verifiable Controller Behavior
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerBehaviorVerifiable extends NinjaControllerBehaviorSpammable 
{
	/**
	 * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config) {

        $config->append(array(
            'validate_fields' => array('subject', 'text'),
            'checks'		  => array('data')
            ));

        parent::_initialize($config);
    }

    /**
     * Run the various checks before editing
     *
     * @param	KCommandContext	The context of the event
     */
    protected function _beforeEdit(KCommandContext $context)
    {
    	return parent::_beforeAdd($context);
    }

    /**
     * Overidden validate data check as NB needs a little more info
     *
     * @param  array An optional configuration array.
     * @return boolean True if validation failed, false otherwise.
     */
    protected function _dataCheck($config = array())
    {
        $config 	= new KConfig($config);
        $request 	= $this->getRequest();

        foreach ($this->_validate_fields as $field) {
    		//if we have a topic id then we are editing
    		if ($config->data->ninjaboard_topic_id && $field == 'subject') {
    			$topic = $this->getService('com://site/ninjaboard.model.topics')->id($config->data->ninjaboard_topic_id)->getItem();
    			// if we are the first post and there is no subject then we failed
    			if ($topic->first_post_id === $request->id && !$config->data->subject) 
    				$this->_invalid_data[] = $field;
    		} else {
    			if (!$config->data->{$field}) $this->_invalid_data[] = $field;
        	}
        }

        return (empty($this->_invalid_data)) ? true : false;
    }
}