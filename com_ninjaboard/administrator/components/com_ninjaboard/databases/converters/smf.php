<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * ComNinjaboardDatabaseConvertersSmf
 *
 * Imports data from SMF.
 * First converter that can import users into joomlas user database
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardDatabaseConvertersSmf extends ComNinjaboardDatabaseConvertersAbstract
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
	protected $_layout = 'admin::com.ninjaboard.database.converters.smf';
	
	/**
	 * The description
	 *
	 * @var string
	 */
	protected $_description = "This converter will add users from the SMF installation to your Joomla! site that don't already exist.<br />The user synchronization algorithm match users based on their username and email adress.";

	/**
	 * Import the sample content
	 *
	 * @return $this
	 */
	public function convert()
	{
		//Connect the lib.koowa.database to the SMF database
		$this->setDatabaseConnection();

		//Get the category offset needed to avoid ID collisions when we merge smf_categories and smf_boards into ninjaboard_forums
		$database	= KFactory::get('lib.koowa.database');
		$query		= KFactory::tmp('lib.koowa.database.query')
														->select('ID_CAT AS id')
														->from('categories')
														->order('ID_CAT', 'desc');
		$forum_padding	= $database->select($query, KDatabase::FETCH_FIELD);
		
		$query		= KFactory::tmp('lib.koowa.database.query')
														->select('ID_MEMBER AS id')
														->from('members')
														->order('ID_MEMBER', 'desc');
		$member_padding	= $database->select($query, KDatabase::FETCH_FIELD);
		
		$tables = array(
			array(
				'name' => 'attachments',
				'options' => array(
					'name' => 'attachments'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'ID_ATTACH AS id',
								'ID_MSG AS post',
								'file_hash AS hash',
								'filename AS name',
								'ID_MEMBER AS joomla_user_id'
							))
							// There are only two types, 0 and 3. 3 is image thumbnails and we don't want those
							->where('attachmentType', '=', 0)
							//->order('ID_ATTACH', 'ASC')
			),
			array(
				'name' => 'forums',
				'options' => array(
					'name' => 'categories'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'ID_CAT AS id',
								'catOrder AS ordering',
								'name AS title',
								"'/' AS path"
							))
							//->order('ID_CAT', 'ASC')
			),
			array(
				'name' => 'forums',
				'options' => array(
					'name' => 'boards'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'(ID_BOARD + '.$forum_padding.') AS id',
								'boardOrder AS ordering',
								'name AS title',
								'IF(ID_PARENT > 0, (ID_PARENT + '.$forum_padding.'), ID_CAT) AS parent_id',
								'description',
								'childLevel AS level',
								'numTopics AS topics',
								'numPosts AS posts',
								'ID_LAST_MSG AS last_post_id',
								"'/' AS path"
							))
							//->order('ID_BOARD', 'ASC')
			),
			array(
				'name' => 'topics',
				'options' => array(
					'name' => 'topics'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'ID_TOPIC AS id',
								'isSticky AS sticky',
								'(ID_BOARD + '.$forum_padding.') AS forum_id',
								'ID_FIRST_MSG AS first_post_id',
								'ID_LAST_MSG AS last_post_id',
								'ID_POLL AS poll',
								'numReplies AS replies',
								'numViews AS hits',
								'locked'
							))
							//->order('ID_TOPIC', 'ASC')
			),
			array(
				'name' => 'posts',
				'options' => array(
					'name' => 'messages'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'ID_MSG AS id',
								'ID_TOPIC AS ninjaboard_topic_id',
								"FROM_UNIXTIME(posterTime) AS created_on",
								'ID_MEMBER AS created_by',
								
								//@TODO this is used to track unread messages
								//'ID_MSG_MODIFIED',
								
								'subject',
								'posterName AS guest_name',
								'posterEmail AS guest_email',
								'posterIP AS user_ip',
								
								//@TODO we should consider a specific column in our table for this param
								//'smileysEnabled',
								
								'FROM_UNIXTIME(modifiedTime) AS modified_on',

								/**
								 * SMF does not store modified_by as a user id, but user name.
								 * So we gather all the names first, then query the ids later in a single query
								 * to reduce overhead.
								 */
								'modifiedName',
								
								'body AS text',
								'icon'
							))
							//->order('ID_MSG', 'ASC')
			),
			array(
				'name' => 'messages',
				'options' => array(
					'name' => 'personal_messages'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'ID_PM AS id',
								'ID_MEMBER_FROM AS created_by',
								"FROM_UNIXTIME(msgtime) AS created_on",
								'subject',
								'body AS text',
								'deletedBySender AS deleted'
							))
							//->order('ID_PM', 'ASC')
			),
			array(
				'name' => 'message_recipients',
				'options' => array(
					'name' => 'pm_recipients'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								'ID_PM AS id',
								'ID_PM AS ninjaboard_message_id',
								'ID_MEMBER AS user_id',
								'bcc AS is_bcc',
								'is_read',
								'deleted AS is_deleted'
							))
							//->order('ID_PM', 'ASC')
			),
			array(
				'name' => 'people',
				'options' => array(
					'name' => 'members'
				),
				'query' => KFactory::tmp('lib.koowa.database.query')
							->select(array(
								//We are padding here because primary keys can't have duplicates, and duplicates happens normally on user id fields before and during the user sync stage
								'(ID_MEMBER + '.$member_padding.' + '.$member_padding.') AS id',
								//'ID_MEMBER AS id',
								'posts',
								'signature',
								'ID_MEMBER AS temporary_id',
								"LOWER('ALIAS') AS which_name",
								'memberName AS alias'
							))
							//->order('ID_MEMBER', 'ASC')
			),
		);

        //To prevent errors only add this part if the table exists
        $query = "SHOW TABLES LIKE '#__pretty_topic_urls';";
        $sef  = KFactory::get('lib.koowa.database')->select($query);
        if($sef)
        {
            $tables[] = array(
            	'name' => 'topic_slugs',
            	'options' => array(
            		'name' => 'pretty_topic_urls'
            	),
            	'query' => KFactory::tmp('lib.koowa.database.query')
            				->select(array(
            					'ID_TOPIC AS id',
            					'ID_TOPIC AS ninjaboard_topic_id',
            					'pretty_url AS ninjaboard_topic_slug',
            				))
            );
        }
        

		//This returns false if the import is big enough to be done in steps.
		//So we need to stop the importing in this step, in order for it to initiate
		if($this->importData($tables, 'smf_converter') === false) return $this;

		//Turn <br /> into real linebreaks
		if(isset($this->data['posts']))
		{
			$utc		= new DateTimeZone('UTC');
			$timezone	= new DateTimeZone(date('e'));
			foreach($this->data['posts'] as $id => $post)
			{
				//Since Ninjaboard stores all dates as GMT, imported datetime values needs to be adjusted
				$created_on								= new DateTime($this->data['posts'][$id]['created_on'], $timezone);
				$created_on->setTimezone($utc);
				$this->data['posts'][$id]['created_on']	= $created_on->format('Y-m-d H:i:s');
				$modified_on							= new DateTime($this->data['posts'][$id]['modified_on'], $timezone);
				$modified_on->setTimezone($utc);
				$this->data['posts'][$id]['modified_on']= $modified_on->format('Y-m-d H:i:s');

				$this->data['posts'][$id]['text']		= str_replace(array('&nbsp;', '<br />', '&#38;#'), array(' ', "\n", '&#'), $post['text']);
			}
		}
		
		//Private messages also needs to adjust datetime offsets
		if(isset($this->data['messages']))
		{
			$utc		= new DateTimeZone('UTC');
			$timezone	= new DateTimeZone(date('e'));
			foreach($this->data['messages'] as $id => $message)
			{
				//Since Ninjaboard stores all dates as GMT, imported datetime values needs to be adjusted
				$created_on								= new DateTime($this->data['messages'][$id]['created_on'], $timezone);
				$created_on->setTimezone($utc);
				$this->data['messages'][$id]['created_on']	= $created_on->format('Y-m-d H:i:s');
				
				$this->data['messages'][$id]['text']		= str_replace(array('&nbsp;', '<br />', '&#38;#'), array(' ', "\n", '&#'), $message['text']);
			}
		}

		//Undo the slashes and html special chars that SMF does on all input
		foreach($this->data as $name => $table)
		{
			foreach($table as $id => $row)
			{
				foreach($row as $key => $value)
				{
					if(!is_string($value)) continue;
					$this->data[$name][$id][$key] = stripslashes(htmlspecialchars_decode($value, ENT_QUOTES));
				}
			}
		}
		
		//Convert html 2 bbcode where needed
		if(isset($this->data['forums']))
		{
			foreach($this->data['forums'] as $id => $forum)
			{
				if(!isset($this->data['forums'][$id]['description'])) continue;
				$this->data['forums'][$id]['description'] = html2bbcode($forum['description']);
			}
		}
		if(isset($this->data['messages']))
		{
			foreach($this->data['messages'] as $id => $message)
			{
				if(!isset($this->data['messages'][$id]['text'])) continue;
				$this->data['messages'][$id]['text'] = html2bbcode($message['text']);
			}
		}
		
		//Fix line breaks in signatures
		if(isset($this->data['people']))
		{
			foreach($this->data['people'] as $id => $person)
			{
				if(!isset($this->data['people'][$id]['signature'])) continue;
				$this->data['people'][$id]['signature'] = str_replace(array('<br />', '&#38;#'), array("\n", '&#'), $person['signature']);
			}
		}

		//Move over file attachments
		if(isset($this->data['attachments']))
		{
			//Get the attachments path
			$query = KFactory::tmp('lib.koowa.database.query')
																->select('value')
																->from('settings')
																->where('variable', '=', 'attachmentUploadDir');
			$path  = KFactory::get('lib.koowa.database')->select($query, KDatabase::FETCH_FIELD);

			foreach($this->data['attachments'] as $id => $attachment)
			{
				//SMF have two ways of storing attachments
				if($attachment['hash'])
				{
					$file = $id.'_'.$attachment['hash'];
				}
				else
				{
					$file = $attachment['name'];

					/** 
					 * The following code is from getLegacyAttachmentFilename defined in SMF/Sources/Sub.php
					 */

					// Remove special accented characters - ie. sí.
					$clean_name = strtr($file, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
					$clean_name = strtr($clean_name, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));

					// Sorry, no spaces, dots, or anything else but letters allowed.
					$clean_name = preg_replace(array('/\s/', '/[^\w_\.\-]/'), array('_', ''), $clean_name);

					$enc_name = $id . '_' . strtr($clean_name, '.', '_') . md5($clean_name);
					$clean_name = preg_replace('~\.[\.]+~', '.', $clean_name);

					$file = JFile::exists($path.'/'.$enc_name) ? $enc_name : $clean_name;
				}
			
				$from	= $path.'/'.$file;
				$file	= JPATH_ROOT.'/media/com_ninjaboard/attachments/'.$file;

				//Don't do anything if attachment don't exist
				if(!JFile::exists($from)) continue;
				
				JFile::copy($from, $file);
				
				$this->data['attachments'][$id]['file'] = basename($file);
			}
		}

		//Reconnect the lib.koowa.database to the Joomla! database
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
	 * Set the lib.koowa.database to use SMF prefix, and set the connection
	 *
	 * @return $this
	 */
	public function setDatabaseConnection()
	{
		//Save the NB resource so we can reset later
		if(!$this->_ninjaboard_resource)
		{
			$this->_ninjaboard_resource = KFactory::get('lib.joomla.database')->_resource;
		}

		$path	= $this->getPath();
		$config	= $path.'/Settings.php';
		
		if(!JFile::exists($config))
		{
			throw new ComNinjaboardDatabaseConverterException(sprintf(JText::_("Couldn't find SMF configuration file at: %s"), $config));
		}
		require $config;
		
		if(!isset($db_server, $db_user, $db_passwd, $db_name, $db_prefix))
		{
			throw new ComNinjaboardDatabaseConverterException(sprintf(JText::_("The configuration in '%s' is incomplete"), $config));
		}
		

		$config = array(
			'host'			=> $db_server,
			'user'			=> $db_user,
			'password'		=> $db_passwd,
			'database'		=> $db_name
		);
		
		$converter_database = new JDatabaseMySQLi($config);
		$this->_converter_resource	= $converter_database->_resource;

		KFactory::get('lib.koowa.database')
			->setConnection($this->_converter_resource)
			->setTablePrefix($db_prefix);
		
		return $this;
	}
	
	/**
	 * Change lib.koowa.database prefix and connection to what it was before setting it
	 *
	 * @return $this
	 */
	public function resetDatabaseConnection()
	{
		KFactory::get('lib.koowa.database')
			->setConnection($this->_ninjaboard_resource)
			->setTablePrefix(KFactory::get('lib.joomla.config')->getValue('dbprefix'));

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
		return KRequest::get('post.path', 'admin::com.ninja.filter.path', JPATH_ROOT);
	}

	/**
	 * Sets another label than the default "Smf"
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return 'SMF';
	}
}