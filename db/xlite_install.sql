

# 
# 表的结构 xlite_act
# 

CREATE TABLE `xlite_act` (
  `act_id` int(11) unsigned NOT NULL auto_increment,
  `act_name` varchar(32) NOT NULL,
  `controller` varchar(128) NOT NULL,
  `action` varchar(128) default NULL,
  PRIMARY KEY  (`act_id`),
  KEY `controller` (`controller`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_admin
# 

CREATE TABLE `xlite_admin` (
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `user_name` varchar(32) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `role_id` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_article
# 

CREATE TABLE `xlite_article` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `summary` text,
  `post_time` int(11) NOT NULL,
  `author` varchar(20) NOT NULL,
  `come_from` varchar(30) NOT NULL,
  `content` longtext,
  `sort_id` int(11) NOT NULL,
  `is_audit` tinyint(1) NOT NULL default '0',
  `is_recmd` tinyint(1) NOT NULL default '0',
  `is_pic` tinyint(1) NOT NULL default '0',
  `tags` varchar(30) NOT NULL,
  `title_color` varchar(10) NOT NULL,
  `thumbnail` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `is_recmd` (`sort_id`,`is_audit`,`is_recmd`),
  KEY `is_pic` (`is_pic`,`is_audit`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_ftp
# 

CREATE TABLE `xlite_ftp` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `site_name` varchar(20) NOT NULL,
  `host` varchar(50) NOT NULL,
  `user` varchar(20) NOT NULL,
  `pwd` varchar(30) NOT NULL,
  `pub_dir` varchar(100) NOT NULL,
  `last_pub_date` int(11) NOT NULL,
  `sort_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_gallery
# 

CREATE TABLE `xlite_gallery` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `pic_number` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `thumbnail` varchar(200) NOT NULL,
  `sort_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `sort_id` (`sort_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_guestbook
# 

CREATE TABLE `xlite_guestbook` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `author` varchar(30) NOT NULL,
  `created` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `home` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text,
  `reply` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_image
# 

CREATE TABLE `xlite_image` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `image` varchar(200) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `gallery_id` (`gallery_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_link
# 

CREATE TABLE `xlite_link` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(50) NOT NULL,
  `url` varchar(100) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL default '1',
  `image` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_role
# 

CREATE TABLE `xlite_role` (
  `role_id` int(11) unsigned NOT NULL auto_increment,
  `role_name` varchar(32) default NULL,
  PRIMARY KEY  (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_role_act
# 

CREATE TABLE `xlite_role_act` (
  `role_id` int(11) default NULL,
  `act_id` int(11) default NULL,
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_scratcher
# 

CREATE TABLE `xlite_scratcher` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `list_url` varchar(200) NOT NULL,
  `list_before_string` text NOT NULL,
  `list_after_string` text NOT NULL,
  `article_url` varchar(200) NOT NULL,
  `title_pattern` text NOT NULL,
  `post_time_pattern` text NOT NULL,
  `summary_pattern` text NOT NULL,
  `author_pattern` text NOT NULL,
  `come_from_pattern` text NOT NULL,
  `tags_pattern` text NOT NULL,
  `content_pattern` text NOT NULL,
  `save_sort_id` int(11) NOT NULL,
  `get_number` int(11) NOT NULL,
  `save_resource` tinyint(1) NOT NULL default '0',
  `total_save` int(11) NOT NULL,
  `charset` varchar(15) NOT NULL,
  `last_modified_time` int(11) NOT NULL,
  `is_rss` tinyint(1) NOT NULL default '0',
  `cookie` text,
  `keyword` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_sort
# 

CREATE TABLE `xlite_sort` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `root_id` int(11) unsigned NOT NULL default '0',
  `deep` tinyint(4) unsigned NOT NULL default '0',
  `ordernum` int(11) unsigned NOT NULL default '0',
  `title` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ordernum` (`ordernum`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# 
# 表的结构 xlite_tags
# 

CREATE TABLE `xlite_tags` (
  `id` int(11) NOT NULL auto_increment,
  `article_id` int(11) NOT NULL,
  `keyword` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;