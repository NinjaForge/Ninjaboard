<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: abstract.php 1784 2011-04-12 22:32:41Z stian $
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

			if($rows) $this->insert($rows, $table);
		}

		return $this;
	}
	
	/**
	 * Runs a heavy insert operation that makes use of mysqli prepared statements for optimal performance
	 *
	 * @TODO this should be a patch on nooku fw for when you're saving rowsets
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  KDatabaseRowsetInterface | array
	 * @param  KDatabaseTableInterface
	 * @return status
	 */
	public function insert($rows, KDatabaseTableAbstract $table, $dbprefix = false)
	{
	    if(!$dbprefix) $dbprefix = KFactory::get('lib.joomla.config')->getValue('dbprefix');
	    $mysqli    = KFactory::get('lib.koowa.database.adapter.mysqli')->getConnection();
	    $statement = false;
	    $base      = $table->getDatabase()->quoteName($dbprefix.$table->getBase());
	    $columns   = $table->getColumns(true);

	    foreach($rows as $row)
		{
			//@TODO Do not import rows that are without ids, they can cause duplicate key errors
			//if(!isset($row['id'])) continue;
			
			//Add missing columns for the sake of the prepared statement query
			foreach($columns as $field => $column)
			{
			    if(!isset($row[$field])) $row[$field] = $column->default !== NULL ? $column->default : '';
			}

			//Filter out any extra columns.
			$data = array_intersect_key($row, $columns);

			//Get the data and apply the column mappings
			$data = $table->mapColumns($data);

            //The key order is vital!
            ksort($data);

			//@TODO support update statements
			//This may be slow, but it's better than failed imports due to rows being inserted into tables during migration
			//try {
			
			    //Create the prepared statements that the other (likely 500 rows) will reuse
			    if(!$statement)
			    {
			        foreach($data as $key => $val)
			        {
			        	$keys[] = '`'.$key.'`';
			        	$vals[] = '?';
			        }

			        $query     = 'INSERT INTO '.$base.'('.implode(', ', $keys).') VALUES ('.implode(', ', $vals).')';
			        $statement = $mysqli->prepare($query) or die('failed to prepare query statement: '.$query);
			    }
			
			    $params = array('');
			    foreach($data as $key => $val)
				{
				    $params[0] .= is_int($val) ? 'i' : is_float($val) ? 'd' : 's';
				    
				     
					$params[] = is_array($val) ? json_encode($val) : $val;
				}

                //Bind the params to the prepared statement in a dynamic fashion
			    call_user_func_array(array($statement, 'bind_param'), $this->refValues($params));
			    
			    //Execute the prepared statement, it's super fast!
			    $statement->execute();
			/*
			} catch(KDatabaseException $e) {
			    // The following is for a query used should we get a duplicate key error
			    $query = $table->getDatabase()->getQuery();
			    foreach($table->getPrimaryKey() as $key => $column) {
			        $query->where($column->name, '=', $table->filter(array($key => $row[$key]), true));
			    }
			
				$table->getDatabase()->update($table->getBase(), KConfig::toData($data), $query);
			}
			//*/
		}
		
		//printf("%d Row inserted.\n", $statement->affected_rows); 
		
		/* */
		if($statement) $statement->close(); 
	}
	
	/**
	 * Runs a lighter but convenient update operation that's faster than the using row objects
	 *
	 * @TODO this should be a patch on nooku fw for when you're saving rowsets
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  KDatabaseRowsetInterface | array
	 * @param  KDatabaseTableInterface
	 * @return status
	 */
	public function update($rows, KDatabaseTableAbstract $table, $dbprefix = false)
	{
	    if(!$dbprefix) $dbprefix = KFactory::get('lib.joomla.config')->getValue('dbprefix');
	    $mysqli    = KFactory::get('lib.koowa.database.adapter.mysqli')->getConnection();
	    $statement = false;
	    $base      = $table->getDatabase()->quoteName($dbprefix.$table->getBase());
	    $columns   = $table->getColumns(true);
	    $primaries = $table->getPrimaryKey();
    
	    foreach($rows as $row)
		{
			//@TODO Do not import rows that are without ids, they can cause duplicate key errors
			//if(!isset($row['id'])) continue;
			
			//Reverse map first
			$row = $table->mapColumns($row, true);
			
			//Add missing columns for the sake of the prepared statement query
			foreach($columns as $field => $column)
			{
			    if(!isset($row[$field])) $row[$field] = $column->default !== NULL ? $column->default : '';
			}

			//Filter out any extra columns.
			$data = array_intersect_key($row, $columns);
			
			//Get the primaries
			$where = $table->mapColumns(array_intersect_key($row, $primaries));

			//Get the data and apply the column mappings
			$data = $table->mapColumns($data);

            //The key order is vital!
            ksort($data);
            ksort($where);

			//try {
			
			    //Create the prepared statements that the other (likely 500 rows) will reuse
			    if(!$statement)
			    {
			        foreach($data as $key => $val)
			        {
			        	$vals[] = $key.'=?';
			        }
			        foreach($where as $key => $val)
			        {
			        	$keys[] = $key.' = ?';
			        }

			        $query     = 'UPDATE '.$base.' SET '.implode(', ', $vals).' WHERE '.implode(', ', $keys);
			        $statement = $mysqli->prepare($query) or die('failed to prepare query statement: '.$query);
			    }
			
			    $params = array('');
			    foreach($data as $key => $val)
				{
				    $params[0] .= is_int($val) ? 'i' : is_float($val) ? 'd' : 's';
					$params[] = is_array($val) ? json_encode($val) : $val;
				}
				foreach($where as $key => $val)
				{
				    $params[0] .= is_int($val) ? 'i' : is_float($val) ? 'd' : 's';
					$params[] = is_array($val) ? json_encode($val) : $val;
				}

                //Bind the params to the prepared statement in a dynamic fashion
			    call_user_func_array(array($statement, 'bind_param'), $this->refValues($params));

			    //Execute the prepared statement, it's super fast!
			    $statement->execute();
			/*
			} catch(KDatabaseException $e) {
			    // The following is for a query used should we get a duplicate key error
			    $query = $table->getDatabase()->getQuery();
			    foreach($table->getPrimaryKey() as $key => $column) {
			        $query->where($column->name, '=', $table->filter(array($key => $row[$key]), true));
			    }
			
				$table->getDatabase()->update($table->getBase(), KConfig::toData($data), $query);
			}
			//*/
		}
		
		//printf("%d Row inserted.\n", $statement->affected_rows); 
		
		/* */
		if($statement) $statement->close(); 
	}
	
	/**
	 * This is a wrapper for mysqli prepared statements bind_param as php 5.3+ requires values to be passed by reference
	 *
	 * @origin http://www.php.net/manual/en/mysqli-stmt.bind-param.php#100879
	 * @param  array    over arguments
	 * @return array    referenced values when needed, on php 5.3+
	 */
	private function refValues($arr){
        if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
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
		    if(!isset($table['options']['identity_column'])) $table['options']['identity_column'] = 'id';
		    if(!isset($table['options']['name'])) $table['options']['name'] = $table['name'];
		
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
				
				if(!is_null($query->columns) && !count($query->columns)) {
	                $query->select('*');
	            }
	
	            if(!count($query->from)) {
	                $query->from($table['options']['name'].' AS tbl');
	            }
			}
			
			$rows = KFactory::get('lib.koowa.database.adapter.mysqli')->select($query, KDatabase::FETCH_ARRAY_LIST, $table['options']['identity_column']);

			//$rows = KFactory::get('admin::com.default.database.table.'.$name.$table['options']['name'], $table['options'])
			//			->select($query, KDatabase::FETCH_OBJECT_LIST);

            $this->data[$name] = isset($this->data[$name]) && is_array($this->data[$name]) ? array_merge($this->data[$name], $rows) : $rows;
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
				    if(!isset($table['options']['identity_column'])) $table['options']['identity_column'] = 'id';
				    if(!isset($table['options']['name'])) $table['options']['name'] = $table['name'];

					$name = $table['name'];
					$query = clone $table['query'];
					
					if(!is_null($query->columns) && !count($query->columns)) {
		                $query->select('*');
		            }
					
		            if(!count($query->from)) {
		                $query->from($table['options']['name'].' AS tbl');
		            }

					$rows = KFactory::get('lib.koowa.database.adapter.mysqli')->select($query, KDatabase::FETCH_ARRAY_LIST, $table['options']['identity_column']);
	
					$this->data[$name] = isset($this->data[$name]) && is_array($this->data[$name]) ? array_merge($this->data[$name], $rows) : $rows;
				}
			}
		}
	}

	protected function updateForumPaths()
	{
		$query  = KFactory::tmp('lib.koowa.database.query')->where('parent_id', '=', 0);
		$table  = KFactory::tmp('admin::com.ninjaboard.database.table.forums');
		$forums = $table->select($query, KDatabase::FETCH_ROWSET);
		
		foreach($forums as $forum)
		{
			$this->updateForumPathsRecurse($forum);
		}
	}
	
	protected function updateForumPathsRecurse($forum)
	{
		$query    = KFactory::tmp('lib.koowa.database.query')->where('parent_id', '!=', 0)->where('parent_id', '=', $forum->id)->where('path', '=', '/');
		$table    = KFactory::tmp('admin::com.ninjaboard.database.table.forums');
		$path     = $forum->path;
		$forums   = $table->select($query, KDatabase::FETCH_ROWSET);
		
		foreach($forums as $forum)
		{
			$forum->path      = $path . $forum->parent_id . '/';
			$forum->save();

			$this->updateForumPathsRecurse($forum);
		}
	}
}


/**
 * The following code is originally from Agora, 
 * so the original copyright and licensing headers seen below still apply.
 */

/**
 * This file is part of the Agora distribution. 
 * Detailed copyright and licensing information can be found
 * in the gpl-3.0.txt file which should be included in the distribution.
 * 
 * @version		$Id: abstract.php 1784 2011-04-12 22:32:41Z stian $
 * @copyright  2007 - 2010 jVitals
 * @license   GPLv3 Open Source
 * @link       http://jvitals.com
 * @since      File available since initial release
 */

function html2bbcode($text) {

	$text = strip_tags($text, '<table><tr><td><b><strong><i><em><u><a><div><span><p><strike><blockquote><ol><ul><li><font><img><br><br/><h1><h2><h3><h4><h5><h6><script>');

	$pregfind = array(
		"/<script.*>.*<\/script>/siU",
		'/on(mousewheel|mouseover|click|load|onload|submit|focus|blur)="[^"]*"/i',
		"/(\r\n|\n|\r)/",
		"/<table([^>]*(width|background|background-color|bgcolor)[^>]*)>/siUe",
		"/<table.*>/siU",
		"/<tr.*>/siU",
		"/<td>/i",
		"/<td(.+)>/siUe",
		"/<\/td>/i",
		"/<\/tr>/i",
		"/<\/table>/i",
		'/<h([0-9]+)[^>]*>(.*)<\/h\\1>/siU',
		"/<img[^>]+smilieid=\"(\d+)\".*>/esiU",
		"/<img([^>]*src[^>]*)>/eiU",
		"/<a\s+?name=.+?\".\">(.+?)<\/a>/is",
		"/<br.*>/siU",
		"/<span\s+?style=\"float:\s+(left|right);\">(.+?)<\/span>/is",
	);
	$pregreplace = array(
		'',
		'',
		"\n",
		"simpletag('\\1')",
		'[table]',
		'[tr]',
		'[td]',
		"simpletag('\\1')",
		'[/td]',
		'[/tr]',
		'[/table]',
		"[size=\\1]\\2[/size]\n\n",
		"smileycode('\\1')",
		"imgtag('\\1')",
		'\1',
		"\n",
		"[float=\\1]\\2[/float]",
	);
	$text = preg_replace($pregfind, $pregreplace, $text);

	$text = recursion('b', $text, 'simpletag', 'b');
	$text = recursion('strong', $text, 'simpletag', 'b');
	$text = recursion('i', $text, 'simpletag', 'i');
	$text = recursion('em', $text, 'simpletag', 'i');
	$text = recursion('u', $text, 'simpletag', 'u');
	$text = recursion('a', $text, 'atag');
	$text = recursion('font', $text, 'fonttag');
	$text = recursion('blockquote', $text, 'simpletag', 'indent');
	$text = recursion('ol', $text, 'listtag');
	$text = recursion('ul', $text, 'listtag');
	$text = recursion('div', $text, 'divtag');
	$text = recursion('span', $text, 'spantag');
	$text = recursion('p', $text, 'ptag');

	$pregfind = array("/(?<!\r|\n|^)\[(\/list|list|\*)\]/", "/<li>(.*)((?=<li>)|<\/li>)/iU", "/<p.*>/iU", "/<p><\/p>/i", "/(<a>|<\/a>|<\/li>)/is", "/<\/?(A|LI|FONT|DIV|SPAN)>/siU", "/\[url[^\]]*\]\[\/url\]/i", "/\[url=javascript:[^\]]*\](.+?)\[\/url\]/is");
	$pregreplace = array("\n[\\1]", "\\1\n", "\n", '', '', '', '', "\\1");
	$text = preg_replace($pregfind, $pregreplace, $text);

	$strfind = array('&nbsp;', '&lt;', '&gt;', '&amp;');
	$strreplace = array(' ', '<', '>', '&');
	$text = str_replace($strfind, $strreplace, $text);

	return trim($text);
}

function getoptionvalue($option, $text) {
	preg_match("/$option(\s+?)?\=(\s+?)?[\"']?(.+?)([\"']|$|>)/is", $text, $matches);
	if (isset($matches[3]))
	return trim($matches[3]);
	else
	return '';
}

function parsestyle($tagoptions, &$prependtags, &$appendtags) {
	$searchlist = array(
		array('tag' => 'align', 'option' => TRUE, 'regex' => 'text-align:\s*(left);?', 'match' => 1),
		array('tag' => 'align', 'option' => TRUE, 'regex' => 'text-align:\s*(center);?', 'match' => 1),
		array('tag' => 'align', 'option' => TRUE, 'regex' => 'text-align:\s*(right);?', 'match' => 1),
		array('tag' => 'color', 'option' => TRUE, 'regex' => '(?<![a-z0-9-])color:\s*([^;]+);?', 'match' => 1),
		array('tag' => 'font', 'option' => TRUE, 'regex' => 'font-family:\s*([^;]+);?', 'match' => 1),
		array('tag' => 'size', 'option' => TRUE, 'regex' => 'font-size:\s*(\d+(\.\d+)?(px|pt|in|cm|mm|pc|em|ex|%|));?', 'match' => 1),
		array('tag' => 'b', 'option' => FALSE, 'regex' => 'font-weight:\s*(bold);?'),
		array('tag' => 'i', 'option' => FALSE, 'regex' => 'font-style:\s*(italic);?'),
		array('tag' => 'u', 'option' => FALSE, 'regex' => 'text-decoration:\s*(underline);?')
	);

	$style = getoptionvalue('style', $tagoptions);
	$style = preg_replace(
		"/(?<![a-z0-9-])color:\s*rgb\((\d+),\s*(\d+),\s*(\d+)\)(;?)/ie",
		'sprintf("color: #%02X%02X%02X$4", $1, $2, $3)',
		$style
	);
	foreach($searchlist as $searchtag) {
		if(preg_match('/'.$searchtag['regex'].'/i', $style, $match)) {
		    if ( isset($match)){
		        $ii = $searchtag.'[match]';
		        if (isset($match[$ii]))
			    $opnvalue = $match["$searchtag[match]"];
			    else $opnvalue = '';
		    }
			else
			$opnvalue = '';
			
			$prependtags .= '['.$searchtag['tag'].($searchtag['option'] == TRUE ? '='.$opnvalue.']' : ']');
			$appendtags = '[/'.$searchtag['tag']."]$appendtags";
		}
	}
}

function simpletag($options, $text, $tagname, $parseto) {
	if(trim($text) == '') {
		return '';
	}
	$text = recursion($tagname, $text, 'simpletag', $parseto);
	return "[$parseto]{$text}[/$parseto]";
}

function spantag($spanoptions, $text) {
	$prependtags = $appendtags = '';
	parsestyle($spanoptions, $prependtags, $appendtags);

	return $prependtags.recursion('span', $text, 'spantag').$appendtags;
}

function atag($aoptions, $text) {
	$href = getoptionvalue('href', $aoptions);

	if(substr($href, 0, 7) == 'mailto:') {
		$tag = 'email';
		$href = substr($href, 7);
	} else {
		$tag = 'url';
	}

	return "[$tag=$href]".trim(recursion('a', $text, 'atag'))."[/$tag]";
}

function litag($listoptions, $text) {
	return '[*]'.rtrim($text);
}

function listtag($listoptions, $text, $tagname) {

	$text = preg_replace('/<li>((.(?!<\/li))*)(?=<\/?ol|<\/?ul|<li|\[list|\[\/list)/siU', '<li>\\1</li>', $text);
	$text = recursion('li', $text, 'litag');

	if($tagname == 'ol') {
		$listtype = fetchoptionvalue('type=', $listoptions) ? fetchoptionvalue('type=', $listoptions) : 1;
		if(in_array($listtype, array('1', 'a', 'A'))) {
			$opentag = '[list='.$listtype.']';
		}
	} else {
		$opentag = '[list]';
	}
	return $text ? $opentag.recursion($tagname, $text, 'listtag').'[/list]' : FALSE;
}


function fetchoptionvalue($option, $text) {
	if(($position = strpos($text, $option)) !== false) {
		$delimiter = $position + strlen($option);
		if($text{$delimiter} == '"') {
			$delimchar = '"';
		} elseif($text{$delimiter} == '\'') {
			$delimchar = '\'';
		} else {
			$delimchar = ' ';
		}
		$delimloc = strpos($text, $delimchar, $delimiter + 1);
		if($delimloc === false) {
			$delimloc = strlen($text);
		} elseif($delimchar == '"' OR $delimchar == '\'') {
			$delimiter++;
		}
		return trim(substr($text, $delimiter, $delimloc - $delimiter));
	} else {
		return '';
	}
}

function fonttag($fontoptions, $text) {
	$tags = array('font' => 'face=', 'size' => 'size=', 'color' => 'color=');
	$prependtags = $appendtags = '';

	foreach($tags as $bbcode => $locate) {
		$optionvalue = fetchoptionvalue($locate, $fontoptions);
		if($optionvalue) {
			$prependtags .= "[$bbcode=$optionvalue]";
			$appendtags = "[/$bbcode]$appendtags";
		}
	}

	parsestyle($fontoptions, $prependtags, $appendtags);

	return $prependtags.recursion('font', $text, 'fonttag').$appendtags;
}


function divtag($divoptions, $text) {
	$prepend = $append = '';

	parsestyle($divoptions, $prepend, $append);
	$align = getoptionvalue('align', $divoptions);

	switch($align) {
		case 'left':
		case 'center':
		case 'right':
			break;
		default:
			$align = '';
	}

	if($align) {
		$prepend .= "[align=$align]";
		$append .= "[/align]";
	}
	$append .= "\n";

	return $prepend.recursion('div', $text, 'divtag').$append;
}

function imgtag($attributes) {
	$value = array('src' => '', 'width' => '', 'height' => '');
	preg_match_all("/(src|width|height)=([\"|\']?)([^\"']+)(\\2)/is", stripslashes($attributes), $matches);
	if(is_array($matches[1])) {
		foreach($matches[1] as $key => $attribute) {
			$value[strtolower($attribute)] = $matches[3][$key];
		}
	}
	@extract($value);
	if(!preg_match("/^http:\/\//i", $src)) {
	    $uri =& JURI::getInstance();
		$src = $uri->root().$src;
	}
	return $src ? ($width && $height ? '[img='.$width.','.$height.']'.$src.'[/img]' : '[img]'.$src.'[/img]') : '';
}


function ptag($poptions, $text) {
	$align = getoptionvalue('align', $poptions);

	switch($align) {
		case 'left':
		case 'center':
		case 'right':
			break;
		default:
			$align = '';
	}

	$prepend = $append = '';
	parsestyle($poptions, $prepend, $append);
	if($align) {
		$prepend .= "[align=$align]";
		$append .= "[/align]";
	}
	$append .= "\n";

	return $prepend.recursion('p', $text, 'ptag').$append;
}

function recursion($tagname, $text, $function, $extraargs = '') {
	$tagname = strtolower($tagname);
	$open_tag = "<$tagname";
	$open_tag_len = strlen($open_tag);
	$close_tag = "</$tagname>";
	$close_tag_len = strlen($close_tag);

	$beginsearchpos = 0;
	do {
		$textlower = strtolower($text);
		$tagbegin = @strpos($textlower, $open_tag, $beginsearchpos);
		if($tagbegin === FALSE) {
			break;
		}

		$strlen = strlen($text);

		$inquote = '';
		$found = FALSE;
		$tagnameend = FALSE;
		for($optionend = $tagbegin; $optionend <= $strlen; $optionend++) {
			$char = $text{$optionend};
			if(($char == '"' || $char == "'") && $inquote == '') {
				$inquote = $char;
			} elseif(($char == '"' || $char == "'") && $inquote == $char) {
				$inquote = '';
			} elseif($char == '>' && !$inquote) {
				$found = TRUE;
				break;
			} elseif(($char == '=' || $char == ' ') && !$tagnameend) {
				$tagnameend = $optionend;
			}
		}
		if(!$found) {
			break;
		}
		if(!$tagnameend) {
			$tagnameend = $optionend;
		}
		$offset = $optionend - ($tagbegin + $open_tag_len);
		$tagoptions = substr($text, $tagbegin + $open_tag_len, $offset);
		$acttagname = substr($textlower, $tagbegin + 1, $tagnameend - $tagbegin - 1);
		if($acttagname != $tagname) {
			$beginsearchpos = $optionend;
			continue;
		}

		$tagend = strpos($textlower, $close_tag, $optionend);
		if($tagend === FALSE) {
			break;
		}

		$nestedopenpos = strpos($textlower, $open_tag, $optionend);
		while($nestedopenpos !== FALSE && $tagend !== FALSE) {
			if($nestedopenpos > $tagend) {
				break;
			}
			$tagend = strpos($textlower, $close_tag, $tagend + $close_tag_len);
			$nestedopenpos = strpos($textlower, $open_tag, $nestedopenpos + $open_tag_len);
		}
		if($tagend === FALSE) {
			$beginsearchpos = $optionend;
			continue;
		}

		$localbegin = $optionend + 1;
		$localtext = $function($tagoptions, substr($text, $localbegin, $tagend - $localbegin), $tagname, $extraargs);

		$text = substr_replace($text, $localtext, $tagbegin, $tagend + $close_tag_len - $tagbegin);

		$beginsearchpos = $tagbegin + strlen($localtext);
	} while($tagbegin !== FALSE);

	return $text;
}