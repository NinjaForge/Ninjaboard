CREATE TABLE IF NOT EXISTS `#__ninjaboard_assets` (
  `ninjaboard_asset_id` SERIAL,
  `level` tinyint(1) unsigned NOT NULL COMMENT 'The permission level. 0 = No Access, 1 = Has Access, 2 = and Can Create, 3 = and Can Manage',
  `name` varchar(255) NOT NULL COMMENT 'The unique name for the permission object',
  UNIQUE KEY `idx_asset_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_attachments` (
  `ninjaboard_attachment_id` SERIAL,
  `file` varchar(255) NOT NULL,
  `joomla_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `post_id` int(11) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  KEY `attach_post` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_forums` (
  `ninjaboard_forum_id` SERIAL,
  `parent_id` bigint(20) unsigned NOT NULL default '0'  COMMENT 'Needed for some converters',
  `level` int(10) unsigned NOT NULL DEFAULT '0'  COMMENT 'Hierarchy depth',
  `path` varchar(255) NOT NULL DEFAULT '/'  COMMENT 'Needed for the enumerated path implementation for the forum hierarchies',
  `enabled` tinyint(1) SIGNED  NOT NULL default 1,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '1',
  `path_sort` text NOT NULL COMMENT 'Cached ordering by path (path+ninjaboard_forum_id)',
  `path_sort_title` text NOT NULL COMMENT 'path_sort with ids replaced with forum titles',
  `path_sort_ordering` text NOT NULL COMMENT 'path_sort with ids replaced with ordering values',
  `posts` int(11) NOT NULL DEFAULT '0',
  `topics` int(11) NOT NULL DEFAULT '0',
  `forum_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_post_id` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  KEY `idx_alias` (`alias`),
  KEY `last_post_id` (`last_post_id`),
  FULLTEXT KEY `path` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_profile_fields` (
  `ninjaboard_profile_field_id` SERIAL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '@Filter("koowa:filter.alpha")',
  `label` text NOT NULL COMMENT 'Label of the column field',
  `description` text NOT NULL,
  `element` varchar(255) NOT NULL,
  `profile_field_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `size` int(7) unsigned NOT NULL DEFAULT '0',
  `min` int(5) NOT NULL,
  `max` int(5) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `registration` tinyint(1) NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_posts` (
  `ninjaboard_post_id` SERIAL,
  `subject` varchar(100) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `created_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'  COMMENT '',
  `created_user_id` int(11) unsigned NOT NULL default 0,
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_user_id` int(11) unsigned NOT NULL default 0,
  `edit_reason` varchar(255) NOT NULL DEFAULT '',
  `user_ip` varchar(15) NOT NULL DEFAULT '',
  `locked` tinyint(1) NOT NULL DEFAULT '1',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  `ninjaboard_topic_id` int(11) NOT NULL DEFAULT '0',
  `guest_name` varchar(255) DEFAULT '',
  `guest_email` varchar(255) DEFAULT '',
  KEY `user_id` (`created_user_id`),
  KEY `created_time` (`created_time`),
  KEY `ninjaboard_topic_id` (`ninjaboard_topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_ranks` (
  `ninjaboard_rank_id` SERIAL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `min` int(11) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `rank_file` text NOT NULL,
  `rank_thumb` text NOT NULL,
  `rank_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_settings` (
  `ninjaboard_setting_id` SERIAL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `theme` varchar(100) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `theme` (`theme`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_subscriptions` (
  `ninjaboard_subscription_id` SERIAL,
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last activity for an watch',
  `guest_name` varchar(255) DEFAULT '',
  `guest_email` varchar(255) DEFAULT '',
  `subscription_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'The subscription type. Currently forum, topic or person.',
  `subscription_type_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'The subscription type id. Example: ninjaboard_forum_id.',
  `one_mail_or_many` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `mail_sent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `subs_user` (`subscription_type`,`subscription_type_id`,`created_by`),
  KEY `subs_item` (`subscription_type`,`subscription_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_topics` (
  `ninjaboard_topic_id` SERIAL,
  `replies` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `vote` tinyint(1) NOT NULL DEFAULT '0',
  `topic_type_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(11) NOT NULL DEFAULT '0',
  `first_post_id` int(11) unsigned NOT NULL DEFAULT '0',
  `last_post_id` int(11) unsigned NOT NULL DEFAULT '0',
  `last_post_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'This is for caching purposes',
  `last_post_by` int(11) unsigned NOT NULL default 0 COMMENT 'This is also for caching purposes',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `sticky` tinyint(1) NOT NULL DEFAULT '1',
  `locked` tinyint(1) NOT NULL DEFAULT '1',
  `resolved` tinyint(1) NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  `show_symlinks` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Show symlinks to moved topics',
  KEY `topic_type_id` (`topic_type_id`),
  KEY `status` (`status`),
  KEY `forum_id` (`forum_id`),
  KEY `first_post_id` (`first_post_id`),
  KEY `last_post_id` (`last_post_id`),
  KEY `last_post_on` (`last_post_on`),
  KEY `last_post_by` (`last_post_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_topic_symlinks` (
  `ninjaboard_topic_id` bigint(20) unsigned NOT NULL,
  `ninjaboard_forum_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ninjaboard_topic_id`,`ninjaboard_forum_id`),
  KEY `ninjaboard_topic_id` (`ninjaboard_topic_id`),
  KEY `ninjaboard_forum_id` (`ninjaboard_forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_topic_slugs` (
  `ninjaboard_topic_slug` varchar(100) NOT NULL DEFAULT '',
  `ninjaboard_topic_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ninjaboard_topic_slug`),
  KEY `ninjaboard_topic_id` (`ninjaboard_topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_people` (
  `ninjaboard_person_id` int(11) unsigned NOT NULL DEFAULT '0',
  `posts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `thumb` text NOT NULL,
  `avatar` text NOT NULL,
  `avatar_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'When the avatar was uploaded, used in urls for browser cache to work',
  `signature` text,
  `which_name` varchar(8) NOT NULL DEFAULT '' COMMENT 'username, name or alias',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT 'Custom screen name defined by person',
  `params` text NOT NULL,
  `notify_on_create_topic` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Subscribe to threads I create',
  `notify_on_reply_topic` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Subscribe to threads I reply to',
  `notify_on_private_message` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Notify me when I receive a private message',
  `temporary_id` int(11) UNSIGNED NULL DEFAULT '0' COMMENT 'Used only by converters for user sync',
  PRIMARY KEY (`ninjaboard_person_id`),
  KEY `which_name` (`which_name`),
  KEY `temporary_id` (`temporary_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_user_groups` (
  `ninjaboard_user_group_id` SERIAL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Adjacency List Reference Id',
  `path` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '1',
  `visible` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_user_group_maps` (
  `joomla_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id',
  `ninjaboard_user_group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__ninjaboard_user_groups.id',
  PRIMARY KEY (`joomla_user_id`,`ninjaboard_user_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_joomla_user_group_maps` (
  `joomla_gid` int(10) unsigned NOT NULL DEFAULT '0',
  `ninjaboard_gid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`joomla_gid`,`ninjaboard_gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_log_topic_reads` (
  `created_by` int(11) unsigned NOT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ninjaboard_forum_id` bigint(20) unsigned NOT NULL,
  `ninjaboard_topic_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`created_by`,`ninjaboard_forum_id`,`ninjaboard_topic_id`),
  KEY `created_on` (`created_on`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_iconsets` (
  `ninjaboard_iconset_id` SERIAL,
  `iconset` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `params` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ninjaboard_messages` (
  `ninjaboard_message_id` SERIAL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `subject` tinytext NOT NULL,
  `text` text NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Deleted by sender'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Private messages, direct messages';


CREATE TABLE IF NOT EXISTS `#__ninjaboard_message_recipients` (
  `ninjaboard_message_id` bigint(20) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `is_bcc` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ninjaboard_message_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;