<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewForumHtml extends ComNinjaboardViewHtml
{
	public function display()
	{					
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		// path to images directory
		$identifier = $this->getModel()->getIdentifier();
		$path		= JPATH_ROOT.DS.'media'.DS.$identifier->type.'_'.$identifier->package.DS.'images'.DS.'forums';
		$filter		= '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$';
		$files		= JFolder::files($path, $filter, true, true);
		$options = array ();
		$options[] = JHTML::_('select.option',  null, JText::_('None'));
		$img[]	   = $this->mediaurl . '/napi/img/blank.gif';
		$optgroup = null;
		if ( is_array($files) )
		{
			foreach ($files as $file)
			{
				$f = basename(dirname($file));
				$options[] = JHTML::_('select.option',  basename($file), basename($file));
				$img[]	   = $this->mediaurl . '/' . $identifier->type.'_'.$identifier->package . '/images/forums/' . basename($file);
			}
		}
		
		$document = JFactory::getDocument();
		$script   = 'var ' . $identifier->name . 'Images = new Asset.images(' . json_encode((array)$img) . ', {
		    onComplete: function(){
		    	$(\'icon_preview\').empty(); ' . $identifier->name . 'Images[$(\'icon\').selectedIndex].clone().injectInside(\'icon_preview\');
		    }
		});';
		//$document->addScriptDeclaration($script);
		
		$this->assign('options', $options);
		$this->assign('attr', array('onchange' => '$(\'icon_preview\').empty(); ' . $identifier->name . 'Images[this.selectedIndex].clone().injectInside(\'icon_preview\')'));

		$forums = array(0 => (object) array(
			'title' => '- No parent -',
			'path' => '/'
		));
		$state			 = $this->getModel()->getState();
		$this->sort		 = $state->sort ? $state->sort : 'title';
		$this->direction = $state->direction;

		$list	= $this->getModel()->limit(0)->sort('path_sort_ordering')->enabled('')->getList();
		$id		= $this->getModel()->getItem()->id;
		foreach($list as $forum)
		{
			if($forum->id === $id && $id > 0) $forum->disable = true;
			$forum->path  .=  $forum->id . '/';
			$pos = strpos($forum->path, '/'.$id.'/');
			if($pos !== false && $pos >= 0 && $id > 0) $forum->disable = true;
			$forum->title = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $forum->level) . $forum->title;
			$forums[]	 = $forum->getData();
		}
		$this->assign('forums', $forums);
		$filepath = dirname($this->getIdentifier()->filepath).'/tmpl/params.xml';
		$model 	  = $this->getModel();
		$forum	  = $model->getItem();
		$this->form = $this->getService('ninja:form.parameter', array(
					  		'data' => $forum->params,
					  		'xml'  => $filepath
					  ));
					  
		$this->assign('total', $model->getTotal());
		
		$permissions = array();
		foreach($this->getService('com://admin/ninjaboard.model.user_groups')->limit(0)->getList() as $usergroup)
		{
			$names		= array();
			$objects	= $this->getService('com://admin/ninjaboard.permissions')->getObjects();
			foreach ($objects as $object) 
			{
				$names[] = 'com_ninjaboard.forum.'.$forum->id.'.'.$usergroup->id.'.'.$object;
			}
		
			$permissions[] = array(
				'form' =>	$this->getService('ninja:template.helper.access', array(
								'name'		=> $names,
								'id'		=> $this->getService('ninja:template.helper.document')->formid($forum->id.'-permissions-'.$usergroup->id),
								'inputName'	=> 'permissions['.$usergroup->id.']',
								'inputId'	=> 'permissions-'.$usergroup->id,
								'render'	=> 'usergroups',
								'objects'	=> $objects
							)),
				'title' => $usergroup->title,
				'group'	=> $usergroup->id,
				'id'	=> $this->getService('ninja:template.helper.document')->formid($forum->id.'-permissions-'.$usergroup->id)
			);
		}
		$this->assign('permissions', $permissions);
				
		return parent::display();
	}
}