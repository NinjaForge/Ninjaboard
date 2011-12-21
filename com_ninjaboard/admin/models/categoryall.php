<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class NinjaBoardModelCategoryAll extends JModel
{
	var $data = null;
	var $_pagination = null;
	var $_total = null;
	var $_search = null;
	var $_query = null;
	var $_orderBy = null;
	var $_orderByLists = null;
		
	
	function getData()
	{
		$pagination =& $this->getPagination();
		
		if (empty($this->data)) {
			$query = $this->buildSearch();
		
			$this->data = $this->_getList($query, $pagination->limitstart, $pagination->limit);

		}
		
				
		return $this->data;
	}
	
	function buildSearch()
	{
		if (!$this->_query) {
		//get oru search and order by
			$search = $this->getSearch();
			$orderby = $this->getOrderBy();
			
		//build our initial query
			$this->_query = "SELECT * FROM #__ninjaboard_categories c ";
			
		//apply any search conditions to it
			if ($search != '') {
				$fields = array('c.name', 'c.id');
				
				$where = array();
				
				$search = $this->_db->getEscaped( $search, true );
				
				foreach ($fields as $field) {
					$where[] = $field . " LIKE '%{$search}%'";
				}
				
				$this->_query .= ' WHERE ' . implode(' OR ', $where);
			}
			
		//append an orderby if there is one
			if ($orderby != ''){
				$this->_query .=' '.$orderby;
			} else {
				$this->_query .=' ORDER BY c.id DESC ';
			}
		}		
				
		return $this->_query;
	}
	
	function getTotal()
	{
		if (!$this->_total) {
			$query = $this->buildSearch();
			$this->_total = $this->_getListCount($query);
		}
		
		return $this->_total;
	}
	
	function &getPagination() 
	{
		if(!$this->_pagination) {
			jimport('joomla.html.pagination');
			global $mainframe;
			$this->_pagination = new JPagination($this->getTotal(), JRequest::getVar('limitstart', 0), JRequest::getVar('limit', $mainframe->getCfg('list_limit')));
		}
		
		return $this->_pagination;
	}
	
	function getSearch()
	{
		if (!$this->_search) {
			global $mainframe, $option;
	
			$search = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
			$this->_search = JString::strtolower($search);
		}
				
		return $this->_search;
	}
	
	function getOrderBy()
	{
		if (!$this->_orderBy) {
			global $mainframe, $option;
			
			$context			= 'com_ninjaboard.ninjaboard_category_view';
		
			$filter_order 		= $mainframe->getUserStateFromRequest("$context.filter_order", 'filter_order', 'c.ordering', 'string');
			$filter_order_Dir 	= $mainframe->getUserStateFromRequest("$context.filter_order_Dir", 'filter_order_Dir', '', 'string');
		
			$this->_orderBy = "\n ORDER BY $filter_order $filter_order_Dir";
		}
				
		return $this->_orderBy;
	}
	
	function getOrderByLists()
	{
		if (!$this->_orderByLists) {
			global $mainframe, $option;
			
			$context			= 'com_ninjaboard.ninjaboard_category_view';
		// parameter list var to pass back
			$lists = array();
				
			$lists['filter_order']		= $mainframe->getUserStateFromRequest("$context.filter_order", 'filter_order', 'c.ordering', 'string');
			$lists['filter_order_Dir'] 	= $mainframe->getUserStateFromRequest("$context.filter_order_Dir", 'filter_order_Dir', '', 'string');
		
			$this->_orderByLists = $lists;
		}
				
		return $this->_orderByLists;
	}
	
} 
?>