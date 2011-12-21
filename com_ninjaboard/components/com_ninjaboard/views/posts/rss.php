<?php
/**
 * @version		$Id: rss.php 1679 2011-03-24 01:24:49Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewPostsRss extends KViewAbstract
{
    protected function _initialize(KConfig $config)
    {
    	$config->append(array('mimetype' => 'application/rss+xml'));
    	
    	parent::_initialize($config);
    }
	 
	public function display()
    {	
		$items = $this->getModel()->getList();
		$root  = KRequest::url()->get(KHttpUri::PART_BASE ^ KHttpUri::PART_PATH);
		
		$xml   = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
		$xml  .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:atom="http://www.w3.org/2005/Atom">';
		$xml  .= '<channel>';
		$xml  .= '<title>Posts RSS feed</title>';
		$xml  .= '<description>RSS description</description>';
		$xml  .= '<generator>'.JURI::base().'</generator>';
			
		foreach($items as $item)
		{
			$xml .= '<item>';	
			$xml .= '<title>'.htmlspecialchars($item->title).'</title>';
			$xml .= '<link>'.$root.JRoute::_('index.php?option=com_ninjaboard&view=topic&id='.$item->ninjaboard_topic_id.'&post='.$item->id.'#p'.$item->id).'</link>';
			$xml .= '<description>'.htmlspecialchars(KFactory::get('admin::com.ninja.helper.bbcode')->parse(array('text' => $item->text))).'</description>';
			$xml .= '<guid isPermaLink="false">'.$item->uuid.'</guid>';
			$xml .= '<media:title>'.htmlspecialchars($item->title).'</media:title> ';
			$xml .= '<media:content url="'.$root.JRoute::_('index.php?option=com_ninjaboard&view=avatar&id='.$item->created_by.'&format=file').'"/>';
			$xml .= '</item>';
		}
		
		$xml .= '</channel>';
		$xml .= '</rss>';	
				
    	$this->output = $xml;
    	
    	return parent::display();
    }
}