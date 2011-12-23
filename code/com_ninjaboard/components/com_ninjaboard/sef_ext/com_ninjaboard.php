<?php
 
 /*

* @version		1.0.0

* @package		com_ninjaboard.php sh404SEF support for NinjaBoard component.

* @author 		NinjaForge

* @author email	support@ninjaforge.com

* @link			http://ninjaforge.com

* @copyright	Copyright (C) 2010 NinjaForge - All rights reserved.

* Last updated: 20th June, 2010 

*/
 

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------

global $sh_LANG;

$sefConfig = & shRouter::shGetConfig();  

$shLangName = '';

$shLangIso = '';

$title = array();

$shItemidString = '';

$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);

if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// ------------------  configuration options ---------------------------
$sh_insert_forum_title = true;  // set to true to insert forum title in URL i.e. http://www.yoursite.com/the_forum_title.html, false will result in http://www.yoursite.com/Forum.html?id=1
$sh_insert_topic_title = true;  // set to true to insert topic title in URL i.e. http://www.yoursite.com/the_topic_title.html, false will result http://www.yoursite.com/Topic.html?id=1
$sh_insert_forum_title_add = true;  // set to true to insert forum title in URL i.e. http://www.yoursite.com/the_forum_title.html, false will result http://www.yoursite.com/Add-Topic.html?id=1
$sh_insert_topic_title_add = true;  // set to true to insert topic title in URL i.e. http://www.yoursite.com/the_topic_title.html, false will result http://www.yoursite.com/Add-Post.html?id=1
$sh_insert_username = true;  // set to true to insert username in URL i.e. http://www.yoursite.com/the_username.html, false will result http://www.yoursite.com/User-Profile.html?id=1
// ------------------  configuration options ---------------------------

// ------------------  load language file - adjust as needed ----------------------------------------

include_once('language/com_ninjaboard.php'); 

// ------------------  load language file - adjust as needed ----------------------------------------

shRemoveFromGETVarsList('option');

shRemoveFromGETVarsList('lang');

// optional removal of limit and limitstart
if (!empty($limit))                       // use empty to test $limit as $limit is not allowed to be zero
shRemoveFromGETVarsList('limit');

if (isset($limitstart))                   // use isset to test $limitstart, as it can be zero
shRemoveFromGETVarsList('limitstart');  
  
// start NinjaBoard specific stuff
if (isset($controller)) 
shRemoveFromGETVarsList('controller');


// start NinjaBoard specific stuff
$view = isset($view) ? $view : null;

$id = isset($id) ? filter_var($id, FILTER_SANITIZE_NUMBER_INT) : null;

$forum = isset($forum) ? filter_var($forum, FILTER_SANITIZE_NUMBER_INT) : null;

$topic = isset($topic) ? filter_var($topic, FILTER_SANITIZE_NUMBER_INT) : null;

$Itemid = isset($Itemid) ? $Itemid : null;	


//insert name from menu
$shName = shGetComponentPrefix($option);

$shName = empty($shName) ? getMenuTitle($option, (isset($view) ? @$view : null), $Itemid ) : $shName;

if (!empty($shName) && $shName != '/') $title[] = $shName;

//now we can remove the Itemid
if (!empty($Itemid))
shRemoveFromGETVarsList('Itemid');

// fetch views
switch ($view)

{

	case 'forums':
	
		if (isset($view) && ($view == 'forums')) {
			$title[] = $sh_LANG[$shLangIso]['_COM_SEF_NB_FORUMS'];
			shRemoveFromGETVarsList('view');
		}
		
	break;
	
	
	case 'forum':
	
		if (isset($view) && ($view == 'forum')) {
			
			if ($sh_insert_forum_title){
			
				$query  = "SELECT ninjaboard_forum_id, title FROM #__ninjaboard_forums" ;
			
				$query .= "\n WHERE ninjaboard_forum_id=".$id;
				
				$database->setQuery( $query );
					
				if (shTranslateUrl($option, $shLangName))              
				
					$shForum = $database->loadObject( );
				
				  else $shForum = $database->loadObject(false);        
				  
				if ($shForum) {                                    
				
				  $title[] = $shForum->title;                    
				  
				  shRemoveFromGETVarsList('id');                
				
				}
			} else {
				
				$title[] = $sh_LANG[$shLangIso]['_COM_SEF_NB_FORUM'];
			
			}
			
			shRemoveFromGETVarsList('view');                
		}
		
	break;
	
	
	case 'topic':
	
		if (isset($view) && ($view == 'topic')) {
			
			if ($sh_insert_topic_title){

				$query  = 
				
				"SELECT nt.ninjaboard_topic_id, nt.first_post_id, np.ninjaboard_post_id, np.subject AS subject
				
				FROM #__ninjaboard_topics AS nt
				
				LEFT JOIN #__ninjaboard_posts AS np ON nt.first_post_id = np.ninjaboard_post_id
		  
				WHERE nt.ninjaboard_topic_id=".$id;
				
				$database->setQuery( $query );
					
				if (shTranslateUrl($option, $shLangName))              
				
					$shTopic = $database->loadObject( );
				
				  else $shTopic = $database->loadObject(false);        
				  
				if ($shTopic) {                                    
				
				  $title[] = $shTopic->subject;                    
				  
				  shRemoveFromGETVarsList('id');                
				
				}
			
			} else {
				
				$title[] = $sh_LANG[$shLangIso]['_COM_SEF_NB_TOPIC'];
			
			}
			
			shRemoveFromGETVarsList('view');    

		}	
	
	break;
		
	
	case 'post':
	
		if (isset($view) && ($view == 'post') && isset($forum)) {
			$title[] = $sh_LANG[$shLangIso]['_COM_SEF_NB_ADD_TOPIC'];
			
			if ($sh_insert_forum_title_add){
				
				if (isset($forum)) {
					
					$query  = "SELECT ninjaboard_forum_id, title FROM #__ninjaboard_forums" ;
				
					$query .= "\n WHERE ninjaboard_forum_id=".$forum;
					
					$database->setQuery( $query );
						
					if (shTranslateUrl($option, $shLangName))              
					
						$shForum = $database->loadObject( );
					
					  else $shForum = $database->loadObject(false);        
					  
					if ($shForum) {                                    
					
					  $title[] = $shForum->title;                    
					  
					  shRemoveFromGETVarsList('forum');                
					
					}
				}
			}
			shRemoveFromGETVarsList('view');

		}	
		
		if (isset($view) && ($view == 'post') && isset($topic)) {
			$title[] = $sh_LANG[$shLangIso]['_COM_SEF_NB_ADD_POST'];
			
			if ($sh_insert_topic_title_add){				
				
				if (isset($topic)) {
	
					$query  = 
					
					"SELECT nt.ninjaboard_topic_id, nt.first_post_id, np.ninjaboard_post_id, np.subject AS subject
					
					FROM #__ninjaboard_topics AS nt
					
					LEFT JOIN #__ninjaboard_posts AS np ON nt.first_post_id = np.ninjaboard_post_id
			  
					WHERE nt.ninjaboard_topic_id=".$topic;
					
					$database->setQuery( $query );
						
					if (shTranslateUrl($option, $shLangName))              
					
						$shTopic = $database->loadObject( );
					
					  else $shTopic = $database->loadObject(false);        
					  
					if ($shTopic) {                                    
					
					  $title[] = $shTopic->subject;                    
					  
					  shRemoveFromGETVarsList('topic');                
					
					}
				}
			}
			shRemoveFromGETVarsList('view');
		}
						
	break;	
	
	
	case 'users':
	
		if (isset($view) && ($view == 'users')) {
			$title[] = $sh_LANG[$shLangIso]['_COM_SEF_NB_USER'];
			
			if ($sh_insert_username){
				
				if (isset($id)) {
					
					$query  = "SELECT id, username FROM #__users" ;
				
					$query .= "\n WHERE id=".$id;
					
					$database->setQuery( $query );
						
					if (shTranslateUrl($option, $shLangName))              
					
						$shForum = $database->loadObject( );
					
					  else $shForum = $database->loadObject(false);        
					  
					if ($shForum) {                                    
					
					  $title[] = $shForum->username;                    
					  
					  shRemoveFromGETVarsList('id');                
					
					}
				}
			}
			shRemoveFromGETVarsList('view');
		}			
	break;
	
	
	case 'default':
		if (isset($view)) {
			$title[] = $sh_LANG[$shLangIso]['_COM_SEF_NB_FORUM'];
			shRemoveFromGETVarsList('view');
		}
}


// ------------------  standard plugin finalize function - don't change ---------------------------  

if ($dosef){

   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 

      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 

      (isset($shLangName) ? @$shLangName : null));

}      

// ------------------  standard plugin finalize function - don't change ---------------------------