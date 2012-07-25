<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Profile Field Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerProfile_field extends NinjaControllerDefault
{
	/**
	 * Holds the column names being changed, false if it's unchanged
	 *
	 * @var boolean|array
	 */
	protected $_column_names = false;

	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		$this->registerCallback('after.add', array($this, 'addColumn'));
		$this->registerCallback('before.edit', array($this, 'setColumnName'));
		$this->registerCallback('after.edit', array($this, 'changeColumn'));
		$this->registerCallback('after.delete', array($this, 'dropColumn'));
	}

	/**
	 * Generates column queries used by addColumn and changeColumn
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	protected function _createColumnQuery()
	{
	
	}

	/**
	 * Function for adding a column
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	public function addColumn(KCommandContext $context)
	{
		$field		= $context->result;
		$database	= $this->getModel()->getTable()->getDatabase();
		$parts			= array(
							'ALTER TABLE `#__',
			'table'		=>	'ninjaboard_people',
							'` ADD `custom_',
			'column'	=>	$field->name,
							"` TEXT NOT NULL DEFAULT '' AFTER `signature`;"
			
		);

		$database->execute(implode($parts));
	}

	/**
	 * Sets the old name of the columns before edit
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	public function setColumnName(KCommandContext $context)
	{
		$rows = $this->getModel()->limit(0)->getList();
		foreach($rows as $row)
		{
			$this->_column_names[$row->id] = $row->name;
		}
	}

	/**
	 * Function for changing a column
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	public function changeColumn(KCommandContext $context)
	{
		$fields		= $context->result;
		$database	= $this->getModel()->getTable()->getDatabase();
		$parts		= array(
							'ALTER TABLE `#__',
							'ninjaboard_people',
							'` CHANGE `custom_',
			'column'	=>	false,
							'` `custom_',
			'name'		=>	false,
							"` TEXT NOT NULL DEFAULT '';"
		);
		
		foreach($fields as $field)
		{
			if(!isset($this->_column_names[$field->id])) continue;

			$name = $this->_column_names[$field->id];
			if($name == $field->name) continue;

			$parts['column']	= $name;
			$parts['name']		= $field->name;
			$database->execute(implode($parts));
		}
	}

	/**
	 * Function for droppimg a column
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	public function dropColumn(KCommandContext $context)
	{
		$fields		= $context->result;
		$database	= $this->getModel()->getTable()->getDatabase();
		$parts		= array(
							'ALTER TABLE `#__',
							'ninjaboard_people',
							'` DROP `custom_',
			'column'	=>	false,
							'`;'
		);
		
		foreach($fields as $field)
		{
			$parts['column'] = $field->name;
			$database->execute(implode($parts));
		}
	}
}