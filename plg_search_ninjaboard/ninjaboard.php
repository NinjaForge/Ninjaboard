<?php defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent( 'onSearch', 'plgSearchNinjaboard' );
$mainframe->registerEvent( 'onSearchAreas', 'plgSearchNinjaboardAreas' );

JPlugin::loadLanguage( 'plg_search_ninjaboard' );

/**
 * @return array An array of search areas
 */
function &plgSearchNinjaboardAreas()
{
	static $areas = array(
		'posts'  => 'Forum Posts'
	);
	return $areas;
}

/**
* Ninjaboard Search method
*
* The sql must return the following fields that are used in a common display
* routine: href, title, section, created, text, browsernav
* @param string Target search string
* @param string mathcing option, exact|any|all
* @param string ordering option, newest|oldest|popular|alpha|category
*/
function plgSearchNinjaboard( $text, $phrase = '', $ordering = '', $areas = null )
{
	if (is_array( $areas )) 
	{
		if (!array_intersect( $areas, array_keys( plgSearchNinjaboardAreas() ) )) {
			return array();
		}
	}

	// load plugin params info
 	$pluginParams = new JParameter( JPluginHelper::getPlugin('search', 'ninjaboard')->params );
	$limit = $pluginParams->def( 'search_limit', 50 );

	if(($text = trim($text)) == '') {
		return array();
	}

	$order = 'created_time';
	$direction = 'desc';
	switch ( $ordering ) 
	{
		case 'alpha':
			$order = 'subject';
			$direction = 'asc';
			break;

		case 'category':
			$order = array('forum', 'topic');
			$direction = 'desc';
			break;

		case 'popular':
			$order = array('hits', 'created_time');
			$direction = 'asc';
			break;
		case 'newest':
			$order = 'created_time';
			$direction = 'desc';
			break;
		case 'oldest':
			$order = 'created_time';
			$direction = 'asc';
	}


	$posts = KFactory::tmp('admin::com.ninjaboard.model.posts')
		->search($text)
		->limit($limit)
		->sort($order)
		->direction($direction)
		->getList();

	// Bit hackish. Don't blame me, com_search is a piece of M**bo crap
	$results = array();
	foreach($posts as $post)
	{
		$results[] = (object) array(
			//'href'			=> 'index.php?option=com_ninjaboard&view=post&id='.$post->id,
			'href'			=> 'index.php?option=com_ninjaboard&view=topic&id='.$post->topic.'&post='.$post->id.'#p'.$post->id.'',
			'title'			=> $post->subject,
			'created'		=> $post->created_on,
			'section'		=> $post->forum,
			'text'			=> KFactory::get('admin::com.ninja.helper.bbcode')->parse(array('text' => $post->text)),
			'browsernav'	=> 0
		);
	}
	return $results;
}
