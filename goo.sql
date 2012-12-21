/*
MySQL Data Transfer
Source Host: 192.168.171.128
Source Database: goo
Target Host: 192.168.171.128
Target Database: goo
Date: 2012/12/19 22:32:39
*/

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
  `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_account` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;


CREATE TABLE `files` (
  `fid` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `attachment` VALUES ('1', 'button.png', '270', '.png', 'admin/201212/e0331b4f84e61e706cb80e72f5b89c91.png', '2012-12-19 21:20:29', 'admin');
INSERT INTO `attachment` VALUES ('2', 'client_login.jpg', '111287', '.jpg', 'admin/201212/3034b965baa7bd60d9d6161808d5914c.jpg', '2012-12-19 21:25:37', 'admin');
INSERT INTO `attachment` VALUES ('3', 'client_login.jpg', '111287', '.jpg', 'admin/201212/4afa42c127e51c26c3c66a75db9e485e.jpg', '2012-12-19 21:29:28', 'admin');
INSERT INTO `product` VALUES ('1', 'Product1', '1', '1', '2012-12-18 22:06:19', '2012-12-19 22:06:29');
INSERT INTO `project` VALUES ('1', 'Project1', '1', '1', '1', '2012-12-19 22:24:58', '2012-12-19 22:25:03');
INSERT INTO `role` VALUES ('1', 'admin', '0', '1', null);
INSERT INTO `user` VALUES ('1', 'admin', 'Admin', 'point9*', '', '', '2012-12-15 12:17:00', '0000-00-00 00:00:00', '1', '1');
INSERT INTO `user` VALUES ('2', 'testclient', 'Test Client', '111111', '', '', '2012-12-16 14:33:20', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('3', 'client2', 'Client2', '111111', '', '', '2012-12-16 15:30:00', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('4', 'client3', 'Client3', '111111', '', '', '2012-12-16 15:30:00', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('11', 'bella', 'bella', '111111', '', '', '2012-12-16 21:12:15', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('12', 'cathy', 'cathy', '111111', '', '', '2012-12-16 21:16:13', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('13', 'testuser1', 'testuser', '111111', '', '', '2012-12-16 21:31:15', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('14', 'testuser2', 'testuser2', '111111', '', '', '2012-12-16 21:31:27', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('15', 'testuser3', 'testuser3', '111111', '', '', '2012-12-16 21:31:48', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('16', 'testuser4', 'testuser4', '111111', '', '', '2012-12-16 21:32:07', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('17', 'testuser5', 'testuser5', '111111', '', '', '2012-12-16 21:32:25', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('18', 'testuser6', 'testuser6', '111111', '', '', '2012-12-16 21:32:44', '0000-00-00 00:00:00', '0', '2');
INSERT INTO `user` VALUES ('19', 'testname', 'testname', 'testname', '', '', '2012-12-19 21:03:05', '0000-00-00 00:00:00', '0', '1');
INSERT INTO `user` VALUES ('20', 'testname1', 'testname1', 'testname1', '', '', '2012-12-19 22:30:34', '0000-00-00 00:00:00', '0', '1');
