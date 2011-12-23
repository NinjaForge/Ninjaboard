<?php



// no direct access



defined('_JEXEC') or die('Restricted access');

class PlgNinjaboardNinjaboardautotweet extends PlgKoowaDefault
{
	/**
	 * Twitter username
	 *
	 * @var string
	 */
	private $Username;

	/**
	 * Twitter password
	 *
	 * @var string
	 */
	private $Password;

	/**
	 * Constructor
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  $dispatcher
	 * @param  $config
	 */
	public function __construct($dispatcher, $config = array())
	{
		parent::__construct($dispatcher, $config);

		$this->Username = $this->params->get('username');
		$this->Password = $this->params->get('password');
	}

	/**
	 * controller.after.add event
	 *
	 * 		Gets the contents of a new post, then tweets it
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  $context
	 */
	public function onControllerAfterAdd(KCommandcontext $context)
	{
		//The caller is a reference to the object that is triggering this event
		$caller = $context['caller'];

		//The result is the actual result of the event, if this is an after event 
		//the result will contain the result of the action.
		$post = $context['result'];
		
		//The identifier
		$identifier = $caller->getIdentifier();

		if($identifier->name != 'post') return;


		$link = JRoute::_( 'index.php?option=com_ninjaboard&view=topic&id=' . $post->ninjaboard_topic_id . '&post=' . $post->id . '#post' . $post->id );
		$link = str_replace( '//', '/', JURI :: base() . $link );
		$link = str_replace( 'http:/', 'http://', $link );
		$link = str_replace( '/administrator', '', $link );
		$link = $this->make_tiny( $link );
		$msg = $post->subject . " - URL: " . $link;
		return $this->postToTwitter( $this->Username, $this->Password, $msg );

		// @TODO implement the following later
		global $mainframe;//get posts in selected categories
		$sql_forum = '';
		if( $only_forums )
		{
			$forums = explode( ',', $only_forums );
			foreach( $forums as $i => $forum )
			{
				$forums[ $i ] = ( int )$forum;
				if( $forum == -1 )
				{
					$forums[ $i ] = $params -> get( 'ninjaboard_forum_id', 0 );
				}
				if( $forum == 0 )
				{
					unset( $forums[ $i ] );
				}
			}
			$only_forums = implode( ',', $forums );
			if( $only_forums )
			{
				if( $forums )
				{
					$sql_forum = " AND (f.ninjaboard_forum_id IN (" . $only_forums . ") OR f.parent_id IN (" . $only_forums . "))";
				}
				else
				{
					$sql_forum = " AND f.ninjaboard_forum_id IN (" . $only_forums . ")";
				}
			}
		}//get posts not in selected categories
		if( $not_forums )
		{
			$forums = explode( ',', $not_forums );
			foreach( $forums as $i => $forum )
			{
				$forums[ $i ] = ( int )$forum;
				if( $forum == 0 )
				{
					unset( $forums[ $i ] );
				}
			}
			$not_forums = implode( ',', $forums );
			if( $not_forums )
			{
				if( $forums )
				{
					$sql_forum .= " AND (f.ninjaboard_forum_id NOT IN (" . $not_forums . ") AND f.parent_id NOT IN (" . $not_forums . "))";
				}
				else
				{
					$sql_forum .= " AND f.ninjaboard_forum_id NOT IN (" . $not_forums . ")";
				}
			}
		}//$isNew = true;
		$topics = trim( $this -> params -> get( 'topics' ) );
		$forums = trim( $this -> params -> get( 'forums' ) );
		$not_topics = trim( $this -> params -> get( 'not_topics' ) );
		$not_forums = trim( $this -> params -> get( 'not_forums' ) );

		if( $topics )
		{
			$topic_ids = explode( ',', $topics );
			JArrayHelper :: toInteger( $topic_ids );
		}
		else
		{
			$topic_ids = array();
		}
		if( $forums )
		{
			$forum_ids = explode( ',', $forums );
			JArrayHelper :: toInteger( $forum_ids );
		}
		else
		{
			$forum_ids = array();
		}
		if( $not_topics )
		{
			$not_topic_ids = explode( ',', $not_topics );
			JArrayHelper :: toInteger( $not_topic_ids );
		}
		else
		{
			$not_topic_ids = array();
		}
		if( $not_forums )
		{
			$not_forum_ids = explode( ',', $not_forums );
			JArrayHelper :: toInteger( $not_forum_ids );
		}
		else
		{
			$not_forum_ids = array();
		}
		if( in_array( $post -> postid, $not_topic_ids ) || in_array( $post->forumid, $not_forum_ids) || !$isNew )
		{
			return false;
		}
		elseif( ( in_array( $post -> postid, $topic_ids ) || in_array( $post->forumid, $forum_ids ) ) && $isNew == true )
		{
			$link = JRoute :: _( 'index.php?option=com_ninjaboard&view=topic&id=' . $topic_id . '&Itemid=' . $itemId . '#post' . $post_id );
			$link = str_replace( '//', '/', JURI :: base() . $link );
			$link = str_replace( 'http:/', 'http://', $link );
			$link = str_replace( '/administrator', '', $link );
			$link = $this -> make_tiny( $link );
			$msg = $post -> subject . " - URL: " . $link;
			$this -> postToTwitter( $this -> Username, $this -> Password, $msg );
		}
		return true;		
	}

	function make_tiny( $url )
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt( $ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		$data = curl_exec( $ch );
		curl_close( $ch );
		return $data;
	}
	function postToTwitter( $username, $password, $message )
	{
		$host = "http://twitter.com/statuses/update.json";
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $host );
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_USERPWD, "$username:$password" );
		curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS, "status=$message");

		$result = curl_exec( $ch );// Look at the returned header
		$resultArray = curl_getinfo( $ch );
		curl_close( $ch );
		if( $resultArray[ 'http_code' ] == "200" )
		{
			$twitter_status = 'Your message has been sent! <a href="http://twitter.com/' . $username . '">See your profile</a>';
		}
		else
		{
			$twitter_status = "Error posting to Twitter. Retry";
		}
		return $twitter_status;
	}//get menu id

	function getItemId()
	{
		$database = JFactory::getDBO();
		$query = "select id from #__menu where link like '%index.php?option=com_ninjaboard%' limit 1";
		$database->setQuery( $query );
		$Itemid = $database->loadResult();
		return $Itemid;
	}
}