<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category  Ninjaboard
 * @copyright Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://ninjaforge.com
 */

/**
 * Ninjaboard Messages model
 *
 * Model for private messages
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelMessages extends ComDefaultModelDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_state
		             ->insert('folder', 'cmd', 'inbox')
		             ->insert('unread', 'boolean')
		             ->insert('conversation_id', 'string');
	}

	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
		parent::_buildQueryJoins($query);

        if(!$this->_state->user_id)
        {
            ///*
    		$query
    		    ->join('left', 'ninjaboard_message_recipients AS recipient', 'recipient.ninjaboard_message_id = tbl.ninjaboard_message_id')
    			->join('left', 'users AS user', 'user.id = tbl.created_by OR user.id = recipient.user_id')
    			->join('left', 'ninjaboard_people AS person', 'person.ninjaboard_person_id = tbl.created_by')
    			;
    			//*/
    	}
	}

	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		parent::_buildQueryColumns($query);
		
		
		
		//Build query for the screen names
		//$this->getService('com://admin/ninjaboard.model.people')->buildScreenNameQuery($query, 'person', 'user', 'recipient');
	}

	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);
		//Build query for the screen names
		$me = $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$id = (int) $me->id;

		//Get sent messages by me
		$where[] = '(tbl.created_by = '.$id.' AND recipient.user_id != '.$id.')';
		//Get sent messages to me
		$where[] = '(tbl.created_by != '.$id.' AND recipient.user_id = '.$id.')';
		
		$query->where('('.implode(' OR ', $where).')');
	}

	/**
     * Builds a GROUP BY clause for the query
     */
    protected function _buildQueryGroup(KDatabaseQuery $query)
    {
        //$query->group('tbl');
    }
    
    /**
     * Get a list of items which represnts a  table rowset
     *
     * @return KDatabaseRowset
     */
    public function getList()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_list))
        {
            if($table = $this->getTable())
            {
                $query  = null;
                
                if(!$this->_state->isEmpty())
                {
                    $me = $this->getService('com://admin/ninjaboard.model.people')->getMe();
                    $this->_list = $table->getRowset();
                
                    // First get the inbox
                    $query = $table->getDatabase()->getQuery();
                
                    $query
                          ->select('tbl.*')
                          ->select('tbl.created_by AS conversation_id')
                          ->select('recipient.is_read AS is_read')
                          ->join('left', 'ninjaboard_message_recipients AS recipient', 'recipient.ninjaboard_message_id = tbl.ninjaboard_message_id')
                          ->join('left', 'users AS user', 'user.id = tbl.created_by')
                          ->join('left', 'ninjaboard_people AS person', 'person.ninjaboard_person_id = tbl.created_by')
                          ->where('recipient.user_id', '=', $me->id)
                          //->group('tbl.created_by')
                          ->order('tbl.created_on', 'desc');
                          
                    if($this->_state->conversation_id)
                    {
                        //Needs to be LIKE, not = as only LIKE will prevent mismatches
                        $query->where('tbl.created_by', 'LIKE', $this->_state->conversation_id);
                    }
                          
                    $this->getService('com://admin/ninjaboard.model.people')->buildScreenNameQuery($query, 'person', 'user', 'conversation_with');
                    $inbox = $table->select($query, KDatabase::FETCH_ROWSET);
                    
                    $keep = array();
                    $remove = array();
                    foreach($inbox as $message)
                    {
                        
                        if(!isset($keep[$message->conversation_id]) || $this->_state->conversation_id)
                        {
                            $message->conversation_with = (array)$message->conversation_with;
                        
                            $key = $this->_state->conversation_id ? $message->id : $message->conversation_id;
                            $keep[$key] = $message->getData();
                        }
                    }
                    
                    // Then get the outbox
                    $query    = $table->getDatabase()->getQuery();
                    $subquery = clone $query;

                    $query
                          ->select(array(
                              'tbl.*',
                              '1 AS is_read'
                          ))
                          ->where('tbl.created_by', '=', $me->id)
                          ->order('tbl.created_on', 'desc');
                    
                    $subquery->select('GROUP_CONCAT(user_id)')
                             ->from('ninjaboard_message_recipients')
                             ->where('ninjaboard_message_id = tbl.ninjaboard_message_id');
                    
                    if($this->_state->conversation_id)
                    {
                        $query
                              ->select('tbl.created_by AS conversation_id')
                              ->join('left', 'ninjaboard_message_recipients AS recipient', 'recipient.ninjaboard_message_id = tbl.ninjaboard_message_id')
                              ->where('('.
                                  $subquery
                              .')', '=', $this->_state->conversation_id);
                    }
                    else
                    {
                        $query->select('('.
                            $subquery
                        .') AS conversation_id');
                    }
                    
                    /*
                    $table = $this->getService($table->getIdentifier(), array('enable_callbacks' => true));
                    $table->registerCallback('after.select', function(KCommandContext $context){
                        die('<pre>'.print_r((string)$context->query, true));
                    });
                    //*/
                    
                    $outbox = $table->select($query, KDatabase::FETCH_ROWSET);

                    $users = $this->getService('com://admin/ninjaboard.database.table.users');
                    $query = $users->getDatabase()->getQuery();
                    $query->join('left', 'ninjaboard_people AS person', 'person.ninjaboard_person_id = tbl.id');
                    $this->getService('com://admin/ninjaboard.model.people')->buildScreenNameQuery($query, 'person', 'tbl', 'conversation_with');
                    
                    foreach($outbox as $message)
                    {
                        if(isset($keep[$message->conversation_id]) && !$this->_state->conversation_id)
                        {
                            $prev = new DateTime($keep[$message->conversation_id]['created_on']);
                            $next = new DateTime($message->created_on);
                            if($prev->format('U') >= $next->format('U')) continue;
                        }

                        $titles = clone $query;
                        $titles->where('tbl.id', 'in', explode(',', $message->conversation_id))->order('tbl.id', 'asc');

                        $message->conversation_with = $users->select($titles, KDatabase::FETCH_FIELD_LIST);

                        $key = $this->_state->conversation_id ? $message->id : $message->conversation_id;
                        $keep[$key] = $message->getData();
                    }
                    
                    usort($keep, array($this, 'sortByCreatedOn'));
                    
                    $this->_list->addData($keep, false);
                    
                    /*
                    foreach($remove as $mesage)
                    {
                        $inbox->extract($message);
                    }
                    //*/
                    
                    // Now get the outbox
                    
                    // And merge them
                    //$this->_list = $keep;
                }
                else
                {
                    $this->_list = $table->select($query, KDatabase::FETCH_ROWSET);
                }
            }
        }

        return $this->_list;
    }
    
    /**
     * Sort by date column
     *
     * @param  $a    Side A of the comparison, should be older
     * @param  $b    Side B of the comparison, should be newer
     * @return int   1 increment or decrement depending on the result
     */
    public function sortByCreatedOn($a, $b)
    {
        if($a['created_on'] == $b['created_on']) {
            return 0;
        }
        $prev = new DateTime($a['created_on']);
        $next = new DateTime($b['created_on']);
        
        return $prev->format('U') > $next->format('U') ? -1 : 1;
    }

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    public function getTotal()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_total))
        {
            if($this->_state->unread)
            {
                $me           = $this->getService('com://admin/ninjaboard.model.people')->getMe();
                $table        = $this->getService('com://admin/ninjaboard.database.table.message_recipients');
                $this->_total = $table->count(array('user_id' => $me->id, 'is_read' => 0));
            }
            else {
                $this->_total = count($this->getList());
            }
        }

        return $this->_total;
    }
}