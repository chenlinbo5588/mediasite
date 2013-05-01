SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for access
-- ----------------------------
CREATE TABLE `access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for attachment
-- ----------------------------
CREATE TABLE `attachment` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(200) NOT NULL DEFAULT '',
  `file_size` int(11) NOT NULL DEFAULT '0' COMMENT '字节数',
  `file_suffix` varchar(10) NOT NULL,
  `path_name` varchar(100) NOT NULL,
  `width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '度宽, 图片、视频',
  `height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '高度',
  `play_sec` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '播放时间, 视频',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '@预留字段 1=新增,2=审核中,3=审核通过,4=审核失败',
  `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '@预留字段 审核备注',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '@预留字段 1 表示删除',
  `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_account` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for category
-- ----------------------------
CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `createtime` datetime NOT NULL,
  `updatetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for file_type
-- ----------------------------
CREATE TABLE `file_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for files
-- ----------------------------
CREATE TABLE `files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(50) NOT NULL DEFAULT '' COMMENT '账号',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '视频名称',
  `tag` varchar(50) NOT NULL DEFAULT '' COMMENT '视频标签',
  `play_sec` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '播放时间,预留',
  `file_name` varchar(200) NOT NULL DEFAULT '原文件名称',
  `file_suffix` varchar(10) NOT NULL COMMENT '文件名后缀',
  `video_path` varchar(100) NOT NULL DEFAULT '' COMMENT '视频路径',
  `video_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '视频大小',
  `video_width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '视频宽度',
  `video_height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '视频高度',
  `img_path` varchar(100) NOT NULL DEFAULT '' COMMENT '图片路径',
  `img_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片大小',
  `img_width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '图片宽度',
  `img_height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '图片高度',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `product_name` varchar(100) NOT NULL DEFAULT '' COMMENT '产品名称',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目ID',
  `project_name` varchar(100) NOT NULL DEFAULT '' COMMENT '项目名称',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `category_name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1=新增,2=审核中,3=审核通过,4=审核失败',
  `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '审核备注',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1 表示删除',
  `createtime` datetime NOT NULL COMMENT '创建时间',
  `updatetime` datetime NOT NULL COMMENT '更新时间',
  `create_user` varchar(50) NOT NULL DEFAULT '' COMMENT '创建者',
  `update_user` varchar(50) NOT NULL DEFAULT '' COMMENT '更新者',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for folder
-- ----------------------------
CREATE TABLE `folder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `createtime` datetime NOT NULL,
  `updatetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for node
-- ----------------------------
CREATE TABLE `node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product
-- ----------------------------
CREATE TABLE `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `createtime` datetime NOT NULL,
  `updatetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for project
-- ----------------------------
CREATE TABLE `project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `createtime` datetime NOT NULL,
  `updatetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `i_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for role
-- ----------------------------
CREATE TABLE `role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for role_user
-- ----------------------------
CREATE TABLE `role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user
-- ----------------------------
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(50) NOT NULL,
  `nickname` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名称',
  `password` varchar(30) NOT NULL DEFAULT '' COMMENT '用户密码',
  `project` varchar(40) NOT NULL DEFAULT '' COMMENT '项目',
  `category` varchar(40) NOT NULL DEFAULT '' COMMENT '分类',
  `createtime` datetime NOT NULL,
  `updatetime` datetime NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user type 0：client，1：admin',
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `user` VALUES ('1', 'admin', 'Admin', 'point9*', '', '', '2012-12-15 12:17:00', '0000-00-00 00:00:00', '1', '1');

CREATE TABLE `site_column` (
  `code` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`code`),
  UNIQUE KEY `u_title_idx` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `site_column` VALUES ('Home Page', 'Home Page', '/');
INSERT INTO `site_column` VALUES ('About Page', 'About Page', '/About/');
INSERT INTO `site_column` VALUES ('Work Page', 'Work Page', '/Work/');
INSERT INTO `site_column` VALUES ('Services Page', 'Services Page', '/Services/');
INSERT INTO `site_column` VALUES ('Contact Page', 'Contact Page', '/Contact/');

alter table attachment add index  sit_column_key (remark) ;
alter table files add index  file_name_idx (file_name) ;

CREATE TABLE `file_auth` (
  `auth_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL COMMENT 'files 表中的 id 外键',
  `user_id` int(10) unsigned NOT NULL COMMENT 'user 表中 id 外键',
  `auth_type` varchar(50) NOT NULL COMMENT '权授类型, view  share download 等等,按照|分隔多个权限',
  PRIMARY KEY (`auth_id`),
  KEY `rid_idx` (`rid`),
  KEY `user_id_idx` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
