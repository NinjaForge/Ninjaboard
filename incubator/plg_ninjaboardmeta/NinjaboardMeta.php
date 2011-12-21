<?php
/**
* @author Jeff Channell (jeffchannell.com)
* @original author Juan Padial (www.shikle.com)
* This plugin will automatically generate Meta Description tags for Ninjaboard forum component.
* version 1.0
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.event.plugin');

class plgSystemNinjaboardMeta extends JPlugin
{
	// Constructor
	function plgSystemNinjaboardMeta( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}
	
	function onAfterDispatch()
	{
		global $mainframe;
		$option = JRequest::getCmd('option', '');
		$document =& JFactory::getDocument();
		$docType = $document->getType();
		if (!$mainframe->isSite() || $docType != 'html') return;
		if ($option !='com_ninjaboard') return;

		if ($option =='com_ninjaboard'){

			$plugin =& JPluginHelper::getPlugin('system', 'NinjaboardMeta');
			$pluginParams = new JParameter( $plugin->params );
			$ninjaboardtitle = $pluginParams->def('maintitle',$mainframe->getCfg('sitename')); 
			$ninjaboarddesc = $pluginParams->def('maindescription',$mainframe->getCfg('MetaDesc')); 
			$ninjaboardkeywords = $pluginParams->def('mainkeywords',$mainframe->getCfg('MetaKeys'));
			$length = $pluginParams->def('desclength','200');
			$sep = str_replace('\\','',$pluginParams->def('separator','|')); //Sets and removes Joomla escape char bug.
			
			// added pluginParams for toggling Topic/Forum/etc in title
			// jdc
			$showCategory = $pluginParams->def('showcategory','0');
			$showTopicForum = $pluginParams->def('showtopicforum','0');
			$showTopicCategory = $pluginParams->def('showtopiccategory','0');
		    
			//get Ninjaboard variables from URL
			$task = JRequest::getCmd('task', '');
			$view = JRequest::getCmd('view', '');
			// get integer to not escape $id in queries
			$id = JRequest::getInt('id', 0);
			$fid = JRequest::getInt('forum', 0);	
			$topic = JRequest::getInt('topic', 0);	
			$cat = JRequest::getInt('category', 0);		

			//get database config
			$database =& JFactory::getDBO();
  	     
			//Title and desc for Ninjaboard board main page.
			//Skip from no forum or topic because for now only use for main page, categories, forums and topics.
			if( $task == '' && $view == 'board' && !$id && !$cat && !$fid && !$topic )
			{
				$pageTitle = $ninjaboardtitle; 
				$metadesc = $ninjaboarddesc; 
				$keywords = $ninjaboardkeywords;
			}
  	          
			//Category
			if( $task=='' && $view == 'board' && $cat )
			{
				//get Category's name
				$query = "SELECT `name` FROM #__ninjaboard_categories WHERE id ='$cat' AND published='1'";
				$database->SetQuery( $query );
				$cattitle = $database->loadResult();
				// ignore 'show' settings here
				$pageTitle = $cattitle.' ' .$sep.' '.$ninjaboardtitle;
  	          
				//get categories descriptions from plugin params
				$categories = $pluginParams->def('categories','');
	
				if($categories!='')
				{
					$categories = explode("\n",$categories);
					foreach($categories as $category) 
					{
						// get the user-entered categories
						list($catid,$catdesc) = explode("|",$category);
			               
						if($catid == $cat)
							$metadesc = $catdesc;
					}
				}
				else
				{
					$metadesc=$ninjaboarddesc;
				}
				//get categories keywords
				$categorieskw = $pluginParams->def('categorieskw','');
				if($categorieskw !='')
				{
					$categorieskw = explode("\n",$categorieskw);
					foreach($categorieskw as $category) 
					{
						// get the user-entered categories
						list( $catid, $catkey ) = explode("|",$category);
			               
						if( $catid == $cat )
							$keywords = $catkey;
					}
				}
				else
				{
					$keywords = $ninjaboardkeywords;
				}
			}  
  	          
			//Forum
			if( $view == 'forum' && $fid )
			{
				//get forum name
				$query = "SELECT f.`name`, f.`description`, c.`name` FROM #__ninjaboard_forums f, #__ninjaboard_categories c WHERE f.id ='$fid' AND c.id=f.id_cat";
				$database->SetQuery( $query );
				list( $forumtitle,$forumdesc,$catname ) = $database->loadRow();
				// instead of concat i'm imploding
				$pageTitleParts = array();
				$pageTitleParts[] = $forumtitle;
				if( $showCategory ) $pageTitleParts[] = $catname;
				$pageTitleParts[] = $ninjaboardtitle;
				$pageTitle = implode( ' '.$sep.' ', $pageTitleParts );
				//$pageTitle = $forumtitle.' '.$sep.' '.$catname.' '.$sep.' '.$ninjaboardtitle;
				
  	          
				//get categories descriptions from plugin params
				$forums = $pluginParams->def('forums','');
	
				if($forums!='')
				{
					$forums = explode("\n",$forums);
					foreach($forums as $forum) 
					{
						// get the user-entered categories
						list($forid,$fordesc) = explode("|",$forum);
			               
						if($forid == $fid)
							$metadesc = $forumdesc;
					}
				}
				else
				{
					$metadesc=$ninjaboarddesc;
				}
				//get categories keywords
				$forumskw = $pluginParams->def('forumskw','');
				if($forumskw !='')
				{
					$forumskw = explode("\n",$forumskw);
					foreach($forumskw as $forums) 
					{
						// get the user-entered categories
						list( $forid, $forkey ) = explode("|",$forums);
			               
						if( $forid == $fid )
							$keywords = $forkey;
					}
				}
				else
				{
					$keywords = $ninjaboardkeywords;
				}
			}
			
			
			//Topic
			if( $view == 'topic' && $topic )
			{
				//get topic subject
				$query = "SELECT ".
				         "p.`subject`, p.`text`, f.`name`, c.`name` ".
				         "FROM #__ninjaboard_posts p, #__ninjaboard_forums f, #__ninjaboard_categories c ".
				         "WHERE p.`id_topic`='$topic' AND p.`id_forum`=f.`id` AND c.`id`=f.`id_cat` ".
				         "ORDER BY p.`id` ASC LIMIT 1";
				$database->SetQuery ($query);
				list($topictitle,$topicdesc,$forumtitle,$catname) = $database->loadRow();
				// instead of concat i'm imploding
				$pageTitleParts = array();
				$pageTitleParts[] = $topictitle;
				if( $showTopicForum ) $pageTitleParts[] = $forumtitle;
				if( $showTopicCategory ) $pageTitleParts[] = $catname;
				$pageTitleParts[] = $ninjaboardtitle;
				$pageTitle = implode( ' '.$sep.' ', $pageTitleParts );
				//$pageTitle = $topictitle.' '.$sep.' '.$ninjaboardtitle; 
				// commented out the rest - nb doesn't have topicdesc :/
				// just use the post text
				//get topic description
				//if ($topicdesc!=''){
					$metadesc = $topicdesc; 
				/*}
				else {
					//if there is no topic description, get first post to set as metadesc
					$query = "SELECT message FROM #__ninjaboard_posts WHERE topic_id ='$id' ORDER BY id ASC LIMIT 1";
					$database->SetQuery ($query);
					$topicdesc = $database->loadResult();
					$metadesc = $topicdesc;
				}*/
				//TODO: get topic keywords.
				$text = $topictitle;
				$text .=$topicdesc;
				// killed this, just use $query above
				//$query = "SELECT message FROM #__ninjaboard_posts WHERE topic_id ='$id' ORDER BY id ASC LIMIT 1";
				$text .= $database->loadRow();
				$listexclude = $pluginParams->def('listexclude','');
				$listgoldwords = $pluginParams->def('goldwords','');
				$mindensity = $pluginParams->def('mindensity','2');
				$minlength = $pluginParams->def('minlength','5');
				$maxwords = $pluginParams->def('maxwords','20');
				$keywords = keys($text,$listexclude,$listgoldwords,$mindensity,$minlength,$maxwords);
			}
			
			  
			//Profile
			if( ( in_array( $view, array( 'profile', 'userposts' ) ) && $id ) || $view == 'editprofile' )
			{
				// if editprofile, we need the user id
				if( $view == 'editprofile' )
				{
					$user =& JFactory::getUser();
					$id = $user->get('id');
				}
				//get all forms of user name and signature
				$query = "SELECT u.`name`, u.`username`, p.`p_firstname`, p.`p_lastname`, nu.`signature` ".
				         "FROM #__users u, #__ninjaboard_profiles p, #__ninjaboard_users nu ".
				         "WHERE u.id ='$id' AND p.id = u.id AND p.id = nu.id LIMIT 1";
				$database->SetQuery( $query );
				list( $fname,$uname,$pfname,$plname,$psig ) = $database->loadRow();
				// start an array for first & last name
				$pname = array();
				// if either are 0 length, don't add
				if( strlen( $pfname ) > 0 ) $pname[] = $pfname;
				if( strlen( $plname ) > 0 ) $pname[] = $plname;
				// set page title by precedence: profile names, name, username
				$pname = ( count( $pname ) > 0 ) ? $pname = implode( ' ', $pname ) : ( strlen( $fname ) ? $fname : $uname );
				$pageTitle = 'Ninjaboard profile for '.$pname.' '.$sep.' '.$ninjaboardtitle;
				// meta is the signature
				$metadesc = strlen( $psig ) > 0 ? htmlentities( $psig ) : $ninjaboarddesc;
				// get user keywords: either use first & last name
				$keywords = preg_replace( '/\s/', ',', $pname );
			}
			
			/*
			this would be better if done in a loop, I know - 
			but it's done this way namely for 2 reasons:
			1) it's the set convention already :P
			2) maybe sometime in the future some of these views will have different keywords & meta descriptions
			*/
			
			// Latest Posts
			if( $view == 'latestposts' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_LATESTPOSTS').' '.$sep.' '.$ninjaboardtitle;
			}
			// Who's Online
			if( $view == 'whosonline' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_WHOSONLINE').' '.$sep.' '.$ninjaboardtitle;
			}
			// user list
			if( $view == 'userlist' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_USERLIST').' '.$sep.' '.$ninjaboardtitle;
			}
			// terms
			if( $view == 'terms' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_TERMS').' '.$sep.' '.$ninjaboardtitle;
			}
			// edittopic
			if( $view == 'edittopic' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_EDITTOPIC').' '.$sep.' '.$ninjaboardtitle;
			}
			// editpost
			if( $view == 'editpost' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_EDITPOST').' '.$sep.' '.$ninjaboardtitle;
			}
			// search
			if( $view == 'search' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_SEARCH').' '.$sep.' '.$ninjaboardtitle;
			}
			// login
			if( $view == 'login' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_LOGIN').' '.$sep.' '.$ninjaboardtitle;
			}
			// reportpost
			if( $view == 'reportpost' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_REPORTPOST').' '.$sep.' '.$ninjaboardtitle;
			}
			// requestlogin
			if( $view == 'requestlogin' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_REQUESTLOGIN').' '.$sep.' '.$ninjaboardtitle;
			}
			// resetlogin
			if( $view == 'resetlogin' )
			{
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_RESETLOGIN').' '.$sep.' '.$ninjaboardtitle;
			}
			// information
			if( $view == 'information' )
			{
				// this would be a cool one to customize!
				$keywords = $ninjaboardkeywords;
				$metadesc = $ninjaboarddesc;
				$pageTitle = JText::_('NB_INFORMATION').' '.$sep.' '.$ninjaboardtitle;
			}
			
			
			// Set Metatags
			$document->setTitle ($pageTitle);
			// Clean things up and prepare Meta Description tag.
			$metadesc = $this->cutText($metadesc);
			$metadesc = $metadesc . ' ';
			$metadesc = substr($metadesc,0,$length);
			$metadesc = substr($metadesc,0,strrpos($metadesc,' '));
			$document->setDescription ($metadesc);
			$document->setMetaData('keywords', $keywords);
	
		}
	}
	   
	function cutText($text)
	{
		$text=clean($text);
		// general sentence tidyup
		for ($cnt = 1; $cnt < strlen($text)-1; $cnt++) {
			// add a space after any full stops or comma's for readability
			// added as strip_tags was often leaving no spaces
			if (($text{$cnt} == '.') || ($text{$cnt} == ',')) {
				if ($text{$cnt+1} != ' ') {
					$text = substr_replace($text, ' ', $cnt + 1, 0);
				}
			}
		}
		return $text;
	}
   	
}
function keys( $text, $listexclude, $listgoldwords, $mindensity, $minlength, $maxwords )
{
	$text = clean( $text );
	// str_word_count 0 - cuenta la cantidad de palabras de la cadena
	$words_count=str_word_count($text);
    
	// str_word_count 1- formar un array con las palabras
	$array_words=array($text);

	// strlen &#8212; Obtiene la longitud de la cadena
	$v=strlen($text); 

	$frequency = array_count_values( str_word_count( $text, 1) );
	arsort ($frequency);
	$words = array();
	$countwords=0;
	foreach( $frequency as $F => $value )
	{
		// Creo que es la formula para la densidad de palabras...&#191; :) ?
		$density = round( ($value * 100) / ($words_count) );

		// trim &#8212; Elimina espacios en blanco (u otros caracteres) del principio y final de una cadena
		$frequent_word=trim($F);
		$excludewords = @explode ( ",",$listexclude);
		if ($frequent_word!=in_array($frequent_word,$excludewords) && !in_array($frequent_word,$words))
		{
   
			$includewords = @explode(",",$listgoldwords);
			//  Excluir palabras poco frecuentes, inferiores a un minimo e incluir las que esten en la lista gold
			if($value >=$mindensity || in_array($frequent_word,$includewords))
			{
	
				// Obtener la longitud de la cadena de frecuencia
				$long=strlen($F);
	
				//  Excluir palabras con longitud de frecuencia inferior al minimo e incluir las que esten en la lista gold
				if($long >= $minlength || in_array($frequent_word,$includewords))
				{
					$words[]=$frequent_word;
					$countwords++;
					if ($countwords>$maxwords)break;
				}
			}
		}
	}
	$keywordslist = implode(',',$words);
	return $keywordslist;
}
function clean($text)
{
	$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
	$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
	$text = preg_replace( '/<!--.+?-->/', '', $text );
	$text = preg_replace( '/{.+?}/', '', $text );
	$text = preg_replace( '/&nbsp;/', ' ', $text );
	$text = preg_replace( '/&amp;/', ' ', $text );
	$text = preg_replace( '/&quot;/', ' ', $text );
	$text = strip_tags( $text );
	$text = htmlspecialchars( $text );
	$text = str_replace(array("\r\n", "\r", "\n", "\t"), " ", $text);
	while (strchr($text,"  ")) {
		$text = str_replace("  ", " ",$text);
	}
	return $text;
}
