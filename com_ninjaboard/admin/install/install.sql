CREATE TABLE IF NOT EXISTS `#__ninjaboard_categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '1',
  `ordering` int(11) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `ordering` (`ordering`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_configs` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `default_config` tinyint(1) NOT NULL default '0',
  `id_design` int(11) NOT NULL default '0',
  `id_timezone` int(11) NOT NULL default '0',
  `id_timeformat` int(11) NOT NULL default '0',
  `editor` varchar(255) NOT NULL default '',
  `topic_icon_function` varchar(255) NOT NULL default '',
  `post_icon_function` varchar(255) NOT NULL default '',
  `board_settings` text NOT NULL,
  `latestpost_settings` text NOT NULL,
  `feed_settings` text NOT NULL,
  `view_settings` text NOT NULL,
  `view_footer_settings` text NOT NULL,
  `user_settings_defaults` text NOT NULL,
  `attachment_settings` text NOT NULL,
  `avatar_settings` text NOT NULL,
  `captcha_settings` text NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_designs` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `default_design` tinyint(1) NOT NULL default '0',
  `template` varchar(255) NOT NULL default '',
  `style` varchar(255) NOT NULL default '',
  `emoticon_set` varchar(255) NOT NULL default '',
  `icon_set` varchar(255) NOT NULL default '',
  `button_set` varchar(255) NOT NULL default '',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_forums` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `state` tinyint(1) NOT NULL default '1',
  `locked` tinyint(1) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '1',
  `new_posts_time` int(5) NOT NULL default '0',
  `posts` int(11) NOT NULL default '0',
  `topics` int(11) NOT NULL default '0',
  `auth_view` int(3) NOT NULL default '0',
  `auth_read` int(3) NOT NULL default '0',
  `auth_post` int(3) NOT NULL default '0',
  `auth_reply` int(3) NOT NULL default '0',
  `auth_edit` int(3) NOT NULL default '0',
  `auth_delete` int(3) NOT NULL default '0',
  `auth_reportpost` int(3) NOT NULL default '0',
  `auth_sticky` int(3) NOT NULL default '0',
  `auth_lock` int(3) NOT NULL default '0',
  `auth_announce` int(3) NOT NULL default '0',
  `auth_vote` int(3) NOT NULL default '0',
  `auth_pollcreate` int(3) NOT NULL default '0',
  `auth_attachments` int(3) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `id_cat` int(11) NOT NULL default '0',
  `id_last_post` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id_cat` (`id_cat`),
  KEY `id_last_post` (`id_last_post`),
  KEY `ordering` (`ordering`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_forums_auth` (
  `id_forum` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `role` tinyint(1) NOT NULL default '0',
  `id_group` int(11) NOT NULL default '0',
  KEY `id_forum` (`id_forum`),
  KEY `id_group` (`id_group`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '1',
  `role` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_groups_mapping` (  
  `id_mapping_type` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_mapping_type`,`id_group`,`value`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_groups_mapping_types` (  
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text,
  `get_value_sql` text NOT NULL,
  `make_param_list_sql` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_groups_users` (
  `id_group` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  KEY `id_group` (`id_group`),
  KEY `id_user` (`id_user`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_posts` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `date_post` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_last_edit` datetime NOT NULL default '0000-00-00 00:00:00',  
  `id_edit_by` int(11) NOT NULL default '0',
  `edit_reason` varchar(255) NOT NULL default '',
  `enable_bbcode` tinyint(1) NOT NULL default '1',
  `enable_emoticons` tinyint(1) NOT NULL default '1',
  `notify_on_reply` tinyint(1) NOT NULL default '0',
  `ip_poster` varchar(15) NOT NULL default '',
  `icon_function` varchar(255) NOT NULL default '',
  `id_topic` int(11) NOT NULL default '0',
  `id_forum` int(11) NOT NULL default '0',
  `id_parent` int(11) NOT NULL default '0',
  `id_user` int(11) NOT NULL default '0',
  `guest_name` varchar(255) default '',
  `guest_email` varchar(255) default '',
  PRIMARY KEY  (`id`),
  KEY `id_forum` (`id_forum`),
  KEY `id_user` (`id_user`),
  KEY `id_topic` (`id_topic`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_posts_guests` (
  `id_post` int(11) NOT NULL default '0',
  `guest_name` varchar(255) NOT NULL default '',
  KEY `id_post` (`id_post`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_profiles` (
  `id` int(11) NOT NULL default '0',
  `p_icq` varchar(15) default NULL,
  `p_aim` varchar(255) default NULL,
  `p_msnm` varchar(255) default NULL,
  `p_yim` varchar(255) default NULL,
  `p_skype` varchar(255) default NULL,
  `p_gtalk` varchar(255) default NULL,
  `p_firstname` varchar(100) default NULL,
  `p_lastname` varchar(100) default NULL,
  `p_location` varchar(255) default NULL,
  `p_country` int(3) default NULL,
  `p_signature` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_profiles_fields` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `element` int(3) NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  `default` varchar(255) NOT NULL default '',
  `size` int(7) unsigned NOT NULL default '0',
  `length` int(7) unsigned NOT NULL default '0',
  `rows` int(7) unsigned NOT NULL default '0',
  `columns` int(7) unsigned NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '1',
  `required` tinyint(1) NOT NULL default '0',
  `disabled` tinyint(1) NOT NULL default '0',
  `show_on_registration` tinyint(1) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `id_profile_field_list` int(11) NOT NULL default '0',
  `id_profile_field_set` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_profiles_fields_lists` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '1',
  `checked_out` int(1) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_profiles_fields_lists_values` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '1',
  `ordering` int(11) NOT NULL default '0',
  `checked_out` int(1) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `id_profile_field_list` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_profiles_fields_sets` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '1',
  `ordering` int(11) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_ranks` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `min_posts` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '1',
  `rank_file` varchar(255) NOT NULL default '',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_session` (
  `session_id` varchar(200) NOT NULL default '',
  `id_user` int(11) NOT NULL default '0',
  `current_action` varchar(255) NOT NULL default '',
  `action_url` varchar(255) NOT NULL default '',
  `action_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`session_id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_terms` (
  `id` int(11) NOT NULL auto_increment,
  `terms` varchar(255) NOT NULL default '',
  `termstext` text NOT NULL,
  `agreement` varchar(255) NOT NULL default '',
  `agreementtext` text NOT NULL,
  `locale` varchar(5) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '1',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_timeformats` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default '',
  `timeformat` varchar(25) NOT NULL default 'd M Y H:i',
  `published` tinyint(1) NOT NULL default '1',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_timezones` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `offset` decimal(5,2) NOT NULL default '0.00',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '1',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_topics` (
  `id` int(11) NOT NULL auto_increment,
  `views` int(11) unsigned NOT NULL default '0',
  `replies` int(11) unsigned NOT NULL default '0',
  `status` tinyint(3) NOT NULL default '0',
  `vote` tinyint(1) NOT NULL default '0',
  `type` tinyint(3) NOT NULL default '0',
  `id_moved` int(11) unsigned NOT NULL default '0',
  `id_forum` int(11) NOT NULL default '0',
  `id_first_post` int(11) unsigned NOT NULL default '0',
  `id_last_post` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `id_forum` (`id_forum`),
  KEY `id_moved` (`id_moved`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_users` (
  `id` int(11) NOT NULL default '0',
  `posts` mediumint(8) unsigned NOT NULL default '0',
  `role` tinyint(1) unsigned NOT NULL default '1',
  `system_emails` tinyint(1) unsigned NOT NULL default '0',
  `show_email` tinyint(1) NOT NULL default '0',
  `show_online_state` tinyint(1) NOT NULL default '1',
  `notify_on_reply` tinyint(1) NOT NULL default '0',
  `enable_bbcode` tinyint(1) NOT NULL default '1',
  `enable_emoticons` tinyint(1) NOT NULL default '1',
  `avatar_file` varchar(255) NOT NULL default '',
  `signature` text,
  `time_zone` decimal(5,2) NOT NULL default '0.00',
  `time_format` varchar(25) NOT NULL default '',
  `gender` tinyint(1) NOT NULL default '0',
  `show_gender` tinyint(1) NOT NULL default '0',
  `birthdate` date,
  `show_birthdate` tinyint(1) NOT NULL default '0',
  `location` varchar(50) NOT NULL default '', 
  PRIMARY KEY  (`id`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_subscriptions` (
  `subs_type` smallint(3) unsigned NOT NULL default '0',
  `id_user` int(11) unsigned NOT NULL default '0',  
  `guest_name` varchar(255) default '',  
  `guest_email` varchar(255) default '',
  `id_subs_item` int(11) unsigned NOT NULL default '0',
  `one_mail_or_many` tinyint(1) unsigned NOT NULL default '0',
  `mail_sent` tinyint(1) unsigned NOT NULL default '0',
  KEY `subs_user` (`subs_type`,`id_subs_item`,`id_user`),
  KEY `subs_item` (`subs_type`,`id_subs_item`)
) TYPE=MyISAM CHARACTER SET `utf8`;

CREATE TABLE IF NOT EXISTS `#__ninjaboard_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `id_user` int(11) unsigned NOT NULL default '0',  
  `id_post` int(11) unsigned NOT NULL default '0',  
  `file_name` varchar(255) default '', 
  PRIMARY KEY  (`id`),
  KEY `attach_post` (`id_post`)
) TYPE=MyISAM CHARACTER SET `utf8`;
