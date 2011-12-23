<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersPhpbb
 *
 * Imports data from phpBB.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersPhpbb extends ComNinjaboardDatabaseConvertersAbstract
{
	/**
	 * This converter is able to run in steps
	 *
	 * @var boolean
	 */
	public $splittable = true;

	/**
	 * Path to form layout if this converter have one
	 *
	 * @note	'converters' is plural as this is a layout identifier, and in that context 'converters'
	 *		 	is treated as a view. And in view identifiers, plural and singular views have different layouts
	 *
	 * @var string|boolean
	 */
	protected $_layout = 'com://admin/ninjaboard.database.converters.phpbb';
	
	/**
	 * The description
	 *
	 * @var string
	 */
	protected $_description = "The phpBB3 forum you're importing needs to be bridged with your Joomla site before you start importing.<br /><br />It also needs to be placed under the Joomla root.";

	/**
	 * Import the sample content
	 *
	 * @return $this
	 */
	public function convert()
	{
		//Connect the koowa:database to the phpBB3 database
		$this->setDatabaseConnection();

		$tables = array(
			array(
				'name' => 'attachments',
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'attach_id AS id',
								'post_msg_id AS post',
								'real_filename AS name',
								'poster_id AS joomla_user_id',
								'physical_filename AS file',
								'extension'
							))->where('post_msg_id', '!=', 0)
			),
			array(
				'name' => 'forums',
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'*',
								'forum_id AS id',
								'forum_name AS title',
								'forum_desc AS description',
								'forum_status AS locked',
								'forum_topics AS topics',
								'forum_posts AS posts',
								'forum_last_post_id AS last_post_id'
							))
			),
			//Ghosts or "shadows"
			array(
				'name' => 'topic_symlinks',
				'options' => array(
					'name' => 'topics'
				),
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'topic_id AS id',
								'topic_moved_id AS ninjaboard_topic_id',
								'forum_id AS ninjaboard_forum_id'
							))
							->where('topic_moved_id', '>', 0)
			),
			array(
				'name' => 'topics',
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'*',
								'topic_id AS id',
								'topic_title AS title',
								'icon_id AS status',
								'topic_last_post_id AS last_post_id',
								'topic_first_post_id AS first_post_id',
								'topic_replies AS replies',
								'topic_views AS hits'
							))
							->where('topic_moved_id', '=', 0)
			),
			array(
				'name' => 'posts',
				'query' => $this->getService('koowa:database.adapter.mysqli')->getQuery()
							->select(array(
								'*',
								'post_id AS id',
								'topic_id AS ninjaboard_topic_id',
								'icon_id AS status',
								'poster_id AS created_by',
								'FROM_UNIXTIME(post_time) AS created_on',
								'poster_ip AS user_ip',
								'FROM_UNIXTIME(post_edit_time) AS mofidied_on',
								'post_edit_reason AS edit_reason',
								'post_subject AS subject',
								'post_text AS text'
								//@TODO complete the slashes fix
								//"REPLACE(post_subject, '\'', ".'"\'"'.") AS subject",
							))
			)
		);

		//This returns false if the import is big enough to be done in steps.
		//So we need to stop the importing in this step, in order for it to initiate
		if($this->importData($tables, 'phpbb') === false) return $this;

		//Move over file attachments
		if(isset($this->data['attachments']))
		{
			//Get the attachments path
			$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()
																->select('config_value')
																->from('config')
																->where('config_name', '=', 'upload_path');
			$path  = $this->getService('koowa:database.adapter.mysqli')->select($query, KDatabase::FETCH_FIELD);

			foreach($this->data['attachments'] as $id => $attachment)
			{
				$from	= JPATH_ROOT.'/'.$this->getPath().'/'.$path.'/'.$attachment['file'];
				$file	= JPATH_ROOT.'/media/com_ninjaboard/attachments/'.$attachment['file'].'.'.$attachment['extension'];
				
				//Don't do anything if attachment don't exist
				if(!JFile::exists($from)) continue;
				
				JFile::copy($from, $file);
				
				$this->data['attachments'][$id]['file'] = basename($file);
			}
		}

		//Reconnect the koowa:database to the Joomla! database
		$this->resetDatabaseConnection();

		//Clear cache folder so that avatars and attachments cache are cleared
		//@TODO this should only run once
		$cache = JPATH_ROOT.'/cache/com_ninjaboard/';
		if(JFolder::exists($cache)) JFolder::delete($cache);

		parent::convert();
		
		if(isset($this->data['forums'])) $this->updateForumPaths();

		return $this;
	}
	
	/**
	 * Set the koowa:database to use phpBB3 prefix, and set the connection
	 *
	 * @return $this
	 */
	public function setDatabaseConnection()
	{
		//Save the NB resource so we can reset later
		if(!$this->_ninjaboard_resource)
		{
			$this->_ninjaboard_resource = JFactory::getDatabase()->_resource;
		}

		$path	= $this->getPath();
		$config	= JPATH_ROOT.'/'.$path.'/config.php';
		
		if(!JFile::exists($config))
		{
			throw new ComNinjaboardDatabaseConverterException(sprintf(JText::_("Couldn't find phpBB3 configuration file at: %s"), $config));
		}
		

		require $config;
		
		if(!isset($dbms, $dbhost, $dbuser, $dbpasswd, $dbname, $table_prefix))
		{
			throw new ComNinjaboardDatabaseConverterException(sprintf(JText::_("The configuration in '%s' is incomplete"), $config));
		}
		
		if(!in_array($dbms, array('mysql', 'mysqli')))
		{
			throw new ComNinjaboardDatabaseConverterException(sprintf(JText::_("'%s' is not a supported database type"), $dbms));
		}
		

		$config = array(
			'host'			=> $dbhost,
			'user'			=> $dbuser,
			'password'		=> $dbpasswd,
			'database'		=> $dbname
		);
		
		$converter_database = new JDatabaseMySQLi($config);
		$this->_converter_resource	= $converter_database->_resource;

		$this->getService('koowa:database.adapter.mysqli')
			->setConnection($this->_converter_resource)
			->setTablePrefix($table_prefix);
		
		return $this;
	}
	
	/**
	 * Change koowa:database prefix and connection to what it was before setting it
	 *
	 * @return $this
	 */
	public function resetDatabaseConnection()
	{
		$this->getService('koowa:database.adapter.mysqli')
			->setConnection($this->_ninjaboard_resource)
			->setTablePrefix(JFactory::getConfig()->getValue('dbprefix'));

		return $this;
	}
	
	/**
	 * Used by converters importing bridged forums
	 *
	 * @TODO store this value in the db in the future instead of HTML5 localStorage
	 *
	 * @return string|boolean
	 */
	public function getPath()
	{
		//Get the path from the request if it exists
		if(KRequest::has('post.path')) return KRequest::get('post.path', 'ninja:filter.path');
		
		//Get the path from RokBridge, if RokBridge is present
		$helper = JPATH_ADMINISTRATOR.'/components/com_rokbridge/helper.php';
		if(JFile::exists($helper))
		{
			JLoader::register('RokBridgeHelper', $helper);
			
			if(!method_exists('RokBridgeHelper', 'getParams')) return false;
			
			return RokBridgeHelper::getParams()->get('phpbb3_path');
		}
		
		return false;
	}

	/**
	 * Sets another label than the default "Phpbb"
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return 'phpBB3';
	}
}