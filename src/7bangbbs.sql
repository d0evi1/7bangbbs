#---------------------------------
# 文章.
#----------------------------------
DROP TABLE IF EXISTS bbs_articles;
CREATE TABLE bbs_articles (
  id 				mediumint(8) unsigned NOT NULL auto_increment,
  cid 				smallint(6) unsigned NOT NULL default '0',
  uid 				mediumint(8) unsigned NOT NULL default '0',
  reply_uid 		mediumint(8) 	unsigned NOT NULL default '0',
  title 			varchar(255) NOT NULL default '',
  content 			mediumtext NOT NULL,
  add_time 			int(10) unsigned NOT NULL default '0',
  edit_time 		int(10) unsigned NOT NULL default '0',
  views 			int(10) unsigned NOT NULL default '1',
  comments 			mediumint(8) unsigned NOT NULL default '0',
  is_comment 		tinyint(1) NOT NULL default '0',
  favorites 		int(10) unsigned NOT NULL default '0',
  is_visible 		tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (id),
  KEY cid (cid),
  KEY edit_time (edit_time),
  KEY uid (uid)
) ENGINE=MyISAM ;

#----------------------------------
# 类别.
#----------------------------------
DROP TABLE IF EXISTS bbs_categories;
CREATE TABLE bbs_categories (
  id 			smallint(6) unsigned NOT NULL auto_increment,
  name 			char(50) NOT NULL,
  articles 		mediumint(8) unsigned NOT NULL default '0',
  about 		text NOT NULL,
  seq			int(11)	unsigned not null default 0,
  PRIMARY KEY  (id),
  KEY articles (articles)
) ENGINE=MyISAM ;

INSERT INTO bbs_categories VALUES(1, '默认分类', 0, '');

#---------------------------------
# 注释
#---------------------------------
DROP TABLE IF EXISTS bbs_comments;
CREATE TABLE bbs_comments (
  id 			int(10) unsigned NOT NULL auto_increment,
  article_id 	mediumint(8) unsigned NOT NULL default '0',
  uid 			mediumint(8) unsigned NOT NULL default '0',
  add_time 		int(10) unsigned NOT NULL default '0',
  content 		mediumtext NOT NULL,
  PRIMARY KEY  (id),
  KEY article_id (article_id)
) ENGINE=MyISAM ;


#----------------------------------
# 外链
#----------------------------------
DROP TABLE IF EXISTS bbs_links;
CREATE TABLE bbs_links (
  id 			smallint(6) unsigned NOT NULL auto_increment,
  name 			varchar(100) NOT NULL default '',
  url 			varchar(200) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=MyISAM ;

INSERT INTO bbs_links VALUES(null,'7bangbbs', 'http://www.7bangbbs.com');

#-----------------------------------
# 全局设置
#-----------------------------------
DROP TABLE IF EXISTS bbs_settings;
CREATE TABLE bbs_settings (
  title 		varchar(50) NOT NULL default '',
  value 		text NOT NULL,
  PRIMARY KEY  (title)
) ENGINE=MyISAM ;


INSERT INTO bbs_settings VALUES('name', '7bangbbs');
INSERT INTO bbs_settings VALUES('site_des', '一个轻快的bbs');
INSERT INTO bbs_settings VALUES('icp', '');
INSERT INTO bbs_settings VALUES('admin_email', '');
INSERT INTO bbs_settings VALUES('home_shownum', '20');
INSERT INTO bbs_settings VALUES('list_shownum', '20');
INSERT INTO bbs_settings VALUES('newest_node_num', '20');
INSERT INTO bbs_settings VALUES('hot_node_num', '20');
INSERT INTO bbs_settings VALUES('bot_node_num', '100');
INSERT INTO bbs_settings VALUES('article_title_max_len', '60');
INSERT INTO bbs_settings VALUES('article_content_max_len', '3000');
INSERT INTO bbs_settings VALUES('article_post_space', '60');
INSERT INTO bbs_settings VALUES('reg_ip_space', '3600');
INSERT INTO bbs_settings VALUES('comment_min_len', '4');
INSERT INTO bbs_settings VALUES('comment_max_len', '1200');
INSERT INTO bbs_settings VALUES('commentlist_num', '32');
INSERT INTO bbs_settings VALUES('comment_post_space', '20');
INSERT INTO bbs_settings VALUES('close', '0');
INSERT INTO bbs_settings VALUES('close_note', '数据调整中');
INSERT INTO bbs_settings VALUES('authorized', '0');
INSERT INTO bbs_settings VALUES('register_review', '0');
INSERT INTO bbs_settings VALUES('close_register', '0');
INSERT INTO bbs_settings VALUES('close_upload', '0');
INSERT INTO bbs_settings VALUES('ext_list', '');
INSERT INTO bbs_settings VALUES('img_shuiyin', '0');
INSERT INTO bbs_settings VALUES('show_debug', '0');
INSERT INTO bbs_settings VALUES('jquery_lib', '/static/js/jquery-1.6.4.js');
INSERT INTO bbs_settings VALUES('head_meta', '');
INSERT INTO bbs_settings VALUES('analytics_code', '');
INSERT INTO bbs_settings VALUES('safe_imgdomain', '');
INSERT INTO bbs_settings VALUES('upyun_avatar_domain', '');
INSERT INTO bbs_settings VALUES('upyun_domain', '');
INSERT INTO bbs_settings VALUES('upyun_user', '');
INSERT INTO bbs_settings VALUES('upyun_pw', '');
INSERT INTO bbs_settings VALUES('ad_post_top', '');
INSERT INTO bbs_settings VALUES('ad_post_bot', '');
INSERT INTO bbs_settings VALUES('ad_sider_top', '');
INSERT INTO bbs_settings VALUES('ad_web_bot', '');
INSERT INTO bbs_settings VALUES('main_nodes', '');
INSERT INTO bbs_settings VALUES('spam_words', '');
INSERT INTO bbs_settings VALUES('qq_scope', 'get_user_info');
INSERT INTO bbs_settings VALUES('qq_appid', '');
INSERT INTO bbs_settings VALUES('qq_appkey', '');

INSERT INTO bbs_settings VALUES('page_per_num', 30);
INSERT INTO bbs_settings VALUES('page_range_limit', 5);

#-------------------------------
# 用户.
#-------------------------------
DROP TABLE IF EXISTS bbs_users;
CREATE TABLE bbs_users (
  id 				mediumint(8) unsigned NOT NULL auto_increment,
  name 				varchar(20) NOT NULL default '',
  limit_flag 		tinyint(2) NOT NULL default '0',
  avatar 			varchar(75) NOT NULL,
  passwd 			char(32) NOT NULL,
  salt				varchar(6) NOT NULL,
  email 			varchar(40) NOT NULL,
  url 				varchar(75) NOT NULL,
  articles 			int(10) unsigned NOT NULL default '0',
  replies 			int(10) unsigned NOT NULL default '0',
  reg_time 			int(10) unsigned NOT NULL default '0',
  last_post_time 	int(10) unsigned NOT NULL default '0',
  last_reply_time 	int(10) unsigned NOT NULL default '0',
  about 			text NOT NULL,
  notice 			text NOT NULL,
  interest			varchar(256) NOT NULL default '',
  sina_weibo		varchar(32)	NOT NULL default '',
  tx_weibo			varchar(32) NOT NULL default '',
  wx_account		varchar(32)	NOT NULL default '',
  qq_account		varchar(32) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM ;


#-----------------------------------
# 关注.
#-----------------------------------
DROP TABLE IF EXISTS bbs_favorites;
CREATE TABLE bbs_favorites (
  id 			mediumint(8) unsigned NOT NULL auto_increment,
  uid 			mediumint(8) unsigned NOT NULL default '0',
  articles 		mediumint(8) unsigned NOT NULL default '0',
  content 		mediumtext NOT NULL,
  PRIMARY KEY (id),
  KEY uid (uid)
) ENGINE=MyISAM ;

#-------------------------------------
# qq/weibo.
#------------------------------------
DROP TABLE IF EXISTS bbs_qqweibo;
CREATE TABLE bbs_qqweibo (
  id 			mediumint(8) unsigned NOT NULL auto_increment,
  uid 			mediumint(8) unsigned NOT NULL default '0',
  name 			varchar(20) NOT NULL default '',
  openid 		char(32) NOT NULL,
  PRIMARY KEY (id),
  KEY uid (uid),
  KEY openid (openid)
) ENGINE=MyISAM ;

#---------------------------------------
# 在线状态表.
#----------------------------------------
DROP TABLE IF EXISTS bbs_online;
CREATE TABLE bbs_online (
  id 			int(10) unsigned NOT NULL,
  skey		 	varchar(64) NOT NULL default '0',
  is_online		tinyint(11) unsigned NOT NULL,
  login_time 	int(11) unsigned NOT NULL default '0',
  logout_time	int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;

#-----------------------------------------
# 群功能.
#-----------------------------------------
DROP TABLE IF EXISTS bbs_group;
CREATE TABLE bbs_group (
    id 			int(10) unsigned NOT NULL,
    admin			int(10) unsigned not NULL,
    is_audit		tinyint(11) unsigned NOT NULL,
    create_time 	int(11) unsigned NOT NULL,
    PRIMARY KEY  (id)
) ENGINE=MyISAM;