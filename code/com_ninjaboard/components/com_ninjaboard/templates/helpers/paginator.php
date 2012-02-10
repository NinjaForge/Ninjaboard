<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardTemplateHelperPaginator extends NinjaTemplateHelperPaginator
{
	/**
	 * Template item override
	 *
	 * @var boolean
	 */
	protected $_item_override = false;
	
	/**
	 * Template list override
	 *
	 * @var boolean
	 */
	protected $_list_override = false;

	/**
	 * Method for rendering the item pagination
	 *
	 * @param	array	optional array of configuration options
	 * @return	string	Html
	 * @link  	http://developer.yahoo.com/ypatterns/navigation/pagination/
	 */
	public function pagination($config = array())
	{
	    $config = new KConfig($config);
		$config->append(array(
			'total'		 => 0,
			'display'	 => 5,
			'name'       => KRequest::get('get.view', 'cmd', 'items'),
			'offset'     => 0,
			'limit'	     => 0,
			'attribs'    => array('onchange' => 'this.form.submit();'),
			'show_limit' => true,
			'show_count' => true
		));

        $this->_initialize($config);
	
		if($config->total !== false && $config->total <= $config->limit) return false;

		$chromePath = JPATH_THEMES.DS.JFactory::getApplication()->getTemplate().DS.'html'.DS.'pagination.php';
		if (file_exists($chromePath))
		{
			require_once ($chromePath);
			if (function_exists('pagination_item_active') && function_exists('pagination_item_inactive')) {
				$this->_item_override = true;
			}
			if (function_exists('pagination_list_render')) {
				$this->_list_override = true;
			}
		}

		$params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$this->override = $this->_list_override && $this->_item_override && $params['view_settings']['pagination'] == 'core';

		if(!$this->override) $this->getService('ninja:template.helper.document')->load('/pagination.css');

		$view = $this->name;
		$items = (int) $total === 1 ? KInflector::singularize($view) : $view;
		//if($total <= 10) return '<div class="pagination"><div class="limit">'.sprintf(JText::_(KInflector::humanize($items)) . ' %s', $total ).'</div></div>';

		$list = $this->_items($config);
		$limitlist = $total > $limit ? $this->limit(array('state' => array('limit' => $limit))) : $total;
		
		$html  = '<div class="pagination">';
		//$html .= '<div class="limit">'.sprintf(JText::_(KInflector::humanize($items)) . ' %s', $total ).'</div>';
		$html .=  $this->pages($list);
		//$html .= '<div class="count"> '.JText::_('Pages').' '.$paginator->current.' '.JText::_('of').' '.$paginator->count.'</div>';
		$html .= '</div>';

		return $html;
	}
	
	/**
	 * Render a list of pages links
	 *
	 * @param	araay 	An array of page data
	 * @return	string	Html
	 */
	public function pages($pages)
	{
		$params = $this->getService('com://admin/ninjaboard.model.settings')->getParams();

		if(!$this->override) return parent::pages($pages);
		
		$list['start']		= $this->createPageItem($pages['first'], 'Start');
		$list['previous']	= $this->createPageItem($pages['previous'], 'Prev');

		foreach($pages['pages'] as $i => $page)
		{
			$list['pages'][$i] = $this->createPageItem($page, $page->page);
		}

		$list['next']	= $this->createPageItem($pages['next'], 'Next');
		$list['end']	= $this->createPageItem($pages['last'], 'End');
		
		return pagination_list_render($list);
	}
	
	public function createPageItem($page, $title)
	{
		$tmp = (object) array(
			'text' => $title,
			'base' => $page->offset,
			'link' => $this->createLink($page)
		);

		return array(
			'active'	=> $page->active,
			'data'		=> $page->active ? pagination_item_active($tmp) : pagination_item_inactive($tmp)
		);
	}
}