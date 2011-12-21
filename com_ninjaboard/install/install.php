<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

// load the component language file
$language = &JFactory::getLanguage();
$language->load('com_ninjaboard');

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * SETUP DEFAULTS
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

// Insert a new installation record in the version log if no rows are present.
$db	= &JFactory::getDBO();

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$rows = 0;
?>
<!--<img src="components/com_ninjaboard/images/box.png" alt="<?php echo JText::_('NB_EXTENSION'); ?>" align="right" />-->

<h2><?php echo JText::_('NB_INSTALL_NAME'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('NB_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('NB_STATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'Ninjaboard Forum '.JText::_('NB_COMPONENT'); ?></td>
			<td><strong><?php echo JText::_('NB_INSTALLED'); ?></strong></td>
		</tr>
	</tbody>
</table>


<?php

function com_install()
{
    $db = &JFactory::getDBO();
	
//Do updates of table structure if we need to
//This is mainly for people doing an upgrade
$db->setQuery("SHOW FULL COLUMNS FROM #__ninjaboard_users where field = 'gender'");

$results = $db->loadObjectList();

if (!count($results))
{
	$db->setQuery("	ALTER TABLE `#__ninjaboard_users`
					ADD COLUMN `gender` tinyint(1) NOT NULL default '0',
					ADD COLUMN `show_gender` tinyint(1) NOT NULL default '0',
					ADD COLUMN `birthdate` datetime,
					ADD COLUMN `show_birthdate` tinyint(1) NOT NULL default '0';");
	$db->query();
}//if (!count($results))

$db->setQuery("SHOW FULL COLUMNS FROM #__ninjaboard_users where field = 'karma'");

$results = $db->loadObjectList();

if (!count($results))
{
	$db->setQuery("	ALTER TABLE `#__ninjaboard_users`
					DROP COLUMN `karma`,
					DROP COLUMN `bio`,
					DROP COLUMN `location`,
					DROP COLUMN `ICQ`,
					DROP COLUMN `AIM`,
					DROP COLUMN `YIM`,
					DROP COLUMN `Skype`,
					DROP COLUMN `MSNM`,
					DROP COLUMN `Gtalk`,
					DROP COLUMN `website_name`,
					DROP COLUMN `website_url`;");
	$db->query();
}//if (!count($results))
	
// First check if there are any records in the main tables
// If there are, then don't do any of the inserts  
// We will check a few because the user might have deleted records from one or more of the tables.      

    $db->setQuery("select count(*) from (SELECT 1 FROM #__ninjaboard_categories  
    			UNION SELECT 1 FROM #__ninjaboard_configs  
    			UNION SELECT 1 FROM #__ninjaboard_designs  
    			UNION SELECT 1 FROM #__ninjaboard_groups  
    			UNION SELECT 1 FROM #__ninjaboard_posts  
    			UNION SELECT 1 FROM #__ninjaboard_profiles_fields  
    			UNION SELECT 1 FROM #__ninjaboard_profiles_fields_lists_values) nbf");
    $counter = $db->loadResult();
    
//only do our inserts if there is no data in our tables - i.e. new install
    if ($counter == 0)
    {
			    
		$db->setQuery("INSERT INTO `#__ninjaboard_categories` (`id`,`name`,`published`,`ordering`,`checked_out`,`checked_out_time`) VALUES 
			(1,'".JText::_('Test Category')."',1,1,0,'0000-00-00 00:00:00')");
			
		$db->query();
			
		$db->setQuery("INSERT INTO `#__ninjaboard_configs` (`id`,`name`,`default_config`,`id_design`,`id_timezone`,`id_timeformat`,`editor`,`topic_icon_function`,`post_icon_function`,`board_settings`,`latestpost_settings`,`feed_settings`,`view_settings`,`view_footer_settings`,`user_settings_defaults`,`attachment_settings`,`avatar_settings`,`captcha_settings`,`checked_out`,`checked_out_time`) VALUES 
			(1,'".JText::_('Ninjaboard Standard Configuration')."',1,1,15,1,'ninjaboard','postPost','postPost','board_name=".JText::_('Ninjaboard - Joomla Bulletin Board')."\nbreadcrumb_index=".JText::_('Board Index')."\ndescription=Ninjaboard - Joomla Bulletin Board by Ninja Forge\nkeywords=Ninjaboard, Joomla Bulletin Board, Ninja Forge\npublished=1\nenable_terms=1\naccount_activation=1\nflood_interval=30\ntopics_per_page=10\nposts_per_page=20\nsearch_results_per_page=10\nitems_per_page=10\nbreadcrumb_max_length=50\nsession_time=5\nguest_time=30\nlatest_items_count=5\nlatest_items_type=0\nenable_bbcode=1\nenable_emoticons=1\nenable_guest_name=1\nguest_name_required=1','enable_filter=1\nlatest_post_hours=8,12\nlatest_post_days=1,2,3\nlatest_post_weeks=1\nlatest_post_months=1,6\nlatest_post_years=1','enable_feeds=1\nfeed_items_count=10\nfeed_items_type=0\nfeed_desc_trunk_size=0\nfeed_desc_html_syndicate=0\nfeed_image_title=".JText::_('Ninjaboard Logo')."\nfeed_image_url=http://www.ninjaboard.org/images/logo.png\nfeed_image_link=http://www.ninjaboard.org/\nfeed_image_description=".JText::_('This feed is provided by Ninjaboard. Please click to visit.')."\nfeed_image_desc_trunk_size=0\nfeed_image_desc_html_syndicate=0','show_latestitems=1\nshow_statistic=1\nshow_whosonline=1\nshow_legend=1\nshow_footer=1\nshow_user_as=0','show_myprofile=1\nshow_logout=1\nshow_login=1\nshow_register=1\nshow_search=1\nshow_latestposts=1\nshow_userlist=1\nshow_terms=1','role=1\nshow_email=0\nshow_online_state=1\nenable_bbcode=1\nenable_emoticons=1\nnotify_on_reply=0\ntime_zone=0.00\ntime_format=%m/%d/%Y %H:%M','enable_attachments=1','enable_avatars=1\nimage_resize=1\nimage_resize_quality=100\navatar_width=100\navatar_height=100\navatar_max_file_size=8192\navatar_path=media/ninjaboard/avatars\navatar_file_types=jpg,png,gif,bmp','captcha_edittopic=0\ncaptcha_deletetopic=0\ncaptcha_editpost=0\ncaptcha_deletepost=0\ncaptcha_login=0\ncaptcha_register=0\ncaptcha_search=0\ncaptcha_saveprofile=0\ncaptcha_requestlogin=0',62,'2008-12-15 20:59:44')");   
			
			$db->query();
			
			$db->setQuery("INSERT INTO `#__ninjaboard_designs` (`id`,`name`,`default_design`,`template`,`style`,`emoticon_set`,`icon_set`,`button_set`,`checked_out`,`checked_out_time`) VALUES 
			(1,'".JText::_('Ninjaboard - Default Design')."',1,'ninjaboard.xml','ninjaboard_blue.xml','ninjaboard_yellow.xml','ninjaboard.xml','ninjaboard_blue.xml',0,'0000-00-00 00:00:00')");   
			
			$db->query();
			
			$db->setQuery("INSERT INTO `#__ninjaboard_forums` (`id`,`name`,`description`,`state`,`locked`,`ordering`,`new_posts_time`,`posts`,`topics`,`auth_view`,`auth_read`,`auth_post`,`auth_reply`,`auth_edit`,`auth_delete`,`auth_reportpost`,`auth_sticky`,`auth_lock`,`auth_announce`,`auth_vote`,`auth_pollcreate`,`auth_attachments`,`checked_out`,`checked_out_time`,`id_cat`,`id_last_post`) VALUES 
			(1,'".JText::_('Test Forum')."','".JText::_('A forum for testing. It can be safely removed.')."',1,0,1,30,1,1,0,0,1,1,1,1,1,3,3,3,1,1,1,0,'0000-00-00 00:00:00',1,1)");   
			
			$db->query();

			$db->setQuery("INSERT INTO `#__ninjaboard_groups` (`id`,`name`,`description`,`published`,`role`,`checked_out`,`checked_out_time`) VALUES 
			(1,'".JText::_('NB_ADMINTITLE')."','".JText::_('NB_ADMINDESC')."',1,4,0,'0000-00-00 00:00:00'),
			(2,'".JText::_('NB_MODTITLE')."','".JText::_('NB_MODDESC')."',1,3,0,'0000-00-00 00:00:00'),
			(3,'".JText::_('NB_PRIVTITLE')."','".JText::_('NB_PRIVDESC')."',1,2,0,'0000-00-00 00:00:00'),
			(4,'".JText::_('NB_USERTITLE')."','".JText::_('NB_USERDESC')."',1,1,0,'0000-00-00 00:00:00')");   
						
			$db->query();
			
			 $db->setQuery("INSERT INTO `#__ninjaboard_posts` (`id`,`subject`,`text`,`date_post`,`date_last_edit`,`enable_bbcode`,`enable_emoticons`,`notify_on_reply`,`ip_poster`,`icon_function`,`id_topic`,`id_forum`,`id_user`) VALUES 
			(1,'".JText::_('Welcome to Ninjaboard')."','[b]".JText::_('Welcome to Ninjaboard')."[/b] :)','2007-08-13 22:00:00','0000-00-00 00:00:00',1,1,0,'127.0.0.1','postPost',1,1,0)");   
			
			$db->query();
			
			$db->setQuery("INSERT INTO `#__ninjaboard_posts_guests` (`id_post`,`guest_name`) VALUES 
			(1,'".JText::_('Ninjaboard Administrator')."')");   
			
			$db->query();			
			
			$db->setQuery("INSERT INTO `#__ninjaboard_profiles_fields` (`id`,`name`,`title`,`description`,`element`,`type`,`default`,`size`,`length`,`rows`,`columns`,`published`,`required`,`disabled`,`show_on_registration`,`ordering`,`checked_out`,`checked_out_time`,`id_profile_field_list`,`id_profile_field_set`) VALUES 
			 (1,'p_icq','".JText::_('ICQ Number')."','".JText::_('Please enter your Icq Number in this field')."',0,'varchar','',15,40,0,0,1,0,0,1,1,0,'0000-00-00 00:00:00',0,2),
			 (2,'p_aim','".JText::_('AIM Address')."','".JText::_('Please enter your AIM Address in this field')."',0,'varchar','',255,40,0,0,1,0,0,1,2,0,'0000-00-00 00:00:00',0,2),
			 (3,'p_msnm','".JText::_('MSN Messenger')."','".JText::_('Please enter your MSN Messenger username in this field')."',0,'varchar','',255,40,0,0,1,0,0,1,3,0,'0000-00-00 00:00:00',0,2),
			 (4,'p_yim','".JText::_('Yahoo Messenger')."','".JText::_('Please enter your Yahoo Messenger username in this field')."',0,'varchar','',255,40,0,0,1,0,0,1,4,0,'0000-00-00 00:00:00',0,2),
			 (5,'p_skype','".JText::_('Skype')."','".JText::_('Please enter your Skype username in this field')."',0,'varchar','',255,40,0,0,1,0,0,1,4,0,'0000-00-00 00:00:00',0,2),
			 (6,'p_gtalk','".JText::_('GTalk')."','".JText::_('Please enter your GTalk username in this field')."',0,'varchar','',255,40,0,0,1,0,0,1,4,0,'0000-00-00 00:00:00',0,2),
			 (7,'p_firstname','".JText::_('Firstname')."','".JText::_('Enter your firstname')."',0,'varchar','',100,40,0,0,1,1,0,1,1,0,'0000-00-00 00:00:00',0,1),
			 (8,'p_lastname','".JText::_('Lastname')."','".JText::_('Enter your lastname')."',0,'varchar','',100,40,0,0,1,1,0,1,2,0,'0000-00-00 00:00:00',0,1),
			 (9,'p_location','".JText::_('Location')."','".JText::_('Enter your location')."',1,'text','',3,0,5,40,1,0,0,1,3,0,'0000-00-00 00:00:00',2,1),
			 (10,'p_country','".JText::_('Country')."','".JText::_('Enter your country')."',5,'integer','0',3,0,0,0,1,0,0,1,7,0,'0000-00-00 00:00:00',2,1)");   
						
			$db->query();
			
			$db->setQuery("INSERT INTO `#__ninjaboard_profiles_fields_lists` (`id`,`name`,`published`,`checked_out`,`checked_out_time`) VALUES 
			(1,'".JText::_('Yes - No')."',1,0,'0000-00-00 00:00:00'),
			(2,'".JText::_('Country List')."',1,0,'0000-00-00 00:00:00')");   
			
			$db->query();
			
			$db->setQuery("INSERT INTO `#__ninjaboard_profiles_fields_lists_values` (`name`,`value`,`published`,`ordering`,`checked_out`,`checked_out_time`,`id_profile_field_list`) VALUES 
			('".JText::_('NB_YES')."','1',1,1,0,'0000-00-00 00:00:00',1),
			('".JText::_('NB_NO')."','0',1,2,0,'0000-00-00 00:00:00',1),
			('".JText::_('Abkhazia - Republic of Abkhazia')."','1',1,1,0,'0000-00-00 00:00:00',2),
			('".JText::_('Afghanistan - Islamic Republic of Afghanistan')."','2',1,2,0,'0000-00-00 00:00:00',2),
			('".JText::_('Akrotiri and Dhekelia - Sovereign Base Areas of Akrotiri and Dhekelia')."','3',1,3,0,'0000-00-00 00:00:00',2),
			('".JText::_('Aland - Aland Islands (Autonomous province of Finland)')."','4',1,4,0,'0000-00-00 00:00:00',2),
			('".JText::_('Albania - Republic of Albania')."','5',1,5,0,'0000-00-00 00:00:00',2),
			('".JText::_('Algeria - Peoples Democratic Republic of Algeria')."','6',1,6,0,'0000-00-00 00:00:00',2),
			('".JText::_('American Samoa - Territory of American Samoa')."','7',1,7,0,'0000-00-00 00:00:00',2),
			('".JText::_('Andorra - Principality of Andorra')."','8',1,8,0,'0000-00-00 00:00:00',2),
			('".JText::_('Angola - Republic of Angola')."','9',1,9,0,'0000-00-00 00:00:00',2),
			('".JText::_('Anguilla (UK overseas territory)')."','10',1,10,0,'0000-00-00 00:00:00',2),
			('".JText::_('Antigua and Barbuda')."','11',1,11,0,'0000-00-00 00:00:00',2),
			('".JText::_('Argentina - Argentine Republic')."','12',1,12,0,'0000-00-00 00:00:00',2),
			('".JText::_('Armenia - Republic of Armenia')."','13',1,13,0,'0000-00-00 00:00:00',2),
			('".JText::_('Aruba (Self-governing country in the Kingdom of the Netherlands)')."','14',1,14,0,'0000-00-00 00:00:00',2),
			('".JText::_('Ascension Island (Dependency of the UK overseas territory of Saint Helena)')."','15',1,15,0,'0000-00-00 00:00:00',2),
			('".JText::_('Australia - Commonwealth of Australia')."','16',1,16,0,'0000-00-00 00:00:00',2),
			('".JText::_('Austria - Republic of Austria')."','17',1,17,0,'0000-00-00 00:00:00',2),
			('".JText::_('Azerbaijan - Republic of Azerbaijan')."','18',1,18,0,'0000-00-00 00:00:00',2),
			('".JText::_('Bahamas, The - Commonwealth of The Bahamas')."','19',1,19,0,'0000-00-00 00:00:00',2),
			('".JText::_('Bahrain - Kingdom of Bahrain')."','20',1,20,0,'0000-00-00 00:00:00',2),
			('".JText::_('Bangladesh - Peoples Republic of Bangladesh')."','21',1,21,0,'0000-00-00 00:00:00',2),
			('".JText::_('Barbados')."','22',1,22,0,'0000-00-00 00:00:00',2),
			('".JText::_('Belarus - Republic of Belarus')."','23',1,23,0,'0000-00-00 00:00:00',2),
			('".JText::_('Belgium - Kingdom of Belgium')."','24',1,24,0,'0000-00-00 00:00:00',2),
			('".JText::_('Belize')."','25',1,25,0,'0000-00-00 00:00:00',2),
			('".JText::_('Benin - Republic of Benin')."','26',1,26,0,'0000-00-00 00:00:00',2),
			('".JText::_('Bermuda (UK overseas territory)')."','27',1,27,0,'0000-00-00 00:00:00',2),
			('".JText::_('Bhutan - Kingdom of Bhutan')."','28',1,28,0,'0000-00-00 00:00:00',2),
			('".JText::_('Bolivia - Republic of Bolivia')."','29',1,29,0,'0000-00-00 00:00:00',2),
			('".JText::_('Bosnia and Herzegovina')."','30',1,30,0,'0000-00-00 00:00:00',2),
			('".JText::_('Botswana - Republic of Botswana')."','31',1,31,0,'0000-00-00 00:00:00',2),
			('".JText::_('Brazil - Federative Republic of Brazil')."','32',1,32,0,'0000-00-00 00:00:00',2),
			('".JText::_('Brunei - Negara Brunei Darussalam')."','33',1,33,0,'0000-00-00 00:00:00',2),
			('".JText::_('Bulgaria - Republic of Bulgaria')."','34',1,34,0,'0000-00-00 00:00:00',2),
			('".JText::_('Burkina Faso')."','35',1,35,0,'0000-00-00 00:00:00',2),
			('".JText::_('Burundi - Republic of Burundi')."','36',1,36,0,'0000-00-00 00:00:00',2),
			('".JText::_('Cambodia - Kingdom of Cambodia')."','37',1,37,0,'0000-00-00 00:00:00',2),
			('".JText::_('Cameroon - Republic of Cameroon')."','38',1,38,0,'0000-00-00 00:00:00',2),
			('".JText::_('Canada')."','39',1,39,0,'0000-00-00 00:00:00',2),
			('".JText::_('Cape Verde - Republic of Cape Verde')."','40',1,40,0,'0000-00-00 00:00:00',2),
			('".JText::_('Cayman Islands (UK overseas territory)')."','41',1,41,0,'0000-00-00 00:00:00',2),
			('".JText::_('Central African Republic')."','42',1,42,0,'0000-00-00 00:00:00',2),
			('".JText::_('Chad - Republic of Chad')."','43',1,43,0,'0000-00-00 00:00:00',2),
			('".JText::_('Chile - Republic of Chile')."','44',1,44,0,'0000-00-00 00:00:00',2),
			('".JText::_('China - Peoples Republic of China')."','45',1,45,0,'0000-00-00 00:00:00',2),
			('".JText::_('Christmas Island - Territory of Christmas Island')."','46',1,46,0,'0000-00-00 00:00:00',2),
			('".JText::_('Cocos (Keeling) Islands - Territory of Cocos (Keeling) Islands')."','47',1,47,0,'0000-00-00 00:00:00',2),
			('".JText::_('Colombia - Republic of Colombia')."','48',1,48,0,'0000-00-00 00:00:00',2),
			('".JText::_('Comoros - Union of the Comoros')."','49',1,49,0,'0000-00-00 00:00:00',2),
			('".JText::_('Congo - Democratic Republic of the Congo')."','50',1,50,0,'0000-00-00 00:00:00',2),
			('".JText::_('Congo - Republic of the Congo')."','51',1,51,0,'0000-00-00 00:00:00',2),
			('".JText::_('Cook Islands (Associated state of New Zealand)')."','52',1,52,0,'0000-00-00 00:00:00',2),
			('".JText::_('Costa Rica - Republic of Costa Rica')."','53',1,53,0,'0000-00-00 00:00:00',2),
			('".JText::_('Cote dIvoire - Republic of Cote dIvoire')."','54',1,54,0,'0000-00-00 00:00:00',2),
			('".JText::_('Croatia - Republic of Croatia')."','55',1,55,0,'0000-00-00 00:00:00',2),
			('".JText::_('Cuba - Republic of Cuba')."','56',1,56,0,'0000-00-00 00:00:00',2),
			('".JText::_('Cyprus - Republic of Cyprus')."','57',1,57,0,'0000-00-00 00:00:00',2),
			('".JText::_('Czech Republic')."','58',1,58,0,'0000-00-00 00:00:00',2),
			('".JText::_('Denmark - Kingdom of Denmark')."','59',1,59,0,'0000-00-00 00:00:00',2),
			('".JText::_('Djibouti - Republic of Djibouti')."','60',1,60,0,'0000-00-00 00:00:00',2),
			('".JText::_('Dominica - Commonwealth of Dominica')."','61',1,61,0,'0000-00-00 00:00:00',2),
			('".JText::_('Dominican Republic')."','62',1,62,0,'0000-00-00 00:00:00',2),
			('".JText::_('East Timor - Democratic Republic of Timor-Leste')."','63',1,63,0,'0000-00-00 00:00:00',2),
			('".JText::_('Ecuador - Republic of Ecuador')."','64',1,64,0,'0000-00-00 00:00:00',2),
			('".JText::_('Egypt - Arab Republic of Egypt')."','65',1,65,0,'0000-00-00 00:00:00',2),
			('".JText::_('El Salvador - Republic of El Salvador')."','66',1,66,0,'0000-00-00 00:00:00',2),
			('".JText::_('Equatorial Guinea - Republic of Equatorial Guinea')."','67',1,67,0,'0000-00-00 00:00:00',2),
			('".JText::_('Eritrea - State of Eritrea')."','68',1,68,0,'0000-00-00 00:00:00',2),
			('".JText::_('Estonia - Republic of Estonia')."','69',1,69,0,'0000-00-00 00:00:00',2),
			('".JText::_('Ethiopia - Federal Democratic Republic of Ethiopia')."','70',1,70,0,'0000-00-00 00:00:00',2),
			('".JText::_('Falkland Islands (UK overseas territory)')."','71',1,71,0,'0000-00-00 00:00:00',2),
			('".JText::_('Faroe Islands (Self-governing country in the Kingdom of Denmark)')."','72',1,72,0,'0000-00-00 00:00:00',2),
			('".JText::_('Fiji - Republic of the Fiji Islands')."','73',1,73,0,'0000-00-00 00:00:00',2),
			('".JText::_('Finland - Republic of Finland')."','74',1,74,0,'0000-00-00 00:00:00',2),
			('".JText::_('France - French Republic')."','75',1,75,0,'0000-00-00 00:00:00',2),
			('".JText::_('French Polynesia (French overseas collectivity)')."','76',1,76,0,'0000-00-00 00:00:00',2),
			('".JText::_('Gabon - Gabonese Republic')."','77',1,77,0,'0000-00-00 00:00:00',2),
			('".JText::_('Gambia, The - Republic of The Gambia')."','78',1,78,0,'0000-00-00 00:00:00',2),
			('".JText::_('Georgia')."','79',1,79,0,'0000-00-00 00:00:00',2),
			('".JText::_('Germany - Federal Republic of Germany')."','80',1,80,0,'0000-00-00 00:00:00',2),
			('".JText::_('Ghana - Republic of Ghana')."','81',1,81,0,'0000-00-00 00:00:00',2),
			('".JText::_('Gibraltar (UK overseas territory)')."','82',1,82,0,'0000-00-00 00:00:00',2),
			('".JText::_('Greece - Hellenic Republic')."','83',1,83,0,'0000-00-00 00:00:00',2),
			('".JText::_('Greenland (Self-governing country in the Kingdom of Denmark)')."','84',1,84,0,'0000-00-00 00:00:00',2),
			('".JText::_('Grenada')."','85',1,85,0,'0000-00-00 00:00:00',2),
			('".JText::_('Guam - Territory of Guam (US organized territory)')."','86',1,86,0,'0000-00-00 00:00:00',2),
			('".JText::_('Guatemala - Republic of Guatemala')."','87',1,87,0,'0000-00-00 00:00:00',2),
			('".JText::_('Guernsey - Bailiwick of Guernsey (British Crown dependency)')."','88',1,88,0,'0000-00-00 00:00:00',2),
			('".JText::_('Guinea - Republic of Guinea')."','89',1,89,0,'0000-00-00 00:00:00',2),
			('".JText::_('Guinea-Bissau - Republic of Guinea-Bissau')."','90',1,90,0,'0000-00-00 00:00:00',2),
			('".JText::_('Guyana - Co-operative Republic of Guyana')."','91',1,91,0,'0000-00-00 00:00:00',2),
			('".JText::_('Haiti - Republic of Haiti')."','92',1,92,0,'0000-00-00 00:00:00',2),
			('".JText::_('Honduras - Republic of Honduras')."','93',1,93,0,'0000-00-00 00:00:00',2),
			('".JText::_('Hong Kong - Hong Kong Special Administrative Region of the Peoples Republic of China')."','94',1,94,0,'0000-00-00 00:00:00',2),
			('".JText::_('Hungary - Republic of Hungary')."','95',1,95,0,'0000-00-00 00:00:00',2),
			('".JText::_('Iceland - Republic of Iceland')."','96',1,96,0,'0000-00-00 00:00:00',2),
			('".JText::_('India - Republic of India')."','97',1,97,0,'0000-00-00 00:00:00',2),
			('".JText::_('Indonesia - Republic of Indonesia')."','98',1,98,0,'0000-00-00 00:00:00',2),
			('".JText::_('Iran - Islamic Republic of Iran')."','99',1,99,0,'0000-00-00 00:00:00',2),
			('".JText::_('Iraq - Republic of Iraq')."','100',1,100,0,'0000-00-00 00:00:00',2),
			('".JText::_('Ireland - Republic of Ireland')."','101',1,101,0,'0000-00-00 00:00:00',2),
			('".JText::_('Isle of Man (British Crown dependency)')."','102',1,102,0,'0000-00-00 00:00:00',2),
			('".JText::_('Israel - State of Israel')."','103',1,103,0,'0000-00-00 00:00:00',2),
			('".JText::_('Italy - Italian Republic')."','104',1,104,0,'0000-00-00 00:00:00',2),
			('".JText::_('Jamaica')."','105',1,105,0,'0000-00-00 00:00:00',2),
			('".JText::_('Japan')."','106',1,106,0,'0000-00-00 00:00:00',2),
			('".JText::_('Jersey - Bailiwick of Jersey (British Crown dependency)')."','107',1,107,0,'0000-00-00 00:00:00',2),
			('".JText::_('Jordan - Hashemite Kingdom of Jordan')."','108',1,108,0,'0000-00-00 00:00:00',2),
			('".JText::_('Kazakhstan - Republic of Kazakhstan')."','109',1,109,0,'0000-00-00 00:00:00',2),
			('".JText::_('Kenya - Republic of Kenya')."','110',1,110,0,'0000-00-00 00:00:00',2),
			('".JText::_('Kiribati - Republic of Kiribati')."','111',1,111,0,'0000-00-00 00:00:00',2),
			('".JText::_('Korea, North - Democratic Peoples Republic of Korea')."','112',1,112,0,'0000-00-00 00:00:00',2),
			('".JText::_('Korea, South - Republic of Korea')."','113',1,113,0,'0000-00-00 00:00:00',2),
			('".JText::_('Kosovo')."','114',1,114,0,'0000-00-00 00:00:00',2),
			('".JText::_('Kuwait - State of Kuwait')."','115',1,115,0,'0000-00-00 00:00:00',2),
			('".JText::_('Kyrgyzstan - Kyrgyz Republic')."','116',1,116,0,'0000-00-00 00:00:00',2),
			('".JText::_('Laos - Lao Peoples Democratic Republic')."','117',1,117,0,'0000-00-00 00:00:00',2),
			('".JText::_('Latvia - Republic of Latvia')."','118',1,118,0,'0000-00-00 00:00:00',2),
			('".JText::_('Lebanon - Republic of Lebanon')."','119',1,119,0,'0000-00-00 00:00:00',2),
			('".JText::_('Lesotho - Kingdom of Lesotho')."','120',1,120,0,'0000-00-00 00:00:00',2),
			('".JText::_('Liberia - Republic of Liberia')."','121',1,121,0,'0000-00-00 00:00:00',2),
			('".JText::_('Libya - Great Socialist Peoples Libyan Arab Jamahiriya')."','122',1,122,0,'0000-00-00 00:00:00',2),
			('".JText::_('Liechtenstein - Principality of Liechtenstein')."','123',1,123,0,'0000-00-00 00:00:00',2),
			('".JText::_('Lithuania - Republic of Lithuania')."','124',1,124,0,'0000-00-00 00:00:00',2),
			('".JText::_('Luxembourg - Grand Duchy of Luxembourg')."','125',1,125,0,'0000-00-00 00:00:00',2),
			('".JText::_('Macao - Macao Special Administrative Region of the Peoples Republic of China')."','126',1,126,0,'0000-00-00 00:00:00',2),
			('".JText::_('Macedonia - Republic of Macedonia')."','127',1,127,0,'0000-00-00 00:00:00',2),
			('".JText::_('Madagascar - Republic of Madagascar')."','128',1,128,0,'0000-00-00 00:00:00',2),
			('".JText::_('Malawi - Republic of Malawi')."','129',1,129,0,'0000-00-00 00:00:00',2),
			('".JText::_('Malaysia')."','130',1,130,0,'0000-00-00 00:00:00',2),
			('".JText::_('Maldives - Republic of Maldives')."','131',1,131,0,'0000-00-00 00:00:00',2),
			('".JText::_('Mali - Republic of Mali')."','132',1,132,0,'0000-00-00 00:00:00',2),
			('".JText::_('Malta - Republic of Malta')."','133',1,133,0,'0000-00-00 00:00:00',2),
			('".JText::_('Marshall Islands - Republic of the Marshall Islands')."','134',1,134,0,'0000-00-00 00:00:00',2),
			('".JText::_('Mauritania - Islamic Republic of Mauritania')."','135',1,135,0,'0000-00-00 00:00:00',2),
			('".JText::_('Mauritius - Republic of Mauritius')."','136',1,136,0,'0000-00-00 00:00:00',2),
			('".JText::_('Mayotte - Departmental Collectivity of Mayotte (French overseas collectivity)')."','137',1,137,0,'0000-00-00 00:00:00',2),
			('".JText::_('Mexico - United Mexican States')."','138',1,138,0,'0000-00-00 00:00:00',2),
			('".JText::_('Micronesia - Federated States of Micronesia')."','139',1,139,0,'0000-00-00 00:00:00',2),
			('".JText::_('Moldova - Republic of Moldova')."','140',1,140,0,'0000-00-00 00:00:00',2),
			('".JText::_('Monaco - Principality of Monaco')."','141',1,141,0,'0000-00-00 00:00:00',2),
			('".JText::_('Mongolia')."','142',1,142,0,'0000-00-00 00:00:00',2),
			('".JText::_('Montenegro - Republic of Montenegro')."','143',1,143,0,'0000-00-00 00:00:00',2),
			('".JText::_('Montserrat (UK overseas territory)')."','144',1,144,0,'0000-00-00 00:00:00',2),
			('".JText::_('Morocco - Kingdom of Morocco')."','145',1,145,0,'0000-00-00 00:00:00',2),
			('".JText::_('Mozambique - Republic of Mozambique')."','146',1,146,0,'0000-00-00 00:00:00',2),
			('".JText::_('Myanmar - Union of Myanmar')."','147',1,147,0,'0000-00-00 00:00:00',2),
			('".JText::_('Nagorno-Karabakh - Nagorno-Karabakh Republic')."','148',1,148,0,'0000-00-00 00:00:00',2),
			('".JText::_('Namibia - Republic of Namibia')."','149',1,149,0,'0000-00-00 00:00:00',2),
			('".JText::_('Nauru - Republic of Nauru')."','150',1,150,0,'0000-00-00 00:00:00',2),
			('".JText::_('Nepal - State of Nepal')."','151',1,151,0,'0000-00-00 00:00:00',2),
			('".JText::_('Netherlands - Kingdom of the Netherlands')."','152',1,152,0,'0000-00-00 00:00:00',2),
			('".JText::_('Netherlands Antilles (Self-governing country in the Kingdom of the Netherlands)')."','153',1,153,0,'0000-00-00 00:00:00',2),
			('".JText::_('New Caledonia - Territory of New Caledonia and Dependencies (French community sui generis)')."','154',1,154,0,'0000-00-00 00:00:00',2),
			('".JText::_('New Zealand')."','155',1,155,0,'0000-00-00 00:00:00',2),
			('".JText::_('Nicaragua - Republic of Nicaragua')."','156',1,156,0,'0000-00-00 00:00:00',2),
			('".JText::_('Niger - Republic of Niger')."','157',1,157,0,'0000-00-00 00:00:00',2),
			('".JText::_('Nigeria - Federal Republic of Nigeria')."','158',1,158,0,'0000-00-00 00:00:00',2),
			('".JText::_('Niue (Associated state of New Zealand)')."','159',1,159,0,'0000-00-00 00:00:00',2),
			('".JText::_('Norfolk Island - Territory of Norfolk Island (Australian overseas territory)')."','160',1,160,0,'0000-00-00 00:00:00',2),
			('".JText::_('Northern Cyprus - Turkish Republic of Northern Cyprus')."','161',1,161,0,'0000-00-00 00:00:00',2),
			('".JText::_('Northern Mariana Islands - Commonwealth of the Northern Mariana Islands (US commonwealth)')."','162',1,162,0,'0000-00-00 00:00:00',2),
			('".JText::_('Norway - Kingdom of Norway')."','163',1,163,0,'0000-00-00 00:00:00',2),
			('".JText::_('Oman - Sultanate of Oman')."','164',1,164,0,'0000-00-00 00:00:00',2),
			('".JText::_('Pakistan - Islamic Republic of Pakistan')."','165',1,165,0,'0000-00-00 00:00:00',2),
			('".JText::_('Palau - Republic of Palau')."','166',1,166,0,'0000-00-00 00:00:00',2),
			('".JText::_('Palestine - Occupied Palestinian Territories')."','167',1,167,0,'0000-00-00 00:00:00',2),
			('".JText::_('Panama - Republic of Panama')."','168',1,168,0,'0000-00-00 00:00:00',2),
			('".JText::_('Papua New Guinea - Independent State of Papua New Guinea')."','169',1,169,0,'0000-00-00 00:00:00',2),
			('".JText::_('Paraguay - Republic of Paraguay')."','170',1,170,0,'0000-00-00 00:00:00',2),
			('".JText::_('Peru - Republic of Peru')."','171',1,171,0,'0000-00-00 00:00:00',2),
			('".JText::_('Philippines - Republic of the Philippines')."','172',1,172,0,'0000-00-00 00:00:00',2),
			('".JText::_('Pitcairn Islands - Pitcairn, Henderson, Ducie, and Oeno Islands (UK overseas territory)')."','173',1,173,0,'0000-00-00 00:00:00',2),
			('".JText::_('Poland - Republic of Poland')."','174',1,174,0,'0000-00-00 00:00:00',2),
			('".JText::_('Portugal - Portuguese Republic')."','175',1,175,0,'0000-00-00 00:00:00',2),
			('".JText::_('Puerto Rico - Commonwealth of Puerto Rico (US commonwealth)')."','176',1,176,0,'0000-00-00 00:00:00',2),
			('".JText::_('Qatar - State of Qatar')."','177',1,177,0,'0000-00-00 00:00:00',2),
			('".JText::_('Romania')."','178',1,178,0,'0000-00-00 00:00:00',2),
			('".JText::_('Russia - Russian Federation')."','179',1,179,0,'0000-00-00 00:00:00',2),
			('".JText::_('Rwanda - Republic of Rwanda')."','180',1,180,0,'0000-00-00 00:00:00',2),
			('".JText::_('Saint Barthelemy - Collectivity of Saint Barthelemy (French overseas collectivity)')."','181',1,181,0,'0000-00-00 00:00:00',2),
			('".JText::_('Saint Helena (UK overseas territory)')."','182',1,182,0,'0000-00-00 00:00:00',2),
			('".JText::_('Saint Kitts and Nevis - Federation of Saint Christopher and Nevis')."','183',1,183,0,'0000-00-00 00:00:00',2),
			('".JText::_('Saint Lucia')."','184',1,184,0,'0000-00-00 00:00:00',2),
			('".JText::_('Saint Martin - Collectivity of Saint Martin (French overseas collectivity)')."','185',1,185,0,'0000-00-00 00:00:00',2), 
			('".JText::_('Saint Pierre and Miquelon - Territorial Collectivity of Saint Pierre and Miquelon')."','186',1,186,0,'0000-00-00 00:00:00',2),
			('".JText::_('Saint Vincent and the Grenadines')."','187',1,187,0,'0000-00-00 00:00:00',2),
			('".JText::_('Samoa - Independent State of Samoa')."','188',1,188,0,'0000-00-00 00:00:00',2),
			('".JText::_('San Marino - Most Serene Republic of San Marino')."','189',1,189,0,'0000-00-00 00:00:00',2),
			('".JText::_('Sao Tome and Principe - Democratic Republic of Sao Tome and Principe')."','190',1,190,0,'0000-00-00 00:00:00',2),
			('".JText::_('Saudi Arabia - Kingdom of Saudi Arabia')."','191',1,191,0,'0000-00-00 00:00:00',2),
			('".JText::_('Senegal - Republic of Senegal')."','192',1,192,0,'0000-00-00 00:00:00',2),
			('".JText::_('Serbia - Republic of Serbia')."','193',1,193,0,'0000-00-00 00:00:00',2),
			('".JText::_('Seychelles - Republic of Seychelles')."','194',1,194,0,'0000-00-00 00:00:00',2),
			('".JText::_('Sierra Leone - Republic of Sierra Leone')."','195',1,195,0,'0000-00-00 00:00:00',2),
			('".JText::_('Singapore - Republic of Singapore')."','196',1,196,0,'0000-00-00 00:00:00',2),
			('".JText::_('Slovakia - Slovak Republic')."','197',1,197,0,'0000-00-00 00:00:00',2),
			('".JText::_('Slovenia - Republic of Slovenia')."','198',1,198,0,'0000-00-00 00:00:00',2),
			('".JText::_('Solomon Islands')."','199',1,199,0,'0000-00-00 00:00:00',2),
			('".JText::_('Somalia')."','200',1,200,0,'0000-00-00 00:00:00',2),
			('".JText::_('South Africa - Republic of South Africa')."','201',1,201,0,'0000-00-00 00:00:00',2),
			('".JText::_('South Ossetia - Republic of South Ossetia')."','202',1,202,0,'0000-00-00 00:00:00',2),
			('".JText::_('Spain - Kingdom of Spain')."','203',1,203,0,'0000-00-00 00:00:00',2),
			('".JText::_('Sri Lanka - Democratic Socialist Republic of Sri Lanka')."','204',1,204,0,'0000-00-00 00:00:00',2),
			('".JText::_('Sudan - Republic of the Sudan')."','205',1,205,0,'0000-00-00 00:00:00',2),
			('".JText::_('Suriname - Republic of Suriname')."','206',1,206,0,'0000-00-00 00:00:00',2),
			('".JText::_('Svalbard (Territory of Norway)')."','207',1,207,0,'0000-00-00 00:00:00',2),
			('".JText::_('Swaziland - Kingdom of Swaziland')."','208',1,208,0,'0000-00-00 00:00:00',2),
			('".JText::_('Sweden - Kingdom of Sweden')."','209',1,209,0,'0000-00-00 00:00:00',2),
			('".JText::_('Switzerland - Swiss Confederation')."','210',1,210,0,'0000-00-00 00:00:00',2),
			('".JText::_('Syria - Syrian Arab Republic')."','211',1,211,0,'0000-00-00 00:00:00',2),
			('".JText::_('Taiwan - Republic of China')."','212',1,212,0,'0000-00-00 00:00:00',2),
			('".JText::_('Tajikistan - Republic of Tajikistan')."','213',1,213,0,'0000-00-00 00:00:00',2),
			('".JText::_('Tanzania - United Republic of Tanzania')."','214',1,214,0,'0000-00-00 00:00:00',2),
			('".JText::_('Thailand - Kingdom of Thailand')."','215',1,215,0,'0000-00-00 00:00:00',2),
			('".JText::_('Togo - Togolese Republic')."','216',1,216,0,'0000-00-00 00:00:00',2),
			('".JText::_('Tokelau (Overseas territory of New Zealand)')."','217',1,217,0,'0000-00-00 00:00:00',2),
			('".JText::_('Tonga - Kingdom of Tonga')."','218',1,218,0,'0000-00-00 00:00:00',2),
			('".JText::_('Transnistria - Transnistrian Moldovan Republic')."','219',1,219,0,'0000-00-00 00:00:00',2),
			('".JText::_('Trinidad and Tobago - Republic of Trinidad and Tobago')."','220',1,220,0,'0000-00-00 00:00:00',2),
			('".JText::_('Tristan da Cunha (Dependency of the UK overseas territory of Saint Helena)')."','221',1,221,0,'0000-00-00 00:00:00',2),
			('".JText::_('Tunisia - Tunisian Republic')."','222',1,222,0,'0000-00-00 00:00:00',2),
			('".JText::_('Turkey - Republic of Turkey')."','223',1,223,0,'0000-00-00 00:00:00',2),
			('".JText::_('Turkmenistan')."','224',1,224,0,'0000-00-00 00:00:00',2),
			('".JText::_('Turks and Caicos Islands (UK overseas territory)')."','225',1,225,0,'0000-00-00 00:00:00',2),
			('".JText::_('Tuvalu')."','226',1,226,0,'0000-00-00 00:00:00',2),
			('".JText::_('Uganda - Republic of Uganda')."','227',1,227,0,'0000-00-00 00:00:00',2),
			('".JText::_('Ukraine')."','228',1,228,0,'0000-00-00 00:00:00',2),
			('".JText::_('United Arab Emirates')."','229',1,229,0,'0000-00-00 00:00:00',2),
			('".JText::_('United Kingdom - United Kingdom of Great Britain and Northern Ireland')."','230',1,230,0,'0000-00-00 00:00:00',2),
			('".JText::_('United States - United States of America')."','231',1,231,0,'0000-00-00 00:00:00',2), 
			('".JText::_('Uruguay - Eastern Republic of Uruguay')."','232',1,232,0,'0000-00-00 00:00:00',2),
			('".JText::_('Uzbekistan - Republic of Uzbekistan')."','233',1,233,0,'0000-00-00 00:00:00',2),
			('".JText::_('Vanuatu - Republic of Vanuatu')."','234',1,234,0,'0000-00-00 00:00:00',2),
			('".JText::_('Vatican City - State of the Vatican City')."','235',1,235,0,'0000-00-00 00:00:00',2),
			('".JText::_('Venezuela - Bolivarian Republic of Venezuela')."','236',1,236,0,'0000-00-00 00:00:00',2),
			('".JText::_('Vietnam - Socialist Republic of Vietnam')."','237',1,237,0,'0000-00-00 00:00:00',2),
			('".JText::_('Virgin Islands, British - British Virgin Islands (UK overseas territory)')."','238',1,238,0,'0000-00-00 00:00:00',2),
			('".JText::_('Virgin Islands, United States - United States Virgin Islands (US organized territory)')."','239',1,239,0,'0000-00-00 00:00:00',2),
			('".JText::_('Wallis and Futuna - Territory of Wallis and Futuna Islands (French overseas collectivity)')."','240',1,240,0,'0000-00-00 00:00:00',2),
			('".JText::_('Western Sahara')."','241',1,241,0,'0000-00-00 00:00:00',2),
			('".JText::_('Yemen - Republic of Yemen')."','242',1,242,0,'0000-00-00 00:00:00',2),
			('".JText::_('Zambia - Republic of Zambia')."','243',1,243,0,'0000-00-00 00:00:00',2),
			('".JText::_('Zimbabwe - Republic of Zimbabwe')."','244',1,244,0,'0000-00-00 00:00:00',2)");   
			
			$db->query();

			
			
			$db->setQuery("INSERT INTO `#__ninjaboard_profiles_fields_sets` (`id`,`name`,`published`,`ordering`,`checked_out`,`checked_out_time`) VALUES
			(1,'Profile Personal Data',1,1,0,'0000-00-00 00:00:00'),
			(2,'Profile Information',1,2,0,'0000-00-00 00:00:00')");   
			
			$db->query();
			
			$db->setQuery("INSERT INTO `#__ninjaboard_ranks` (`id`,`name`,`description`,`min_posts`,`published`,`rank_file`,`checked_out`,`checked_out_time`) VALUES
			(1,'".JText::_('NB_RANKNEWBIENAME')."','".JText::_('NB_RANKNEWBIEDESC')."',0,1,'stars_1.gif',0,'0000-00-00 00:00:00'),
			(2,'".JText::_('NB_RANKUSERNAME')."','".JText::_('NB_RANKUSERDESC')."',25,1,'stars_2.gif',0,'0000-00-00 00:00:00'),
			(3,'".JText::_('NB_RANKEXPNAME')."','".JText::_('NB_RANKEXPDESC')."',50,1,'stars_3.gif',0,'0000-00-00 00:00:00'),
			(4,'".JText::_('NB_RANKHERONAME')."','".JText::_('NB_RANKHERODESC')."',100,1,'stars_4.gif',0,'0000-00-00 00:00:00'),
			(5,'".JText::_('NB_RANKMASTERNAME')."','".JText::_('NB_RANKMASTERDESC')."',200,1,'stars_5.gif',0,'0000-00-00 00:00:00')");   
			
			$db->query();
			
			
			
			 $db->setQuery("INSERT INTO `#__ninjaboard_terms` (`id`,`terms`,`termstext`,`agreement`,`agreementtext`,`locale`,`published`,`checked_out`,`checked_out_time`) VALUES  
			(1,'".JText::_('Terms of Agreement')."','".JText::_('NB_TERMSTEXT')."','".JText::_('NB_TERMSAGREE')."','".JText::_('NB_TERMSAGREETEXT')."','".JText::_('NB_TERMSLANG')."',1,0,'0000-00-00 00:00:00')");   
			
			$db->query();
			
			
			 $db->setQuery("INSERT INTO `#__ninjaboard_timeformats` (`id`,`name`,`timeformat`,`published`,`checked_out`,`checked_out_time`) VALUES 
			 (1,'m/d/Y H:M','%m/%d/%Y %H:%M',1,0,'0000-00-00 00:00:00'),
			 (2,'m/d/Y','%m/%d/%Y',1,0,'0000-00-00 00:00:00'),
			 (3,'d/m/Y H:M','%d/%m/%Y %H:%M',1,0,'0000-00-00 00:00:00'),
			 (4,'d/m/Y','%d/%m/%Y',1,0,'0000-00-00 00:00:00'),
			 (5,'d.m.Y H:M','%d.%m.%Y %H:%M',1,0,'0000-00-00 00:00:00'),
			 (6,'d.m.Y','%d.%m.%Y',1,0,'0000-00-00 00:00:00'),
			 (7,'b, d.Y H:M','%b, %d.%Y %H:%M',1,0,'0000-00-00 00:00:00'),
			 (8,'b, d.Y','%b, %d.%Y',1,0,'0000-00-00 00:00:00'),
			 (9,'A,  d.B Y H:M','%A,  %d.%B %Y %H:%M',1,0,'0000-00-00 00:00:00'),
			 (10,'A,  d.B Y','%A,  %d.%B %Y',1,0,'0000-00-00 00:00:00')");   
			 
			 $db->query();
			
			 $db->setQuery("INSERT INTO `#__ninjaboard_timezones` (`id`,`name`,`description`,`offset`,`ordering`,`published`,`checked_out`,`checked_out_time`) VALUES 
			(1,'UTC -12:00','".JText::_('NB_IDLW')."','-12.00',1,1,0,'0000-00-00 00:00:00'),
			(2,'UTC -11:00','".JText::_('NB_MIS')."','-11.00',2,1,0,'0000-00-00 00:00:00'),
			(3,'UTC -10:00','".JText::_('NB_HAW')."','-10.00',3,1,0,'0000-00-00 00:00:00'),
			(4,'UTC -09:30','".JText::_('NB_TMI')."','-9.50',4,1,0,'0000-00-00 00:00:00'),
			(5,'UTC -09:00','".JText::_('NB_ALA')."','-9.00',5,1,0,'0000-00-00 00:00:00'),
			(6,'UTC -08:00','".JText::_('NB_PAC')."','-8.00',6,1,0,'0000-00-00 00:00:00'),
			(7,'UTC -07:00','".JText::_('NB_MTN')."','-7.00',7,1,0,'0000-00-00 00:00:00'),
			(8,'UTC -06:00','".JText::_('NB_CENT')."','-6.00',8,1,0,'0000-00-00 00:00:00'),
			(9,'UTC -05:00','".JText::_('NB_EAST')."','-5.00',9,1,0,'0000-00-00 00:00:00'),
			(10,'UTC -04:00','".JText::_('NB_ATL')."','-4.00',10,1,0,'0000-00-00 00:00:00'),
			(11,'UTC -03:30','".JText::_('NB_SJN')."','-3.50',11,1,0,'0000-00-00 00:00:00'),
			(12,'UTC -03:00','".JText::_('NB_BBG')."','-3.00',12,1,0,'0000-00-00 00:00:00'),
			(13,'UTC -02:00','".JText::_('NB_MA')."','-2.00',13,1,0,'0000-00-00 00:00:00'),
			(14,'UTC -01:00','".JText::_('NB_ACVI')."','-1.00',14,1,0,'0000-00-00 00:00:00'),
			(15,'UTC +00:00','".JText::_('NB_WET')."','0.00',15,1,0,'0000-00-00 00:00:00'),
			(16,'UTC +01:00','".JText::_('NB_ABB')."','1.00',16,1,0,'0000-00-00 00:00:00'),
			(17,'UTC +02:00','".JText::_('NB_JKSA')."','2.00',17,1,0,'0000-00-00 00:00:00'),
			(18,'UTC +03:00','".JText::_('NB_BRMS')."','3.00',18,1,0,'0000-00-00 00:00:00'),
			(19,'UTC +03:30','".JText::_('NB_TEH')."','3.50',19,1,0,'0000-00-00 00:00:00'),
			(20,'UTC +04:00','".JText::_('NB_AMBT')."','4.00',20,1,0,'0000-00-00 00:00:00'),
			(21,'UTC +04:30','".JText::_('NB_KABU')."','4.50',21,1,0,'0000-00-00 00:00:00'),
			(22,'UTC +05:00','".JText::_('NB_EIKT')."','5.00',22,1,0,'0000-00-00 00:00:00'),
			(23,'UTC +05:30','".JText::_('NB_BCMN')."','5.50',23,1,0,'0000-00-00 00:00:00'),
			(24,'UTC +05:45','".JText::_('NB_KATH')."','5.75',24,1,0,'0000-00-00 00:00:00'),
			(25,'UTC +06:00','".JText::_('NB_ADC')."','6.00',25,1,0,'0000-00-00 00:00:00'),
			(26,'UTC +06:30','".JText::_('NB_YAG')."','6.50',26,1,0,'0000-00-00 00:00:00'),
			(27,'UTC +07:00','".JText::_('NB_BHJ')."','7.00',27,1,0,'0000-00-00 00:00:00'),
			(28,'UTC +08:00','".JText::_('NB_BPS')."','8.00',28,1,0,'0000-00-00 00:00:00'),
			(29,'UTC +08:45','".JText::_('NB_WA')."','8.75',29,1,0,'0000-00-00 00:00:00'),
			(30,'UTC +09:00','".JText::_('NB_TSO')."','9.00',30,1,0,'0000-00-00 00:00:00'),
			(31,'UTC +09:30','".JText::_('NB_ADY')."','9.50',31,1,0,'0000-00-00 00:00:00'),
			(32,'UTC +10:00','".JText::_('NB_EA')."','10.00',32,1,0,'0000-00-00 00:00:00'),
			(33,'UTC +10:30','".JText::_('NB_LHI')."','10.50',33,1,0,'0000-00-00 00:00:00'),
			(34,'UTC +11:00','".JText::_('NB_MSN')."','11.00',34,1,0,'0000-00-00 00:00:00'),
			(35,'UTC +11:30','".JText::_('NB_NOR')."','11.50',35,1,0,'0000-00-00 00:00:00'),
			(36,'UTC +12:00','".JText::_('NB_AWF')."','12.00',36,1,0,'0000-00-00 00:00:00'),
			(37,'UTC +12:45','".JText::_('NB_CHI')."','12.75',37,1,0,'0000-00-00 00:00:00'),
			(38,'UTC +13:00','".JText::_('NB_TON')."','13.00',38,1,0,'0000-00-00 00:00:00'),
			(39,'UTC +14:00','".JText::_('NB_KIRI')."','14.00',39,1,0,'0000-00-00 00:00:00')");   
			
			$db->query();
			
			
			 $db->setQuery("INSERT INTO `#__ninjaboard_topics` (`id`,`views`,`replies`,`status`,`vote`,`type`,`id_moved`,`id_forum`,`id_first_post`,`id_last_post`) VALUES 
			(1,0,0,0,0,2,0,1,1,1)");   
			
			$db->query();
     
    }//if !count($counter)
	
}

?>
