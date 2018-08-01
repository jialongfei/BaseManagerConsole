/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50717
Source Host           : localhost:3306
Source Database       : base_manager_console

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2018-08-01 09:29:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `base_setting`
-- ----------------------------
DROP TABLE IF EXISTS `base_setting`;
CREATE TABLE `base_setting` (
  `title` varchar(100) DEFAULT NULL COMMENT '网站标题',
  `site_name` varchar(100) DEFAULT NULL COMMENT '站点名称',
  `footer_text` varchar(255) DEFAULT NULL COMMENT '公共底部显示文字',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='站点基本信息表';

-- ----------------------------
-- Records of base_setting
-- ----------------------------
INSERT INTO `base_setting` VALUES ('Dragon - Admin', 'Manage - Dragon', '© Manage - Dragon', '1');

-- ----------------------------
-- Table structure for `premission`
-- ----------------------------
DROP TABLE IF EXISTS `premission`;
CREATE TABLE `premission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned DEFAULT '0' COMMENT '父权限ID 0=顶级权限',
  `name` varchar(100) NOT NULL COMMENT '权限名称',
  `uri` varchar(100) DEFAULT 'javascript:;' COMMENT '路由名称',
  `is_nav` tinyint(1) unsigned DEFAULT '1' COMMENT '是否在导航栏显示',
  `create_user` int(10) unsigned NOT NULL COMMENT '创建人',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 COMMENT='权限表';

-- ----------------------------
-- Records of premission
-- ----------------------------
INSERT INTO `premission` VALUES ('1', '0', '后台首页', '/', '0', '1', '1531727311');
INSERT INTO `premission` VALUES ('2', '0', '用户&amp;权限', 'javascript:;', '1', '1', '1531727423');
INSERT INTO `premission` VALUES ('3', '2', '用户管理', '/user', '1', '1', '1531729427');
INSERT INTO `premission` VALUES ('4', '3', '用户添加', '/user/create', '0', '1', '1531729649');
INSERT INTO `premission` VALUES ('5', '3', '用户修改', '/user/edit', '0', '1', '1531729765');
INSERT INTO `premission` VALUES ('6', '3', '用户删除', '/user/delete', '0', '1', '1531729785');
INSERT INTO `premission` VALUES ('7', '2', '角色管理', '/role', '1', '1', '1531729852');
INSERT INTO `premission` VALUES ('8', '7', '角色添加', '/role/create', '0', '1', '1531729868');
INSERT INTO `premission` VALUES ('9', '7', '角色修改', '/role/edit', '0', '1', '1531729884');
INSERT INTO `premission` VALUES ('10', '7', '角色删除', '/role/delete', '0', '1', '1531729925');
INSERT INTO `premission` VALUES ('11', '2', '权限管理', '/premission', '1', '1', '1531791313');
INSERT INTO `premission` VALUES ('12', '11', '权限添加', '/premission/create', '0', '1', '1531791427');
INSERT INTO `premission` VALUES ('13', '11', '权限修改', '/premission/edit', '0', '1', '1531791449');
INSERT INTO `premission` VALUES ('14', '11', '权限删除', '/premission/delete', '0', '1', '1531791473');
INSERT INTO `premission` VALUES ('15', '0', '站点配置', 'javascript:;', '1', '1', '1531793108');
INSERT INTO `premission` VALUES ('16', '15', '基本配置', '/basesetting', '1', '1', '1531793195');
INSERT INTO `premission` VALUES ('28', '3', '修改状态', '/user/changestatus', '0', '1', '1531895858');
INSERT INTO `premission` VALUES ('29', '3', '重置密码', '/user/resetpassword', '0', '1', '1531895889');
INSERT INTO `premission` VALUES ('32', '7', '修改角色状态', '/role/changestatus', '0', '1', '1531898303');

-- ----------------------------
-- Table structure for `role`
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '角色名称',
  `desc` varchar(255) DEFAULT NULL COMMENT '角色描述',
  `create_user` int(10) unsigned NOT NULL COMMENT '创建人',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('5', '超级管理员', '暂无描述', '1', '1531725643', '1');

-- ----------------------------
-- Table structure for `role_pre`
-- ----------------------------
DROP TABLE IF EXISTS `role_pre`;
CREATE TABLE `role_pre` (
  `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',
  `pre_id` int(10) unsigned NOT NULL COMMENT '权限ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色,权限关联表';

-- ----------------------------
-- Records of role_pre
-- ----------------------------

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(36) NOT NULL COMMENT '账户',
  `true_name` varchar(100) DEFAULT NULL COMMENT '真实姓名',
  `phone` varchar(36) DEFAULT NULL COMMENT '手机号',
  `email` varchar(60) DEFAULT NULL COMMENT '邮箱',
  `password` varchar(36) DEFAULT 'invY1234' COMMENT '密码',
  `login_ip` varchar(36) DEFAULT NULL COMMENT '最后登录IP',
  `login_time` int(11) unsigned DEFAULT NULL COMMENT '最后登录时间',
  `create_user` int(10) unsigned NOT NULL COMMENT '创建人',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态 是否启用',
  `avatar` varchar(100) DEFAULT '0' COMMENT '头像',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'dragon', 'dragon Jia', '15988805856', 'jialongfeicn@gmail.com', '64e702d799692a0d0029c3fa7a299da8', '127.0.0.1', '1533085956', '1', '1531720576', '1', './Uploads/img/2018-07-31/5b602e92ab05e.jpg');

-- ----------------------------
-- Table structure for `user_role`
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `role_id` int(10) unsigned NOT NULL COMMENT '角色ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户,角色关联表';

-- ----------------------------
-- Records of user_role
-- ----------------------------
INSERT INTO `user_role` VALUES ('1', '5');
