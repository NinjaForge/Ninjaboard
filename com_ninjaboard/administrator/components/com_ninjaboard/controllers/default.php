<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: default.php 1762 2011-04-11 18:59:09Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Default Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerDefault extends ComNinjaControllerView
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$cache = JPATH_ROOT.'/cache/com_'.$this->getIdentifier()->package . '/maintenance.forums.txt';

		if(!JFile::exists($cache))
		{
			KFactory::get('admin::com.ninjaboard.controller.maintenance')->forums();
			JFile::write($cache, date('r'));
		}
	}

	public function setTitle()
	{
		$title = KRequest::get('post.title', 'string', 'Untitled');
		$id	   = KRequest::get('get.id', 'int', 0);
		
		$table		= KFactory::get(KFactory::get($this->getModel())->getTable());
		$primaryKey	= current($table->getPrimaryKey())->name;
		$query		= $table->getDatabase()->getQuery()->where('title', '=', $title)->where($primaryKey, '!=', $id);

		if($table->count($query))
		{
			KRequest::set('post.title', $title . ' ' . JText::_('copy'), 'string');
			if((bool) $table->count($table->getDatabase()->getQuery()->where('title', '=', KRequest::get('post.title', 'string'))->where($primaryKey, '!=', $id))) self::setTitle();
		}
		
		return $this;
	}
	
	/**
	 * Generic function for setting the permissions
	 *
	 * @return void
	 */
	public function setPermissions()
	{
		$model 		= KFactory::get($this->getModel());
		$table		= KFactory::tmp(KFactory::get(KFactory::get('admin::com.ninja.helper.access')->models->assets)->getTable());
		$query		= $table->getDatabase()->getQuery();
		$item  		= $model->getItem();
		$identifier = $this->getIdentifier();
		$id			= $identifier->type.'_'.$identifier->package.'.'.$identifier->name.'.'.$item->id.'.';
		 
		$permissions = (array) KRequest::get('post.permissions', 'int');
		$editable	 = KRequest::get('post.editpermissions', 'boolean', false);
		
		if(!$permissions && $editable)
		{
			$query->where('tbl.name', 'LIKE', $id.'%');
			$table->select($query)->delete();
			return;
		}
		
		foreach($permissions as $name => $permission)
		{
			KFactory::tmp(KFactory::get('admin::com.ninja.helper.access')->models->assets)
				->name($id.$name)
				->getItem()
				->setData(array('name' => $id.$name, 'level' => $permission))
				->save();
		}	
	}
	
	/**
	 * Raise closable hint
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param $param
	 */
	protected function _raiseClosableMessage($text)
	{
		$id		= md5($text);
		$close	= KFactory::get('admin::com.ninja.helper.default')->formid('close-'.$id);
		$cookie	= KFactory::get('admin::com.ninja.helper.default')->formid('show-'.$id);

		if(!KRequest::has('cookie.'.$cookie))
		{
			KFactory::get('lib.koowa.application')->enqueueMessage( 
				JText::_($text).
				' <a href="#" id="' . $close . '">[' .
					JText::_('close') .
				']</a>'
			);
			KFactory::get('admin::com.ninja.helper.default')->js(
				"window.addEvent('domready', function(){
					$('$close').addEvent('click', function(event){
						(new Event(event)).stop();
						var parent = this.getParent('li');
						if(!parent.getSiblings().length) {
							parent = parent.getParent().getParent();
						}
						parent.dissolve({onComplete: function(){
							this.element.remove();
						}});
						Cookie.write('$cookie', true, {duration: 365});
					});
				});"
			);
			
			KFactory::get('admin::com.ninja.helper.default')->css("
			#$close {
				display: block;
				color: inherit;
				float: right;
				margin-right: 5px;
			}");
		}
	}
}