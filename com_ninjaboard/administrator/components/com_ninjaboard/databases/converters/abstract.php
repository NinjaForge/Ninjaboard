<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: abstract.php 1604 2011-02-23 13:53:07Z betweenbrain $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

//@TODO move converters into a custom ninjaboard-converters plugin group

/**
 * ComNinjaboardDatabaseConvertersAbstract
 *
 * Just to keep things DRY.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
abstract class ComNinjaboardDatabaseConvertersAbstract extends KObject implements ComNinjaboardDatabaseConvertersInterface, KObjectIdentifiable
{
	/**
	 * The current data
	 *
	 * @var array
	 */
	public $data = array();
	
	/**
	 * If set to true, then this converter is able to run in steps
	 *
	 * @var bool
	 */
	public $splittable = false;
	
	/**
	 * Array over tructated tables to prevent tables from trunctate twice
	 *
	 * @var array
	 */
	public $trunctated = array();
	
	/**
	 * Path to form layout if this converter have one
	 *
	 * @var string|boolean
	 */
	protected $_layout = false;
	
	/**
	 * Boolean deciding wether the converter needs an UI button or not
	 *
	 * @var boolean
	 */
	public $button = true;

	/**
	 * The button HTML attributes
	 *
	 * @var array
	 */
	protected $_attributes = array('onclick' => 'return this;');

	/**
	 * The MySQLi resource connecting to Ninjaboard tables
	 *
	 * Used by converters like phpBB3 for switching between MySQL connections based on data read/write
	 *
	 * @var variable type
	 */
	protected $_ninjaboard_resource = false;
	
	/**
	 * The MySQLi resource connecting to Converter (eg phpBB3) tables
	 *
	 * Used by converters like phpBB3 for switching between MySQL connections based on data read/write
	 *
	 * @var variable type
	 */
	protected $_converter_resource = false;

	/**
	 * Get the object identifier
	 * 
	 * @return	KIdentifier	
	 * @see 	KObjectIdentifiable
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
	}

	/**
	 * Execute the convertion
	 *
	 * @param  $dbprefix string	Used by converters like phpBB3 to allow importing from multiple tables,
	 * 							 and by multiple prefixes to avoid the duplicates blocker to block'em
	 * @return $this
	 */
	public function convert($dbprefix = false)
	{
		if(!$dbprefix) $dbprefix = KFactory::get('lib.joomla.config')->getValue('dbprefix');
		$identifier = new KIdentifier('admin::com.ninjaboard.database.table.default');

		foreach($this->data as $this->name => $rows)
		{
			$identifier->name = $this->name;
			$table = KFactory::get($identifier);
			
			if(KRequest::get('post.offset', 'int', 0) < 1) $this->_truncateTable($table);
			
			foreach($rows as $row)
			{
				//Filter the data and remove unwanted columns
				$data = $table->filter($row, true);
				
				//Get the data and apply the column mappings
				$data = $table->mapColumns($data);
				
				$table->getDatabase()->insert($table->getBase(), KConfig::toData($data));
			}
		}

		return $this;
	}

	/**
	 * Checks if the converter can convert
	 *
	 * Usually a check for wether the component is installed or not
	 * Example: JComponentHelper::getComponent( 'com_kunena', true )->enabled
	 *
	 * @return boolean
	 */
	public function canConvert()
	{
		return true;
	}
	
	/**
	 * Gets the layout path if the converter has one, or false if none
	 *
	 * @return string|boolean
	 */
	public function getLayout()
	{
		return $this->_layout;
	}

	/**
	 * Gets the converter description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		$key		= strtoupper($this->getName()) . '_DESCRIPTION';
		return JText::_($key) == $key ? $this->_description : JText::_($key);
	}
	
	/**
	 * Used by converters importing bridged forums
	 *
	 * @return string|boolean
	 */
	public function getPath()
	{
		return false;
	}

	/**
	 * Gets the name of the converter
	 *
	 * Is used as an identifier for the JS and controller
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->getIdentifier()->name;
	}

	/**
	 * Gets a more descriptive name for the converter
	 *
	 * Is used for the button label, "Importing from XYZ" messages and like
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return isset($this->_title) ? $this->_title : KInflector::humanize($this->getName());
	}

	/**
	 * HTML attributes for the buttons
	 *
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}
	
	/**
	 * Truncates tables that data is being imported to
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  KDatabaseTableAbstract $table	The table being deleted
	 * @return $this
	 */
	protected function _truncateTable(KDatabaseTableAbstract $table)
	{
		$name = $table->getBase();

		//If this table have been truncated ebfore, don't truncate it again
		if(isset($this->trunctated[$name])) return $this;

		$sql = 'TRUNCATE TABLE '.$table->getDatabase()->quoteName('#__'.$name);

		//Execute the query
		$table->getDatabase()->execute($sql);
		
		//Update the trunctated array
		$this->trunctated[$name] = true;

		return $this;
	}
	
	/**
	 * Imports data
	 *
	 * Used by splittable importers
	 *
	 * @param  array $tables		Array over tables to import from
	 * @param  string $package		The identifier package
	 * @return ComNinjaboardDatabaseConvertersInterface $this
	 */
	public function importData(array $tables, $package)
	{
		$offset = KRequest::get('post.offset', 'int', false);
		foreach ( $tables as $table )
		{
			$name = $table['name'];
			$query = clone $table['query'];

			if($offset === false)
			{
				if(!isset($this->data[$name]) || $this->data[$name] == array()) {
					//$this->data[$name] = (int)KFactory::tmp('admin::com.'.$package.'.table.'.$name, $table['options'])->count(clone $query);
					$this->data[$name] = (int)KFactory::get('admin::com.default.database.table.'.$name.$table['options']['name'], $table['options'])->count(clone $query);
				} else {
					//$this->data[$name] += (int)KFactory::tmp('admin::com.'.$package.'.table.'.$name, $table['options'])->count(clone $query);
					$this->data[$name] += (int)KFactory::get('admin::com.default.database.table.'.$name.$table['options']['name'], $table['options'])->count(clone $query);
				}

				continue;
			}
			elseif ($offset !== false)
			{
				$query->limit(KRequest::get('post.limit', 'int', 1000), $offset);
			}
			
			if(!isset($this->data[$name]) || $this->data[$name] == array()) {
				$this->data[$name] = KFactory::get('admin::com.default.database.table.'.$name.$table['options']['name'], $table['options'])
										->select($query, KDatabase::FETCH_ROWSET)
										->getData();
			} else {
				$rows = KFactory::get('admin::com.default.database.table.'.$name.$table['options']['name'], $table['options'])
							->select($query, KDatabase::FETCH_ROWSET)
							->getData();

				foreach($rows as $row)
				{
					$this->data[$name][] = $row;
				}
			}
			
		}

		if($offset === false)
		{
			$total = array_reduce($this->data, 'max');
			$steps = floor($total / KRequest::get('post.limit', 'int', 1000));
			if($steps > 0) 
			{
				echo json_encode(array('splittable' => true, 'total' => $total, 'steps' => $steps));
				return false;
			}
			else
			{
				echo json_encode(array('splittable' => false));
				foreach ( $tables as $table )
				{
					$name = $table['name'];
					$query = clone $table['query'];

					if($this->data[$name] == array() || is_numeric($this->data[$name])) {
						$this->data[$name] = KFactory::get('admin::com.default.database.table.'.$name.$table['options']['name'], $table['options'])
												->select($query, KDatabase::FETCH_ROWSET)
												->getData();
					} else {
						$rows = KFactory::get('admin::com.default.database.table.'.$name.$table['options']['name'], $table['options'])
									->select($query, KDatabase::FETCH_ROWSET)
									->getData();
		
						foreach($rows as $row)
						{
							$this->data[$name][] = $row;
						}
					}
				}
			}
		}
	}
	
	public function mapClean()
	{
		$table = KFactory::get('admin::com.ninjaboard.database.table.imported_rows');
		$where = $table->getDatabase()->getQuery()
					->where('ninjaboard', '=', 0);

		$table->getDatabase()->delete($table->getBase(), $where);
	}
	
	/**
	 * Set old key for an row
	 *
	 * @return $this
	 */
	public function mapLeft()
	{
		if(!isset($this->row['id'])) return $this;
		$this->id = $this->row['id'];
		$this->row['backup_id'] = $this->row['id'];
		$this->_key_maps[$this->name][$this->id] = true;
		unset($this->row['id']);

		return $this;
	}
	
	/**
	 * Set old key for an row
	 *
	 * @return $this
	 */
	public function mapRight()
	{
		$this->_key_maps[$this->name][$this->id] = $this->row->id;

		return $this;
	}
	
	/**
	 * Set old key for an row
	 *
	 * @return $this
	 */
	public function map($map, $key)
	{
		//$this->keys   = array_keys($this->_key_maps[$this->name]);
		//$this->values =	$this->_key_maps[$this->name];
		//$this->id = array_search($this->row->id, $this->_key_maps[$this->name]);

		if(!isset($this->_key_maps[$map])) return false;
		
		if(!isset($this->_key_maps[$map][$key])) return false;

		return $this->_key_maps[$map][$key];
	}
	
	protected function updateTopicForumIds()
	{
		foreach($this->data['topics'] as $topic)
		{
			KFactory::tmp('site::com.ninjaboard.model.topics')
				->id($topic->id)
				->getItem()
				->setData(array('forum_id' => $this->map('forums', $topic->forum_id)))
				->save();
		}
	}
	
	protected function updatePostTopicIds()
	{
		foreach($this->data['posts'] as $post)
		{
			$postid = $post->backup_id;
			$topic = $this->map('topics', $post->ninjaboard_topic_id);
			$row = KFactory::tmp('site::com.ninjaboard.model.posts')
					->getItem()
					->setData(
						array_merge(
							$post->getData(),
							array(
								'ninjaboard_topic_id' => $topic,
								'forum_id'			  => KFactory::tmp('site::com.ninjaboard.model.topics')
															->id($topic)
															->getItem()
															->forum_id
							)
						)
					)
					->save();
			$this->_key_maps['posts'][$postid] = $row->id;
		}
	}
	
	protected function updatePostIds()
	{	
		foreach($this->data['topics'] as $topic)
		{
			KFactory::tmp('site::com.ninjaboard.model.topics')
				->id($topic->id)
				->getItem()
				->setData(array('first_post_id' => $this->map('posts', $topic->first_post_id)))
				->save();
		}
	}
	
	protected function updateAttachments()
	{
		foreach($this->data['attachments'] as $attachment)
		{
			KFactory::tmp('site::com.ninjaboard.model.attachments')
				->id($attachment->id)
				->getItem()
				->setData(array('post_id' => $this->map('posts', $attachment->post_id)))
				->save();
		}
	}
	
	protected function updateForumPaths()
	{
		$query = KFactory::tmp('lib.koowa.database.query')->where('parent_id', '=', 0);
		$table = KFactory::tmp('admin::com.ninjaboard.database.table.forums');
		
		
		//Set paths
		$table->getCommandChain()->disable();
		foreach($table->select($query, KDatabase::FETCH_ROWSET) as $forum)
		{
			$this->updateForumPathsRecurse($forum, $table);
		}
		$table->getCommandChain()->enable();
	}
	
	protected function updateForumPathsRecurse($forum, $table)
	{
		$query = KFactory::tmp('lib.koowa.database.query')->where('tbl.parent_id', '=', $forum->id);

		$path = $forum->path;
		foreach ($table->select($query, KDatabase::FETCH_ROWSET) as $forum)
		{
			$forum->path = $path . $forum->parent_id . '/';
			$forum->save();

			$this->updateForumPathsRecurse($forum, $table);
		}
	}
}